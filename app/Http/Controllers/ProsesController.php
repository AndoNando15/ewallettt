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

        // --- data points (vektor fitur + nama) ---
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
        $initialIds = $request->dataset_id;
        $selectedDatasets = Dataset::whereIn('id', $initialIds)->get(); // tampilkan "Hasil Pilihan Dataset (Centroid Awal)"
        $centroids = [];
        foreach ($initialIds as $cid) {
            $centroids[] = $X[$cid];
        }

        // --- Simpan iterasi 0 dengan centroid awal ---
        $iterationsLog = [];
        $distanceTable = $this->generateDistanceTable($X, $centroids, $features, $names); // Tabel jarak untuk iterasi 0
        $iterationsLog[] = [
            'iteration' => 0,
            'centroids' => $centroids,   // Centroid awal
            'clusters' => [],            // Tidak ada cluster di iterasi 0
            'sse' => 0,                  // SSE untuk iterasi 0
            'distanceTable' => $distanceTable, // Tabel jarak untuk iterasi 0
        ];

        // --- loop k-means + simpan snapshot setiap iterasi ---
        $maxIterations = 100;
        $threshold = 1e-6;
        $clustersIds = array_fill(0, $k, []);
        $iterationsUsed = 0;

        for ($iter = 1; $iter <= $maxIterations; $iter++) {
            $iterationsUsed = $iter;

            // 1) Assignment
            $clustersIds = array_fill(0, $k, []);
            $sseTotalIter = 0.0;

            // Hitung jarak dan tentukan cluster untuk setiap data
            foreach ($X as $pid => $vec) {
                $bestIdx = 0;
                $bestD2 = INF;
                $dList = [];

                foreach ($centroids as $idx => $cvec) {
                    $d2 = $this->squaredEuclideanVec($vec, $cvec, $features);
                    $dList[] = sqrt($d2); // Menyimpan jarak Euclidean
                    if ($d2 < $bestD2) {
                        $bestD2 = $d2;
                        $bestIdx = $idx;
                    }
                }

                $sseTotalIter += $bestD2;
                $distanceTable[] = [
                    'dataset' => (object) ['id' => $pid, 'nama_platform_e_wallet' => $names[$pid]],  // Dataset info
                    'distances' => $dList,
                    'nearest' => $bestIdx + 1,  // Menyimpan indeks cluster yang paling dekat
                    'dmin' => sqrt($bestD2),
                    'dminSquared' => $bestD2,
                ];

                $clustersIds[$bestIdx][] = $pid;
            }

            // 2) Update centroid
            $newCentroids = [];
            foreach ($clustersIds as $idx => $members) {
                if (count($members) === 0) {
                    // empty cluster -> pertahankan
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

            // 3) Simpan SNAPSHOT iterasi ini
            $clusterNames = [];
            foreach ($clustersIds as $idx => $members) {
                $clusterNames[$idx] = implode(', ', array_map(fn($id) => $names[$id], $members));
            }

            // Generate tabel jarak pada setiap iterasi
            $distanceTable = $this->generateDistanceTable($X, $newCentroids, $features, $names);

            $iterationsLog[] = [
                'iteration' => $iter,
                'centroids' => $newCentroids,   // array[cluster][fitur] => nilai
                'clusters' => $clusterNames,   // array[cluster] => "nama1, nama2, ..."
                'sse' => $sseTotalIter,
                'distanceTable' => $distanceTable, // Tabel jarak untuk iterasi ini
            ];

            // 4) Cek konvergensi
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

        // --- hasil akhir untuk tabel jarak & SSE total ---
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

        // ringkasan hasil akhir (anggota cluster)
        $finalClusters = [];
        foreach ($iterationsLog[array_key_last($iterationsLog)]['clusters'] as $idx => $namesStr) {
            $finalClusters[] = ['cluster' => $idx + 1, 'platforms' => $namesStr];
        }

        $newCentroids = $centroids;

        // kirim ke view
        $totalDataset = Dataset::count();
        $allDatasets = Dataset::select('id', 'nama_platform_e_wallet')->orderBy('id')->get();

        return view('pages.proses.index', compact(
            'totalDataset',
            'allDatasets',
            'selectedDatasets',   // centroid awal (model) untuk tabel awal
            'features',
            'distanceTable',      // jarak ke centroid akhir
            'sseTotal',           // SSE akhir
            'finalClusters',      // ringkasan akhir
            'newCentroids',       // centroid akhir
            'iterationsLog'       // riwayat semua iterasi
        ))->with('selectedCluster', $k)
            ->with('iterationsUsed', $iterationsUsed);
    }


    // helper jarak kuadrat
    private function squaredEuclideanVec(array $a, array $b, array $features): float
    {
        $sum = 0.0;
        foreach ($features as $f) {
            $d = (float) ($a[$f] ?? 0) - (float) ($b[$f] ?? 0);
            $sum += $d * $d;
        }
        return $sum;
    }

    // Generate tabel jarak untuk setiap iterasi
    private function generateDistanceTable($X, $centroids, $features, $names)
    {
        $distanceTable = [];
        foreach ($X as $pid => $vec) {
            $dList = [];
            $bestIdx = 0;
            $bestD2 = INF;
            foreach ($centroids as $idx => $cvec) {
                $d2 = $this->squaredEuclideanVec($vec, $cvec, $features);
                $dList[] = sqrt($d2); // Menyimpan jarak Euclidean
                if ($d2 < $bestD2) {
                    $bestD2 = $d2;
                    $bestIdx = $idx;
                }
            }
            $distanceTable[] = [
                'dataset' => (object) ['id' => $pid, 'nama_platform_e_wallet' => $names[$pid]],  // Dataset info
                'distances' => $dList,
                'nearest' => $bestIdx + 1,  // Menyimpan indeks cluster yang paling dekat
                'dmin' => sqrt($bestD2),
                'dminSquared' => $bestD2,
            ];
        }

        return $distanceTable;
    }





}