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

        // centroid = dataset yang dipilih user (ditampilkan di "Hasil Pilihan Dataset")
        $centroids = Dataset::whereIn('id', $request->dataset_id)->get();

        // fitur yang dipakai untuk jarak Euclidean (7 dimensi sesuai tabel Anda)
        $features = ['VTP', 'NTP', 'PPE', 'FPE', 'PSD', 'IPE', 'PKP'];

        // seluruh titik yang mau dihitung jaraknya
        $points = Dataset::orderBy('id')->get();

        $distanceTable = []; // untuk render tabel
        $sseTotal = 0;  // Untuk menyimpan total SSE
        $clusters = array_fill(0, $k, []);  // Array untuk menyimpan dataset berdasarkan cluster

        foreach ($points as $p) {
            $dists = [];
            // Hitung jarak ke setiap centroid
            foreach ($centroids as $c) {
                $dists[] = $this->euclidean($p, $c, $features);
            }

            // Menentukan index dari centroid yang memiliki jarak terkecil
            $minIdx = $this->argmin($dists);  // index 0..k-1

            // Tambahkan dataset ke dalam cluster yang sesuai
            $clusters[$minIdx][] = $p->id;  // Menyimpan ID dataset (bukan nama platform)

            // Tambahkan jarak terdekat kuadrat ke SSE total
            $sseTotal += $dists[$minIdx] ** 2;

            $distanceTable[] = [
                'dataset' => $p,
                'distances' => $dists,
                'nearest' => $minIdx + 1, // Menampilkan 1..k (untuk cluster)
                'dmin' => $dists[$minIdx],
                'dminSquared' => $dists[$minIdx] ** 2, // Menyimpan jarak terdekat dipangkatkan 2
            ];
        }

        // Menghitung centroid baru untuk setiap cluster
        $newCentroids = [];
        foreach ($clusters as $index => $cluster) {
            $newCentroids[] = $this->calculateCentroid($cluster, $features);
        }

        // tetap kirim list untuk form
        $totalDataset = Dataset::count();
        $allDatasets = Dataset::select('id', 'nama_platform_e_wallet')->orderBy('id')->get();

        // untuk tetap menampilkan “Hasil Pilihan Dataset”
        $selectedDatasets = $centroids;

        // Menampilkan nama platform e-wallet dalam setiap cluster
        $clusterResults = [];
        foreach ($clusters as $index => $platforms) {
            $clusterResults[] = [
                'cluster' => $index + 1,
                'platforms' => implode(', ', Dataset::whereIn('id', $platforms)->pluck('nama_platform_e_wallet')->toArray()),  // Menggabungkan nama platform dengan koma
            ];
        }

        return view('pages.proses.index', compact(
            'totalDataset',
            'allDatasets',
            'selectedDatasets',
            'distanceTable',
            'centroids',
            'features',
            'clusterResults',
            'sseTotal',  // Mengirimkan total SSE
            'newCentroids'  // Mengirimkan centroid baru
        ))->with('selectedCluster', $k);
    }

    // Fungsi untuk menghitung ulang centroid berdasarkan rata-rata fitur
    private function calculateCentroid($cluster, $features)
    {
        $centroid = [];

        foreach ($features as $feature) {
            $sum = 0;
            $count = 0;
            // Untuk setiap dataset dalam cluster
            foreach ($cluster as $datasetId) {
                $dataset = Dataset::find($datasetId);  // Ambil dataset berdasarkan ID
                if ($dataset) {
                    $sum += $dataset->$feature;
                    $count++;
                }
            }

            // Hitung rata-rata dari fitur jika ada dataset dalam cluster
            if ($count > 0) {
                $centroid[$feature] = $sum / $count;
            } else {
                $centroid[$feature] = 0;  // Set nilai 0 jika tidak ada dataset valid
            }
        }

        return (object) $centroid;  // Mengembalikan sebagai objek
    }



    // Fungsi untuk menghitung jarak Euclidean antara dataset dan centroid
    private function euclidean($point, $centroid, $features)
    {
        $distance = 0;
        foreach ($features as $feature) {
            // Menghitung kuadrat selisih nilai fitur
            $distance += pow($point->$feature - $centroid->$feature, 2);
        }
        return sqrt($distance);  // Mengembalikan akar kuadrat dari total perhitungan
    }

    // Fungsi untuk mencari indeks dengan nilai terkecil (argmin)
    private function argmin($array)
    {
        return array_keys($array, min($array))[0]; // Mengembalikan indeks dari nilai terkecil
    }



}