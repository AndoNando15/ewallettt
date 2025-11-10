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
        // simpan vektor fitur + nama untuk render dengan cepat
        $X = [];           // id => [feat => float]
        $names = [];       // id => nama_platform_e_wallet
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
        $selectedDatasets = Dataset::whereIn('id', $selectedIds)->get(); // untuk tabel “Hasil Pilihan Dataset”
        $centroids = []; // indeks 0..k-1, masing2 array fitur
        foreach ($selectedIds as $cid) {
            $centroids[] = $X[$cid]; // vektor fitur
        }

        // --- loop k-means sampai konvergen ---
        $maxIterations = 100;
        $threshold = 1e-6;

        $clustersIds = array_fill(0, $k, []); // per iterasi: array of array id
        $iterationsUsed = 0;
        $prevCentroids = $centroids;

        for ($iter = 1; $iter <= $maxIterations; $iter++) {
            $iterationsUsed = $iter;

            // 1) Assignment: tentukan cluster terdekat utk setiap point
            $clustersIds = array_fill(0, $k, []);
            foreach ($X as $pid => $vec) {
                $bestIdx = 0;
                $bestD2 = INF;
                foreach ($centroids as $idx => $cvec) {
                    $d2 = $this->squaredEuclideanVec($vec, $cvec, $features); // tanpa sqrt untuk efisien
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
                    // empty cluster -> pertahankan centroid lama
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

            // 3) Cek konvergensi (max L2 diff antar centroid)
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

        // --- hitung tabel jarak akhir (terhadap centroid konvergen) + SSE total ---
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
                'nearest' => $bestIdx + 1,     // 1..k
                'dmin' => sqrt($bestD2),
                'dminSquared' => $bestD2,
            ];
        }

        // --- hasil anggota cluster (pakai nama platform) ---
        $clusterResults = [];
        foreach ($clustersIds as $idx => $members) {
            $clusterResults[] = [
                'cluster' => $idx + 1,
                'platforms' => implode(', ', array_map(fn($id) => $names[$id], $members)),
            ];
        }

        // --- siapkan centroid akhir untuk ditampilkan ---
        $newCentroids = $centroids; // alias nama yang sudah dipakai di Blade

        // kirim lagi data agar form tetap bisa dipakai setelah submit
        $totalDataset = Dataset::count();
        $allDatasets = Dataset::select('id', 'nama_platform_e_wallet')->orderBy('id')->get();

        return view('pages.proses.index', compact(
            'totalDataset',
            'allDatasets',
            'selectedDatasets',
            'distanceTable',
            'features',
            'clusterResults',
            'sseTotal',
            'newCentroids'
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
}