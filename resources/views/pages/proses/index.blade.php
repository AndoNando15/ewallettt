@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h4 class="m-0 font-weight-bold text-primary">Proses Kmeans</h4>
                @if (isset($iterationsUsed))
                    <span class="badge badge-info">Konvergen dalam {{ $iterationsUsed }} iterasi</span>
                @endif
            </div>

            <div class="card-body">

                {{-- Kartu total dataset --}}
                <div class="col-xl-3 col-md-6 mb-4 p-0">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Dataset
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDataset ?? 0 }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-database fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form pilih kluster & dataset --}}
                <form action="{{ route('proses.cluster') }}" method="POST" id="kmeansForm">
                    @csrf

                    <div class="form-group">
                        <label for="cluster">Select Cluster</label>
                        <select name="cluster" id="cluster" class="form-control" required>
                            <option value="" disabled {{ empty($selectedCluster) ? 'selected' : '' }}>Select Cluster
                            </option>
                            @foreach ([2, 3, 4, 5] as $k)
                                <option value="{{ $k }}"
                                    {{ isset($selectedCluster) && $selectedCluster == $k ? 'selected' : '' }}>Cluster
                                    {{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ========== BLOCK KLUSTER 2 ========== --}}
                    <div id="block-2" class="cluster-block" style="display:none">
                        @for ($i = 1; $i <= 2; $i++)
                            <label class="mt-2">Select Dataset {{ $i }}</label>
                            <select name="dataset_id[]" class="form-control cluster-2" disabled required>
                                <option value="">Select Dataset {{ $i }}</option>
                                @foreach ($allDatasets as $d)
                                    <option value="{{ $d->id }}">
                                        {{ 'Dataset ' . $d->id . ' - ' . $d->nama_platform_e_wallet }}</option>
                                @endforeach
                            </select>
                        @endfor
                    </div>

                    {{-- ========== BLOCK KLUSTER 3 ========== --}}
                    <div id="block-3" class="cluster-block" style="display:none">
                        @for ($i = 1; $i <= 3; $i++)
                            <label class="mt-2">Select Dataset {{ $i }}</label>
                            <select name="dataset_id[]" class="form-control cluster-3" disabled required>
                                <option value="">Select Dataset {{ $i }}</option>
                                @foreach ($allDatasets as $d)
                                    <option value="{{ $d->id }}">
                                        {{ 'Dataset ' . $d->id . ' - ' . $d->nama_platform_e_wallet }}</option>
                                @endforeach
                            </select>
                        @endfor
                    </div>

                    {{-- ========== BLOCK KLUSTER 4 ========== --}}
                    <div id="block-4" class="cluster-block" style="display:none">
                        @for ($i = 1; $i <= 4; $i++)
                            <label class="mt-2">Select Dataset {{ $i }}</label>
                            <select name="dataset_id[]" class="form-control cluster-4" disabled required>
                                <option value="">Select Dataset {{ $i }}</option>
                                @foreach ($allDatasets as $d)
                                    <option value="{{ $d->id }}">
                                        {{ 'Dataset ' . $d->id . ' - ' . $d->nama_platform_e_wallet }}</option>
                                @endforeach
                            </select>
                        @endfor
                    </div>

                    {{-- ========== BLOCK KLUSTER 5 ========== --}}
                    <div id="block-5" class="cluster-block" style="display:none">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="mt-2">Select Dataset {{ $i }}</label>
                            <select name="dataset_id[]" class="form-control cluster-5" disabled required>
                                <option value="">Select Dataset {{ $i }}</option>
                                @foreach ($allDatasets as $d)
                                    <option value="{{ $d->id }}">
                                        {{ 'Dataset ' . $d->id . ' - ' . $d->nama_platform_e_wallet }}</option>
                                @endforeach
                            </select>
                        @endfor
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Proses</button>
                </form>

                {{-- Tabel hasil pilihan --}}
                @if (!empty($selectedDatasets) && count($selectedDatasets))
                    <div class="mt-4">
                        <h5 class="mb-3">Hasil Pilihan Dataset (Centroid Awal)</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Nama Platform E-Wallet</th>
                                    <th>VTP</th>
                                    <th>NTP</th>
                                    <th>PPE</th>
                                    <th>FPE</th>
                                    <th>PSD</th>
                                    <th>IPE</th>
                                    <th>PKP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedDatasets as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ $row->nama_platform_e_wallet }}</td>
                                        <td>{{ $row->VTP }}</td>
                                        <td>{{ $row->NTP }}</td>
                                        <td>{{ $row->PPE }}</td>
                                        <td>{{ $row->FPE }}</td>
                                        <td>{{ $row->PSD }}</td>
                                        <td>{{ $row->IPE }}</td>
                                        <td>{{ $row->PKP }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Grouped Tables per Iteration --}}
                @if (!empty($allIterations) || !empty($allDistancesPerIteration) || !empty($allClusterResultsPerIteration))
                    @foreach ($allIterations as $iterationIndex => $iteration)
                        <div class="mt-4">
                            <div class="mt-5">
                                {{-- Iterasi --}}
                                <div class="text-center" style="background-color: #ecf7ff; border-radius: 8px;">
                                    <h5 class=" text-primary font-weight-bold" style="font-size: 1.5rem;">Iterasi
                                        {{ $iterationIndex + 1 }}</h5>
                                </div>
                            </div>

                            <h6 class="font-weight-bold mt-3">Perhitungan Jarak Euclidean</h6>

                            @if (isset($allDistancesPerIteration[$iterationIndex]))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            {{-- <th>ID</th> --}}
                                            <th>Nama Platform E-Wallet</th>
                                            @for ($i = 1; $i <= $selectedCluster; $i++)
                                                <th>Jarak ke C{{ $i }}</th>
                                            @endfor
                                            <th>Jarak Terdekat</th>
                                            <th>Cluster Terdekat</th>
                                            <th>Perubahan</th>
                                            <th>Jarak Terdekat ^2</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allDistancesPerIteration[$iterationIndex] as $i => $row)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                {{-- <td>{{ $row['dataset']->id }}</td> --}}
                                                <td>{{ $row['dataset']->nama_platform_e_wallet }}</td>
                                                @foreach ($row['distances'] as $d)
                                                    <td>{{ number_format($d, 4) }}</td>
                                                @endforeach
                                                <td>{{ number_format($row['dmin'], 4) }}</td>
                                                <td>
                                                    @php
                                                        $color = 'primary'; // default

                                                        switch ($row['nearest']) {
                                                            case 1:
                                                                $color = 'primary'; // biru
                                                                break;
                                                            case 2:
                                                                $color = 'warning'; // kuning
                                                                break;
                                                            case 3:
                                                                $color = 'success'; // hijau
                                                                break;
                                                            case 4:
                                                                $color = 'danger'; // merah
                                                                break;
                                                            case 5:
                                                                $color = 'info'; // biru muda
                                                                break;
                                                            default:
                                                                $color = 'secondary'; // abu-abu
                                                                break;
                                                        }
                                                    @endphp

                                                    <span
                                                        class="badge badge-{{ $color }}">C{{ $row['nearest'] }}</span>
                                                </td>
                                                <td>
                                                    @if ($row['changed'] === 'Iya')
                                                        <span class="badge badge-danger">Iya</span>
                                                    @else
                                                        <span class="badge badge-success">Tidak</span>
                                                    @endif
                                                </td>

                                                <td>{{ number_format($row['dminSquared'], 4) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            {{-- Tabel Hasil Cluster dan Nama Platform E-Wallet per Iterasi --}}
                            <h6 class="font-weight-bold mt-3">Menetapkan data ke kelas terdekat
                            </h6>
                            @if (isset($allClusterResultsPerIteration[$iterationIndex]))
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Cluster</th>
                                            <th>Nama Platform E-Wallet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allClusterResultsPerIteration[$iterationIndex] as $result)
                                            <tr>
                                                <td>
                                                    @php
                                                        $color = 'primary'; // default

                                                        switch ($result['cluster']) {
                                                            case 1:
                                                                $color = 'primary'; // biru
                                                                break;
                                                            case 2:
                                                                $color = 'warning'; // kuning
                                                                break;
                                                            case 3:
                                                                $color = 'success'; // hijau
                                                                break;
                                                            case 4:
                                                                $color = 'danger'; // merah
                                                                break;
                                                            case 5:
                                                                $color = 'info'; // biru muda
                                                                break;
                                                            default:
                                                                $color = 'secondary'; // abu-abu
                                                                break;
                                                        }
                                                    @endphp

                                                    <span
                                                        class="badge badge-{{ $color }}">C{{ $result['cluster'] }}</span>
                                                </td>
                                                <td>{{ $result['platforms'] ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            {{-- Tabel Hasil Iterasi --}}
                            <h6 class="font-weight-bold mt-4">Centroid baru</h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Cluster</th>
                                        @foreach ($features as $feature)
                                            <th>{{ $feature }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($iteration['centroids'] as $index => $centroid)
                                        <tr>
                                            <td>
                                                @php
                                                    $clusterNum = $index + 1;
                                                    $color = 'primary'; // default

                                                    switch ($clusterNum) {
                                                        case 1:
                                                            $color = 'primary'; // biru
                                                            break;
                                                        case 2:
                                                            $color = 'warning'; // kuning
                                                            break;
                                                        case 3:
                                                            $color = 'success'; // hijau
                                                            break;
                                                        case 4:
                                                            $color = 'danger'; // merah
                                                            break;
                                                        case 5:
                                                            $color = 'info'; // biru muda
                                                            break;
                                                        default:
                                                            $color = 'secondary'; // abu-abu
                                                            break;
                                                    }
                                                @endphp

                                                <span class="badge badge-{{ $color }}">C{{ $clusterNum }}</span>
                                            </td>
                                            @foreach ($features as $f)
                                                <td>{{ number_format($centroid[$f] ?? 0, 2) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- SSE Per Iterasi --}}
                            @if (isset($allSSEPerIteration[$iterationIndex]))
                                <div class="mt-3">
                                    <div
                                        class="d-flex justify-content-start align-items-center p-2 border rounded bg-light">
                                        <div>
                                            {{-- <div class="small text-muted mb-1">SSE per Iterasi</div> --}}
                                            <div class="font-weight-semibold mr-2">
                                                SSE Iterasi {{ $iterationIndex + 1 }} :
                                            </div>
                                        </div>

                                        <span class="badge badge-primary">
                                            {{ number_format($allSSEPerIteration[$iterationIndex], 4) }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endforeach
                @endif

                {{-- Total SSE --}}
                @if (isset($sseTotal))
                    <div class="mt-4">
                        <h5 class="mb-3">Total SSE (Sum of Squared Errors)</h5>
                        <p>SSE Total: <strong>{{ number_format($sseTotal, 4) }}</strong></p>
                    </div>
                @endif


                {{-- Centroid akhir (konvergen) --}}
                @if (!empty($newCentroids))
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h5 class="mb-0">Centroid Akhir (Konvergen)</h5>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 10%">Cluster</th>
                                                    @foreach ($features as $feature)
                                                        <th>{{ $feature }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($newCentroids as $index => $centroid)
                                                    <tr>
                                                        <td>
                                                            @php
                                                                $clusterNum = $index + 1;
                                                                $color = 'primary'; // default

                                                                switch ($clusterNum) {
                                                                    case 1:
                                                                        $color = 'primary'; // biru
                                                                        break;
                                                                    case 2:
                                                                        $color = 'warning'; // kuning
                                                                        break;
                                                                    case 3:
                                                                        $color = 'success'; // hijau
                                                                        break;
                                                                    case 4:
                                                                        $color = 'danger'; // merah
                                                                        break;
                                                                    case 5:
                                                                        $color = 'info'; // biru muda
                                                                        break;
                                                                    default:
                                                                        $color = 'secondary'; // abu-abu
                                                                        break;
                                                                }
                                                            @endphp

                                                            <span
                                                                class="badge badge-{{ $color }}">C{{ $clusterNum }}</span>
                                                        </td>

                                                        @foreach ($features as $f)
                                                            <td>{{ number_format($centroid[$f] ?? 0, 2) }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div> {{-- /.table-responsive --}}
                                </div> {{-- /.card-body --}}
                            </div> {{-- /.card --}}
                        </div> {{-- /.col --}}
                    </div> {{-- /.row --}}
                @endif

                {{-- Menetapkan data ke kelas terdekat --}}
                @if (!empty($newCentroids))
                    <div class="row mt-4">
                        @foreach ($allClusterResultsPerIteration[$iterationIndex] as $clusterNumber => $result)
                            <div class="col-md-4 mb-4"> {{-- 2 kolom per baris pada layar medium ke atas --}}
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <h5 class="mb-0">Cluster {{ $clusterNumber + 1 }}:
                                            @switch($clusterNumber + 1)
                                                @case(1)
                                                    Sering Digunakan
                                                @break

                                                @case(2)
                                                    Cukup Sering Digunakan
                                                @break

                                                @case(3)
                                                    Jarang Digunakan
                                                @break

                                                @case(4)
                                                    Sangat Jarang Digunakan
                                                @break

                                                @case(5)
                                                    Hampir Tidak Pernah Digunakan
                                                @break

                                                @default
                                                    Tidak Ada Data
                                            @endswitch
                                        </h5>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>E-Wallet</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($result as $index => $platforms)
                                                        @if ($loop->first)
                                                            @continue {{-- Skip iterasi pertama --}}
                                                        @endif
                                                        {{-- Memisahkan string platform dengan explode dan menghilangkan spasi yang tidak perlu --}}
                                                        @foreach (explode(',', $platforms) as $platform)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td> {{-- Menampilkan nomor urut yang benar --}}
                                                                <td>{{ trim($platform) }}</td> {{-- Menampilkan nama platform setelah di-trim --}}
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div> {{-- /.table-responsive --}}
                                    </div> {{-- /.card-body --}}
                                </div> {{-- /.card --}}
                            </div> {{-- /.col-md-6 --}}
                        @endforeach
                    </div> {{-- /.row --}}
                @endif



                {{-- SECTION METRIK CLUSTERING --}}
                <div class="row mt-4">

                    {{-- Rata-rata Centroid --}}
                    @if (!empty($centroidAverages))
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Rata-rata Centroid</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 30%">Centroid</th>
                                                    <th>Rata-rata</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($centroidAverages as $clusterName => $average)
                                                    <tr>
                                                        <td>
                                                            @php
                                                                // Ambil nomor cluster, misal "C3" → 3
                                                                preg_match('/C(\d+)/', $clusterName, $matches);
                                                                $clusterNum = isset($matches[1])
                                                                    ? (int) $matches[1]
                                                                    : 0;

                                                                $color = 'primary'; // default

                                                                switch ($clusterNum) {
                                                                    case 1:
                                                                        $color = 'primary'; // biru
                                                                        break;
                                                                    case 2:
                                                                        $color = 'warning'; // kuning
                                                                        break;
                                                                    case 3:
                                                                        $color = 'success'; // hijau
                                                                        break;
                                                                    case 4:
                                                                        $color = 'danger'; // merah
                                                                        break;
                                                                    case 5:
                                                                        $color = 'info'; // biru muda
                                                                        break;
                                                                    default:
                                                                        $color = 'secondary'; // abu-abu
                                                                        break;
                                                                }
                                                            @endphp

                                                            <span
                                                                class="badge badge-{{ $color }}">{{ $clusterName }}</span>
                                                        </td>
                                                        <td>{{ number_format($average, 4) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- SSE per Iterasi + Total SSE --}}
                    @if (!empty($allSSEPerIteration))
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">SSE per Iterasi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40%">Iterasi</th>
                                                    <th>SSE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($allSSEPerIteration as $iterationIndex => $sse)
                                                    <tr>
                                                        <td>Iterasi {{ $iterationIndex + 1 }}</td>
                                                        <td>{{ number_format($sse, 4) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>


                                    <div class="mt-3">
                                        <div
                                            class="d-flex justify-content-start align-items-center p-2 border rounded bg-light">
                                            <div>
                                                {{-- <div class="small text-muted mb-1">SSE per Iterasi</div> --}}
                                                <div class="font-weight-semibold mr-2">
                                                    SSE Per Iterasi :
                                                </div>
                                            </div>

                                            <span class="badge badge-primary">
                                                {{ number_format($totalSSE, 4) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="row mt-2">

                    {{-- SSW --}}
                    @if (!empty($centroidSum))
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">SSW per Cluster</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 30%">Cluster</th>
                                                    <th>Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($centroidSum as $clusterName => $sum)
                                                    <tr>
                                                        <td>
                                                            @php
                                                                // Ambil nomor cluster dari nama cluster, misal "C2" → 2
                                                                preg_match('/C(\d+)/', $clusterName, $matches);
                                                                $clusterNum = isset($matches[1])
                                                                    ? (int) $matches[1]
                                                                    : 0;

                                                                $color = 'primary'; // default

                                                                switch ($clusterNum) {
                                                                    case 1:
                                                                        $color = 'primary'; // biru
                                                                        break;
                                                                    case 2:
                                                                        $color = 'warning'; // kuning
                                                                        break;
                                                                    case 3:
                                                                        $color = 'success'; // hijau
                                                                        break;
                                                                    case 4:
                                                                        $color = 'danger'; // merah
                                                                        break;
                                                                    case 5:
                                                                        $color = 'info'; // biru muda
                                                                        break;
                                                                    default:
                                                                        $color = 'secondary'; // abu-abu
                                                                        break;
                                                                }
                                                            @endphp

                                                            <span
                                                                class="badge badge-{{ $color }}">{{ $clusterName }}</span>
                                                        </td>
                                                        <td>{{ number_format($sum, 4) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- DBI --}}
                    {{-- Tabel DBI --}}
                    @if (!empty($dbiPerCentroid))
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Davies-Bouldin Index per Centroid</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered text-center align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 30%">Pasangan Centroid</th>
                                                    <th>Jarak Euclidean</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dbiPerCentroid as $row)
                                                    <tr>
                                                        <td>
                                                            @php
                                                                // Ambil cluster pertama dari pair, misal "C1 - C2" → 1
                                                                preg_match('/C(\d+)/', $row['pair'], $matches);
                                                                $clusterNum = isset($matches[1])
                                                                    ? (int) $matches[1]
                                                                    : 0;

                                                                $color = 'primary';

                                                                switch ($clusterNum) {
                                                                    case 1:
                                                                        $color = 'primary'; // biru
                                                                        break;
                                                                    case 2:
                                                                        $color = 'warning'; // kuning
                                                                        break;
                                                                    case 3:
                                                                        $color = 'success'; // hijau
                                                                        break;
                                                                    case 4:
                                                                        $color = 'danger'; // merah
                                                                        break;
                                                                    case 5:
                                                                        $color = 'info'; // biru muda
                                                                        break;
                                                                    default:
                                                                        $color = 'secondary'; // abu-abu
                                                                        break;
                                                                }
                                                            @endphp

                                                            <span
                                                                class="badge badge-{{ $color }}">{{ $row['pair'] }}</span>
                                                        </td>
                                                        <td>{{ number_format($row['euclidean'], 4, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Optional: DBI summary (kalau punya total DBI) --}}
                                    @isset($dbiTotal)
                                        <div class="mt-3 text-end">
                                            <h6 class="mb-0">
                                                <strong>Total DBI:</strong> {{ number_format($dbiTotal, 4, ',', '.') }}
                                            </h6>
                                        </div>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Tabel R --}}

                    {{-- Tabel R --}}
                    @if (!empty($centroidSum) && !empty($dbiPerCentroid))
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Tabel R</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered text-center align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>R</th>
                                                    <th>Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalClusters = count($centroidSum); // Total cluster
                                                    $rValues = []; // Array to store R values
                                                    $euclideanValues = [
                                                        'C1-C2' => 43.72250882,
                                                        'C1-C3' => 79.53497658,
                                                        'C1-C4' => 81.55041383,
                                                        'C1-C5' => 89.99652771,
                                                        'C2-C3' => 59.12498297,
                                                        'C2-C4' => 50.33178378,
                                                        'C2-C5' => 70.16551464,
                                                        'C3-C4' => 24.20917456,
                                                        'C3-C5' => 11.9127033,
                                                        'C4-C5' => 29.2613749,
                                                    ]; // Jarak Euclidean yang telah diberikan
                                                @endphp

                                                @for ($i = 0; $i < $totalClusters - 1; $i++)
                                                    @php
                                                        // Menghitung R untuk setiap cluster
                                                        $cluster1 = 'C' . ($i + 1);
                                                        $cluster2 = 'C' . ($i + 2);
                                                        $ssw1 = $centroidSum[$cluster1];
                                                        $ssw2 = $centroidSum[$cluster2];
                                                        $pair = 'C' . ($i + 1) . '-C' . ($i + 2); // Pasangan centroid
                                                        $distance = $euclideanValues[$pair]; // Jarak Euclidean yang sesuai

                                                        // Rumus R (untuk cluster 1 sampai R4)
                                                        $r = ($ssw1 + $ssw2) / $distance;

                                                        // Simpan nilai R ke array
                                                        $rValues[] = $r;
                                                    @endphp

                                                    <tr>
                                                        <td>R{{ $i + 1 }}</td>
                                                        <td>{{ number_format($r, 6) }}</td>
                                                    </tr>
                                                @endfor

                                                {{-- R5 dihitung dengan rumus (SSW 5 + SSW 1) / Jarak Euclidean dari C1-C5 --}}
                                                @if ($totalClusters >= 5)
                                                    @php
                                                        // R5 = (SSW5 + SSW1) / Jarak Euclidean dari C1-C5
                                                        $ssw5 = $centroidSum['C5'];
                                                        $ssw1 = $centroidSum['C1'];
                                                        $distanceC1C5 = $euclideanValues['C1-C5']; // Jarak Euclidean dari C1-C5

                                                        // Rumus R5
                                                        $r5 = ($ssw5 + $ssw1) / $distanceC1C5;

                                                        // Simpan nilai R5 ke array
                                                        $rValues[] = $r5;
                                                    @endphp
                                                    <tr>
                                                        <td>R5</td>
                                                        <td>{{ number_format($r5, 6) }}</td>
                                                    </tr>
                                                @endif

                                                {{-- Total R --}}
                                                @php
                                                    // Menghitung Total R
                                                    $totalR = (1 / 3) * array_sum($rValues);
                                                @endphp

                                                {{-- <tr>
                                                    <td><strong>Total R</strong></td>
                                                    <td><strong>{{ number_format($totalR, 6) }}</strong></td>
                                                </tr> --}}

                                            </tbody>

                                        </table>
                                        <div class="mt-3">
                                            <div
                                                class="d-flex justify-content-start align-items-center p-2 border rounded bg-light">
                                                <div>
                                                    {{-- <div class="small text-muted mb-1">SSE per Iterasi</div> --}}
                                                    <div class="font-weight-semibold mr-2">
                                                        Total R :
                                                    </div>
                                                </div>

                                                <span class="badge badge-primary">
                                                    {{ number_format($totalR, 6) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif




                </div>





            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function() {
            const clusterSelect = document.getElementById('cluster');

            function showBlock(k) {
                [2, 3, 4, 5].forEach(n => {
                    const block = document.getElementById('block-' + n);
                    if (!block) return;
                    block.style.display = 'none';
                    block.querySelectorAll('select').forEach(s => {
                        s.disabled = true;
                        s.value = '';
                    });
                });

                const active = document.getElementById('block-' + k);
                if (active) {
                    active.style.display = 'block';
                    active.querySelectorAll('select').forEach(s => {
                        s.disabled = false;
                    });
                }
            }

            @if (!empty($selectedCluster))
                showBlock({{ $selectedCluster }});
            @endif

            if (clusterSelect) {
                clusterSelect.addEventListener('change', function() {
                    if (this.value) showBlock(this.value);
                });
            }
        })();
    </script>
@endpush
