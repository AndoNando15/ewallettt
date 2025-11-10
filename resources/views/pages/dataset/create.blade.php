@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h4 class="m-0 font-weight-bold text-primary">Create New Dataset</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('dataset.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_platform_e_wallet">Nama Platform E-Wallet</label>
                        <input type="text" class="form-control" id="nama_platform_e_wallet" name="nama_platform_e_wallet"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="VTP">VTP</label>
                        <input type="text" class="form-control" id="VTP" name="VTP" required>
                    </div>

                    <div class="form-group">
                        <label for="NTP">NTP</label>
                        <input type="text" class="form-control" id="NTP" name="NTP" required>
                    </div>

                    <div class="form-group">
                        <label for="PPE">PPE</label>
                        <input type="text" class="form-control" id="PPE" name="PPE" required>
                    </div>

                    <div class="form-group">
                        <label for="FPE">FPE</label>
                        <input type="text" class="form-control" id="FPE" name="FPE" required>
                    </div>

                    <div class="form-group">
                        <label for="PSD">PSD</label>
                        <input type="text" class="form-control" id="PSD" name="PSD" required>
                    </div>

                    <div class="form-group">
                        <label for="IPE">IPE</label>
                        <input type="text" class="form-control" id="IPE" name="IPE" required>
                    </div>

                    <div class="form-group">
                        <label for="PKP">PKP</label>
                        <input type="text" class="form-control" id="PKP" name="PKP" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Dataset</button>
                    <a href="{{ route('dataset.index') }}" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
@endsection
