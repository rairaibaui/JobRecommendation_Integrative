@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Upload Business Permit</div>

                <div class="card-body">
                    @if(session('permit_verification_status'))
                        <div class="alert alert-info">
                            <strong>{{ session('permit_verification_status') }}</strong>
                            <div>{{ session('permit_verification_message') }}</div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('permit.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="permit">Select permit (PDF only, max 5MB)</label>
                            <input id="permit" type="file" name="permit" accept="application/pdf" class="form-control-file" required>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" type="submit">Upload Permit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
