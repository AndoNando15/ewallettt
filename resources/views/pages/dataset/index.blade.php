@extends('layouts.base')

@section('content')
    <div class="container-fluid">

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h4 class="m-0 font-weight-bold text-primary">Dataset</h4>
            </div>
            <div class="card-body">
                <a href="{{ route('dataset.create') }}" class="btn btn-primary mb-3">Tambah Dataset</a>

                <!-- Import Dataset Button (Triggers Modal) -->
                <button class="btn btn-info mb-3" data-toggle="modal" data-target="#importModal">Import Dataset</button>

                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Platform E-Wallet</th>
                                <th>VTP</th>
                                <th>NTP</th>
                                <th>PPE</th>
                                <th>FPE</th>
                                <th>PSD</th>
                                <th>IPE</th>
                                <th>PKP</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($datasets as $dataset)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $dataset->nama_platform_e_wallet }}</td>
                                    <td>{{ $dataset->VTP }}</td>
                                    <td>{{ $dataset->NTP }}</td>
                                    <td>{{ $dataset->PPE }}</td>
                                    <td>{{ $dataset->FPE }}</td>
                                    <td>{{ $dataset->PSD }}</td>
                                    <td>{{ $dataset->IPE }}</td>
                                    <td>{{ $dataset->PKP }}</td>
                                    <td>
                                        <a href="{{ route('dataset.edit', $dataset->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('dataset.destroy', $dataset->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal for Importing Dataset -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Dataset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Import Form -->
                    <form action="{{ route('dataset.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Upload Excel File</label>
                            <input type="file" class="form-control" name="file" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <!-- Custom styles for this page -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('script')
    <!-- Page level plugins -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script>
        $(document).ready(function() {
            // Destroy the previous instance if any
            if ($.fn.dataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
            }

            // Initialize the DataTable with the additional option for 5 entries
            $('#dataTable').DataTable({
                "pageLength": 15, // Default number of rows per page
                "lengthMenu": [5, 10, 25, 50, 100] // Options for rows per page
            });
        });
    </script>
@endpush
