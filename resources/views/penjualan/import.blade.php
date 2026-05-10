@extends('layouts.app')

@section('title', 'Import Excel')
@section('page-title', 'Import Data Excel')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-upload text-primary"></i> Upload File Excel
            </div>
            <div class="card-body">
                <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" id="form-import">
                    @csrf

                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold">File Excel <span class="text-danger">*</span></label>
                        <input type="file"
                               class="form-control @error('file') is-invalid @enderror"
                               id="file"
                               name="file"
                               accept=".xlsx,.xls">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format yang diterima: <strong>.xlsx</strong> atau <strong>.xls</strong>. Maks 10 MB.</div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btn-import">
                        <i class="bi bi-upload me-1"></i> Import Sekarang
                    </button>
                </form>
            </div>
        </div>



    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('form-import').addEventListener('submit', function() {
    const btn = document.getElementById('btn-import');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
});
</script>
@endpush
