<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;

class ProsesController extends Controller
{
    public function index()
    {
        $totalDataset = Dataset::count();
        $allDatasets = Dataset::select('id', 'nama_platform_e_wallet')->orderBy('id')->get();

        return view('pages.proses.index', [
            'totalDataset' => $totalDataset,
            'allDatasets' => $allDatasets,
            'selectedDatasets' => null,
        ]);
    }
    public function process(Request $request)
    {
        $request->validate([
            'cluster' => 'required|in:2,3,4,5',
            'dataset_id' => 'required|array|size:' . $request->cluster,
            'dataset_id.*' => 'integer|distinct|exists:dataset,id',
        ]);

        $k = (int) $request->cluster;
        $features = ['VTP', 'NTP', 'PPE', 'FPE', 'PSD', 'IPE', 'PKP'];

        // --- data points (vector fitur) ---
        $points = Dataset::orderBy('id')->get();
        $X = [];
        $names = [];
        foreach ($points as $p) {
            $vec = [];
            foreach ($features as $f) {
                $vec[$f] = (float) ($p->{$f} ?? 0);
            }
            $X[$p->id] = $vec;
            $names[$p->id] = $p->nama_platform_e_wallet;
        }

        // --- inisialisasi centroid dari pilihan user ---
        $selectedIds = $request->dataset_id;
        $selectedDatasets = Dataset::whereIn('id', $selectedIds)->get();
        $centroids = [];
        foreach ($selectedIds as $cid) {
            $centroids[] = $X[$cid];
        }

        // --- loop k-means sampai konvergen ---
        $maxIterations = 100;
        $threshold = 1e-6;

        $clustersIds = array_fill(0, $k, []);
        $iterationsUsed = 0;
        $prevCentroids = $centroids;

        // Menyimpan hasil iterasi dan jarak Euclidean per iterasi
        $allIterations = [];
        $allDistancesPerIteration = [];
        $allClusterResultsPerIteration = [];
        $allSSEPerIteration = [];  // To store SSE for each iteration

        for ($iter = 1; $iter <= $maxIterations; $iter++) {
            $iterationsUsed = $iter;
            $clustersIds = array_fill(0, $k, []);

            // 1) Assignment: tentukan cluster terdekat utk setiap point
            foreach ($X as $pid => $vec) {
                $bestIdx = 0;
                $bestD2 = INF;
                foreach ($centroids as $idx => $cvec) {
                    $d2 = $this->squaredEuclideanVec($vec, $cvec, $features);
                    if ($d2 < $bestD2) {
                        $bestD2 = $d2;
                        $bestIdx = $idx;
                    }
                }
                $clustersIds[$bestIdx][] = $pid;
            }

            // 2) Update: hitung centroid baru via rata-rata fitur tiap cluster
            $newCentroids = [];
            foreach ($clustersIds as $idx => $members) {
                if (count($members) === 0) {
                    $newCentroids[$idx] = $centroids[$idx];
                    continue;
                }
                $sum = array_fill_keys($features, 0.0);
                foreach ($members as $pid) {
                    foreach ($features as $f) {
                        $sum[$f] += $X[$pid][$f];
                    }
                }
                $mean = [];
                foreach ($features as $f) {
                    $mean[$f] = $sum[$f] / count($members);
                }
                $newCentroids[$idx] = $mean;
            }

            // Save the centroid and cluster results per iteration
            $allIterations[] = [
                'iteration' => $iter,
                'centroids' => $newCentroids,
                'clusters' => $clustersIds
            ];

            // 3) Calculate SSE for this iteration
            $sseIteration = 0.0;
            $distanceTableForThisIteration = [];
            foreach ($points as $p) {
                $vec = $X[$p->id];
                $dList = [];
                $bestIdx = 0;
                $bestD2 = INF;
                foreach ($centroids as $idx => $cvec) {
                    $d2 = $this->squaredEuclideanVec($vec, $cvec, $features);
                    $d = sqrt($d2);
                    $dList[] = $d;
                    if ($d2 < $bestD2) {
                        $bestD2 = $d2;
                        $bestIdx = $idx;
                    }
                }
                $sseIteration += $bestD2;

                $distanceTableForThisIteration[] = [
                    'dataset' => $p,
                    'distances' => $dList,
                    'nearest' => $bestIdx + 1,  // Cluster start from 1
                    'dmin' => sqrt($bestD2),
                    'dminSquared' => $bestD2,
                ];
            }

            $allDistancesPerIteration[] = $distanceTableForThisIteration;
            $allSSEPerIteration[] = $sseIteration;  // Store SSE for this iteration

            // 4) Save the cluster results per iteration (platforms assigned to each cluster)
            $clusterResultsForThisIteration = [];
            foreach ($clustersIds as $idx => $members) {
                $clusterResultsForThisIteration[] = [
                    'cluster' => $idx + 1,
                    'platforms' => implode(', ', array_map(fn($id) => $names[$id], $members)),
                ];
            }
            $allClusterResultsPerIteration[] = $clusterResultsForThisIteration;

            // 5) Cek konvergensi (max L2 diff antar centroid)
            $maxShift = 0.0;
            for ($i = 0; $i < $k; $i++) {
                $shift = sqrt($this->squaredEuclideanVec($centroids[$i], $newCentroids[$i], $features));
                if ($shift > $maxShift)
                    $maxShift = $shift;
            }

            $centroids = $newCentroids;
            if ($maxShift < $threshold) {
                break;
            }
        }

        // Calculate the total SSE across all iterations
        $totalSSE = array_sum($allSSEPerIteration);

        // --- Calculate centroid averages for each cluster ---
        $centroidAverages = [];
        foreach ($newCentroids as $index => $centroid) {
            $clusterName = 'C' . ($index + 1); // C1, C2, C3, ...
            $average = array_sum($centroid) / count($centroid);  // Calculate average for each centroid
            $centroidAverages[$clusterName] = $average;
        }
        // --- Calculate centroid averages for each cluster ---
        $centroidSum = [];
        foreach ($newCentroids as $index => $centroid) {
            $clusterName = 'C' . ($index + 1); // C1, C2, C3, ...
            $sum = array_sum($centroid);  // Calculate sum for each centroid
            $centroidSum[$clusterName] = $sum;
        }


        // --- Calculate SSE total (for final results) ---
        $distanceTable = [];
        $sseTotal = 0.0;

        foreach ($points as $p) {
            $vec = $X[$p->id];
            $dList = [];
            $bestIdx = 0;
            $bestD2 = INF;
            foreach ($centroids as $idx => $cvec) {
                $d2 = $this->squaredEuclideanVec($vec, $cvec, $features);
                $d = sqrt($d2);
                $dList[] = $d;
                if ($d2 < $bestD2) {
                    $bestD2 = $d2;
                    $bestIdx = $idx;
                }
            }
            $sseTotal += $bestD2;

            $distanceTable[] = [
                'dataset' => $p,
                'distances' => $dList,
                'nearest' => $bestIdx + 1,
                'dmin' => sqrt($bestD2),
                'dminSquared' => $bestD2,
            ];
        }

        $clusterResults = [];
        foreach ($clustersIds as $idx => $members) {
            $clusterResults[] = [
                'cluster' => $idx + 1,
                'platforms' => implode(', ', array_map(fn($id) => $names[$id], $members)),
            ];
        }

        $newCentroids = $centroids;

        // --- Hitung jumlah total dataset ---
        $totalDataset = Dataset::count();

        // --- Retrieve all datasets ---
        $allDatasets = Dataset::select('id', 'nama_platform_e_wallet')->orderBy('id')->get();
        // Normalisasi centroid agar jadi array dua dimensi numerik
        $finalCentroids = array_values(array_map(function ($c) {
            // Jika elemen masih memiliki key 'centroid', ambil nilainya
            return isset($c['centroid']) ? array_values($c['centroid']) : array_values($c);
        }, $centroids));

        // Hitung DBI antar centroid
        $dbiPerCentroid = $this->calculateDBIPerCentroid($finalCentroids);
        // Return the view with the new data
        return view('pages.proses.index', compact(
            'totalDataset',
            'allDatasets',
            'selectedDatasets',
            'distanceTable',
            'features',
            'clusterResults',
            'sseTotal',
            'newCentroids',
            'centroidAverages',
            'dbiPerCentroid',
            'centroidSum',
            'allIterations',
            'allDistancesPerIteration',
            'allClusterResultsPerIteration',
            'allSSEPerIteration', // Pass allSSEPerIteration to view
            'totalSSE'  // Pass totalSSE to view
        ))->with('selectedCluster', $k)
            ->with('iterationsUsed', $iterationsUsed);
    }

    // ---------- helpers ----------
    private function squaredEuclideanVec(array $a, array $b, array $features): float
    {
        $sum = 0.0;
        foreach ($features as $f) {
            $xa = (float) ($a[$f] ?? 0);
            $xb = (float) ($b[$f] ?? 0);
            $d = $xa - $xb;
            $sum += $d * $d;
        }
        return $sum;
    }
    public function calculateDBIPerCentroid($centroids)
    {
        $result = [];
        $count = count($centroids);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $sumSq = 0;
                $dim = count($centroids[$i]);

                for ($k = 0; $k < $dim; $k++) {
                    $sumSq += pow($centroids[$i][$k] - $centroids[$j][$k], 2);
                }

                $distance = sqrt($sumSq);
                $result[] = [
                    'pair' => 'C' . ($i + 1) . ' - C' . ($j + 1),
                    'without_sqrt' => $sumSq,
                    'euclidean' => $distance,
                ];
            }
        }

        return $result;
    }

}