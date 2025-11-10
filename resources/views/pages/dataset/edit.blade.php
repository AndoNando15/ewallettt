@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h4 class="m-0 font-weight-bold text-primary">Edit Dataset</h4>
            </div>
            <div class="card-body">
                <form id="editDatasetForm" action="{{ route('dataset.update', $dataset->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nama_platform_e_wallet">Nama Platform E-Wallet</label>
                        <input type="text" class="form-control" id="nama_platform_e_wallet" name="nama_platform_e_wallet"
                            value="{{ old('nama_platform_e_wallet', $dataset->nama_platform_e_wallet) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="VTP">VTP</label>
                        <input type="text" class="form-control" id="VTP" name="VTP"
                            value="{{ old('VTP', $dataset->VTP) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="NTP">NTP</label>
                        <input type="text" class="form-control" id="NTP" name="NTP"
                            value="{{ old('NTP', $dataset->NTP) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="PPE">PPE</label>
                        <input type="text" class="form-control" id="PPE" name="PPE"
                            value="{{ old('PPE', $dataset->PPE) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="FPE">FPE</label>
                        <input type="text" class="form-control" id="FPE" name="FPE"
                            value="{{ old('FPE', $dataset->FPE) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="PSD">PSD</label>
                        <input type="text" class="form-control" id="PSD" name="PSD"
                            value="{{ old('PSD', $dataset->PSD) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="IPE">IPE</label>
                        <input type="text" class="form-control" id="IPE" name="IPE"
                            value="{{ old('IPE', $dataset->IPE) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="PKP">PKP</label>
                        <input type="text" class="form-control" id="PKP" name="PKP"
                            value="{{ old('PKP', $dataset->PKP) }}" required>
                    </div>

                    <!-- Update Button that Triggers Modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">
                        Update Dataset
                    </button>

                    <a href="{{ route('dataset.index') }}" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Confirming Update -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update this dataset's information? Please confirm your changes.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="editDatasetForm">Yes, Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection
