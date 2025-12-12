@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Row statistik singkat -->
        <div class="row">
            {{-- Card Total Dataset --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Dataset
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalDataset ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-database fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kalau nanti mau tambah card lain, bisa di sini --}}
            {{-- 
            <div class="col-xl-3 col-md-6 mb-4">
                ...
            </div>
            --}}
        </div>

        <!-- Penjelasan metode K-Means -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Penjelasan Singkat Metode K-Means Clustering
                        </h6>
                    </div>
                    <div class="card-body">
                        <p>
                            Aplikasi ini menggunakan <strong>metode K-Means Clustering</strong> untuk
                            mengelompokkan platform e-wallet berdasarkan beberapa variabel penilaian,
                            seperti <strong>VTP, NTP, PPE, FPE, PSD, IPE, dan PKP</strong>.
                        </p>

                        <p>
                            Secara sederhana, K-Means akan membagi data ke dalam
                            <strong>K buah cluster</strong> (misalnya 2, 3, 4, atau 5 cluster). Setiap cluster
                            memiliki sebuah <em>centroid</em> (titik pusat) yang mewakili karakteristik rata-rata
                            data dalam cluster tersebut.
                        </p>

                        <p class="mb-2"><strong>Alur utama metode K-Means:</strong></p>
                        <ol>
                            <li>Pilih jumlah cluster (<strong>K</strong>), misalnya 2–5 cluster.</li>
                            <li>Pilih centroid awal dari beberapa data yang dipilih sebagai titik awal.</li>
                            <li>Hitung <strong>jarak Euclidean</strong> tiap data ke setiap centroid.</li>
                            <li>Setiap data dimasukkan ke cluster dengan jarak paling dekat (cluster terdekat).</li>
                            <li>Update centroid dengan menghitung rata-rata nilai semua data di dalam tiap cluster.</li>
                            <li>Ulangi proses perhitungan jarak dan update centroid sampai <strong>konvergen</strong>
                                (centroid sudah tidak banyak berubah).</li>
                        </ol>

                        <p>
                            Pada halaman <strong>Proses K-Means</strong>, kamu dapat melihat:
                        </p>
                        <ul>
                            <li><strong>Centroid awal</strong> berdasarkan dataset yang dipilih.</li>
                            <li><strong>Perhitungan jarak Euclidean</strong> tiap iterasi untuk setiap data ke masing-masing
                                centroid.</li>
                            <li><strong>Cluster terdekat</strong> yang menunjukkan data masuk ke cluster mana.</li>
                            <li><strong>SSE (Sum of Squared Errors)</strong> per iterasi dan totalnya, yang menggambarkan
                                seberapa rapat data terhadap centroid cluster-nya.</li>
                            <li><strong>DBI (Davies-Bouldin Index)</strong> dan nilai-nilai lain yang membantu
                                mengevaluasi kualitas hasil clustering.</li>
                        </ul>

                        <p class="mb-0">
                            Semakin kecil nilai <strong>SSE</strong> dan semakin baik nilai <strong>DBI</strong>,
                            biasanya menunjukkan bahwa pembentukan cluster semakin baik, yaitu data dalam satu cluster
                            cenderung mirip dan antar cluster semakin berbeda.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
