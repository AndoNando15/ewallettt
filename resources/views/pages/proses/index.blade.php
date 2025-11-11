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
                                    <th>#</th>
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
                            <h5 class="mb-3">Iterasi {{ $iterationIndex + 1 }}</h5>

                            {{-- Tabel Perhitungan Jarak Euclidean per Iterasi --}}
                            <h6>Perhitungan Jarak Euclidean</h6>
                            @if (isset($allDistancesPerIteration[$iterationIndex]))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Nama Platform E-Wallet</th>
                                            @for ($i = 1; $i <= $selectedCluster; $i++)
                                                <th>Jarak ke C{{ $i }}</th>
                                            @endfor
                                            <th>Cluster Terdekat</th>
                                            <th>Jarak Terdekat</th>
                                            <th>Jarak Terdekat ^2</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allDistancesPerIteration[$iterationIndex] as $i => $row)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $row['dataset']->id }}</td>
                                                <td>{{ $row['dataset']->nama_platform_e_wallet }}</td>
                                                @foreach ($row['distances'] as $d)
                                                    <td>{{ number_format($d, 4) }}</td>
                                                @endforeach
                                                <td><span class="badge badge-primary">C{{ $row['nearest'] }}</span></td>
                                                <td>{{ number_format($row['dmin'], 4) }}</td>
                                                <td>{{ number_format($row['dminSquared'], 4) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            {{-- Tabel Hasil Cluster dan Nama Platform E-Wallet per Iterasi --}}
                            <h6>Hasil Cluster dan Nama Platform E-Wallet</h6>
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
                                                <td>C{{ $result['cluster'] }}</td>
                                                <td>{{ $result['platforms'] ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            {{-- Tabel Hasil Iterasi --}}
                            <h6>Hasil Iterasi</h6>
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
                                            <td>C{{ $index + 1 }}</td>
                                            @foreach ($features as $f)
                                                <td>{{ number_format($centroid[$f] ?? 0, 2) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                    <div class="mt-4">
                        <h5 class="mb-3">Centroid Akhir (Konvergen)</h5>
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
                                @foreach ($newCentroids as $index => $centroid)
                                    <tr>
                                        <td>C{{ $index + 1 }}</td>
                                        @foreach ($features as $f)
                                            <td>{{ number_format($centroid[$f] ?? 0, 2) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                {{-- Rata-rata Centroid --}}
                @if (!empty($centroidAverages))
                    <div class="mt-4">
                        <h5 class="mb-3">Rata-rata Centroid</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Centroid</th>
                                    <th>Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($centroidAverages as $clusterName => $average)
                                    <tr>
                                        <td>{{ $clusterName }}</td>
                                        <td>{{ number_format($average, 4) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif


                {{-- SSE per Iterasi --}}
                @if (!empty($allSSEPerIteration))
                    <div class="mt-4">
                        <h5 class="mb-3">SSE per Iterasi</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Iterasi</th>
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

                        {{-- Total SSE --}}
                        <h6 class="mt-4"><strong>Total SSE:</strong> {{ number_format($totalSSE, 4) }}</h6>
                    </div>
                @endif
                {{-- SSE per Iterasi --}}
                @if (!empty($allSSEPerIteration))
                    <div class="mt-4">
                        <h5 class="mb-3">SSE per Iterasi</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Iterasi</th>
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
                        {{-- Total SSE --}}
                        <h6 class="mt-4"><strong>Total SSE:</strong> {{ number_format($totalSSE, 4) }}</h6>
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
