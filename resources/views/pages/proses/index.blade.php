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
                                    <th class="text-center">No</th>
                                    <th class="text-center">ID</th>

                                    {{-- Header Nama Platform E-Wallet tetap rata kiri --}}
                                    <th class="text-start">Nama Platform E-Wallet</th>

                                    <th class="text-center">VTP</th>
                                    <th class="text-center">NTP</th>
                                    <th class="text-center">PPE</th>
                                    <th class="text-center">FPE</th>
                                    <th class="text-center">PSD</th>
                                    <th class="text-center">IPE</th>
                                    <th class="text-center">PKP</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($selectedDatasets as $row)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $row->id }}</td>

                                        {{-- Isi tetap rata kiri --}}
                                        <td class="text-start">{{ $row->nama_platform_e_wallet }}</td>

                                        <td class="text-center">{{ $row->VTP }}</td>
                                        <td class="text-center">{{ $row->NTP }}</td>
                                        <td class="text-center">{{ $row->PPE }}</td>
                                        <td class="text-center">{{ $row->FPE }}</td>
                                        <td class="text-center">{{ $row->PSD }}</td>
                                        <td class="text-center">{{ $row->IPE }}</td>
                                        <td class="text-center">{{ $row->PKP }}</td>
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
                                            <th class="text-center">No</th>
                                            {{-- <th>ID</th> --}}

                                            {{-- Header Nama Platform tetap kiri --}}
                                            <th class="text-start">Nama Platform E-Wallet</th>

                                            @for ($i = 1; $i <= $selectedCluster; $i++)
                                                <th class="text-center">Jarak ke C{{ $i }}</th>
                                            @endfor

                                            <th class="text-center">Jarak Terdekat</th>
                                            <th class="text-center">Cluster Terdekat</th>
                                            <th class="text-center">Perubahan</th>
                                            <th class="text-center">Jarak Terdekat ^2</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($allDistancesPerIteration[$iterationIndex] as $i => $row)
                                            <tr>
                                                <td class="text-center">{{ $i + 1 }}</td>

                                                {{-- Isi Nama Platform E-Wallet tetap kiri --}}
                                                <td class="text-start">{{ $row['dataset']->nama_platform_e_wallet }}</td>

                                                @foreach ($row['distances'] as $d)
                                                    <td class="text-center">{{ number_format($d, 4) }}</td>
                                                @endforeach

                                                <td class="text-center">{{ number_format($row['dmin'], 4) }}</td>

                                                <td class="text-center">
                                                    @php
                                                        $color = match ($row['nearest']) {
                                                            1 => 'primary',
                                                            2 => 'warning',
                                                            3 => 'success',
                                                            4 => 'danger',
                                                            5 => 'info',
                                                            default => 'secondary',
                                                        };
                                                    @endphp
                                                    <span
                                                        class="badge badge-{{ $color }}">C{{ $row['nearest'] }}</span>
                                                </td>

                                                <td class="text-center">
                                                    @if ($row['changed'] === 'Iya')
                                                        <span class="badge badge-danger">Iya</span>
                                                    @else
                                                        <span class="badge badge-success">Tidak</span>
                                                    @endif
                                                </td>

                                                <td class="text-center">{{ number_format($row['dminSquared'], 4) }}</td>
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
                                            <th class="text-center">Cluster</th>
                                            <th>Nama Platform E-Wallet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allClusterResultsPerIteration[$iterationIndex] as $result)
                                            <tr>
                                                <td class="text-center">
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
                            <table class="table table-bordered text-center">
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

                {{-- 
                <div class="mt-3">
                    <div class="d-flex justify-content-start align-items-center p-2 border rounded bg-light">
                        <div>
                       
                            <div class="font-weight-semibold mr-2">
                                <Strong> Total SSE (Sum of Squared Errors) :</Strong>
                            </div>
                        </div>

                        <span class="badge badge-primary">
                            <strong>{{ number_format($sseTotal, 4) }}</strong>
                        </span>
                    </div>
                </div> --}}
                @if (!empty($newCentroids))
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-center">Hasil Konvergen</h4>
                        </div>
                        <div class="m-4">
                            {{-- Centroid akhir (konvergen) --}}
                            @if (!empty($newCentroids))
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card shadow-sm">
                                            <div class="card-header">
                                                <h5 class="mb-0">Centroid Akhir (Konvergen)</h5>
                                            </div>

                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table
                                                        class="table table-sm table-bordered text-center align-middle mb-0">
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
                                        <div class="col-md-4 mb-2"> {{-- 2 kolom per baris pada layar medium ke atas --}}
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
                                                                    <th class=" text-center">No</th>
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
                                                                            <td class=" text-center">
                                                                                {{ $loop->iteration }}
                                                                            </td>
                                                                            {{-- Menampilkan nomor urut yang benar --}}
                                                                            <td>{{ trim($platform) }}</td>
                                                                            {{-- Menampilkan nama platform setelah di-trim --}}
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
                        </div>
                    </div>
                @endif


                @if (!empty($newCentroids))
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-center">Hasil Rata-Rata</h4>
                        </div>
                        {{-- SECTION METRIK CLUSTERING --}}
                        <div class="row m-2">

                            {{-- Rata-rata Centroid --}}
                            @if (!empty($centroidAverages))
                                <div class="col-lg-6 mb-2 mt-3">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header">
                                            <h5 class="mb-0">Rata-rata Centroid</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class=" text-center" style="width: 30%">Centroid</th>
                                                            <th>Rata-rata</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($centroidAverages as $clusterName => $average)
                                                            <tr>
                                                                <td class=" text-center">
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
                                <div class="col-lg-6 mb-4 mt-3">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">SSE per Iterasi</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class=" text-center" style="width: 40%">Iterasi</th>
                                                            <th class=" text-center">SSE</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($allSSEPerIteration as $iterationIndex => $sse)
                                                            <tr>
                                                                <td class=" text-center">Iterasi {{ $iterationIndex + 1 }}
                                                                </td>
                                                                <td class=" text-center">{{ number_format($sse, 4) }}</td>
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
                                                            Total SSE :
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



                        <div class="row m-2">

                            {{-- SSW --}}
                            @if (!empty($centroidSum))
                                <div class="col-lg-6 mb-2 mt-3">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header">
                                            <h5 class="mb-0">SSW per Cluster</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class=" text-center" style="width: 30%">Cluster</th>
                                                            <th class=" text-center">Nilai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($centroidSum as $clusterName => $sum)
                                                            <tr>
                                                                <td class=" text-center">
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
                                                                </td class=" text-center">
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
                                <div class="col-lg-6 mb-2 mt-3">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header">
                                            <h5 class="mb-0">Davies-Bouldin Index per Centroid</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered text-center align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class=" text-center" style="width: 30%">Pasangan Centroid
                                                            </th>
                                                            <th class=" text-center">Jarak Euclidean</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($dbiPerCentroid as $row)
                                                            <tr>
                                                                <td class=" text-center">
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
                                                                <td class=" text-center">
                                                                    {{ number_format($row['euclidean'], 4, ',', '.') }}
                                                                </td>
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
                            @if (!empty($centroidSum) && !empty($dbiPerCentroid))
                                <div class="col-lg-6 mb-3 mt-3">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header">
                                            <h5 class="mb-0">Tabel Perhitungan R</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">

                                                @php
                                                    $totalClusters = count($centroidSum);
                                                    $rValues = [];
                                                    $distanceMap = [];

                                                    // Normalisasi pasangan jarak
                                                    foreach ($dbiPerCentroid as $row) {
                                                        if (empty($row['pair'])) {
                                                            continue;
                                                        }
                                                        $key = strtoupper(
                                                            str_replace(['–', '—', ' '], ['-', '-', ''], $row['pair']),
                                                        );
                                                        $distanceMap[$key] = (float) $row['euclidean'];
                                                    }

                                                    $getDistance = function ($a, $b) use ($distanceMap) {
                                                        $p1 = strtoupper("$a-$b");
                                                        $p2 = strtoupper("$b-$a");
                                                        return $distanceMap[$p1] ?? ($distanceMap[$p2] ?? null);
                                                    };
                                                @endphp

                                                <table class="table table-bordered text-center">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Centroid</th>
                                                            <th>Nilai R</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach ($centroidSum as $c => $ssw)
                                                            @php
                                                                $denominator = 0;

                                                                foreach ($centroidSum as $c2 => $ssw2) {
                                                                    if ($c !== $c2) {
                                                                        $dist = $getDistance($c, $c2);
                                                                        if ($dist !== null && $dist > 0) {
                                                                            $denominator += $dist;
                                                                        }
                                                                    }
                                                                }

                                                                if ($denominator > 0) {
                                                                    $r = $ssw / $denominator;
                                                                    $rValues[] = $r;
                                                                } else {
                                                                    $r = 'N/A';
                                                                }
                                                            @endphp

                                                            <tr>
                                                                <td>{{ $c }}</td>
                                                                <td class="text-end">
                                                                    {{ is_numeric($r) ? number_format($r, 9) : $r }}</td>
                                                            </tr>
                                                        @endforeach

                                                        @php
                                                            $totalR =
                                                                count($rValues) > 0
                                                                    ? array_sum($rValues) / count($rValues)
                                                                    : 0;
                                                        @endphp

                                                    </tbody>
                                                </table>

                                                <div class="mt-3">
                                                    <div class="p-2 border rounded bg-light d-inline-block">
                                                        <span class="font-weight-semibold">Total R (DBI):</span>
                                                        <span class="badge badge-primary">
                                                            {{ number_format($totalR, 9) }}
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
                @endif




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
