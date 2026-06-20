@extends('layouts.app')

@section('title', 'Add University')
@section('page_title', 'Add New University')
@section('page_subtitle', 'Register a new university and set up its initial points configurations')

@section('content')

<div class="glass-card p-4 col-xl-10 mx-auto mb-5">
    <h5 class="fw-bold mb-4 text-white">
        <i class="fa-solid fa-graduation-cap text-primary me-2"></i>University Master Registration
    </h5>

    <form method="POST" action="{{ route('admin.universities.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label text-secondary small fw-semibold">University Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                       placeholder="e.g. FOCUZ University" value="{{ old('name') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Code --}}
            <div class="col-md-6">
                <label class="form-label text-secondary small fw-semibold">University Code (Unique Identification)</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                       placeholder="e.g. FOCUZ" value="{{ old('code') }}" required>
                @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="col-12">
                <label class="form-label text-secondary small fw-semibold">Description</label>
                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" 
                          placeholder="Brief description about the university Counselor PMS limits...">{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Logo Upload with Live Preview --}}
            <div class="col-md-6">
                <label class="form-label text-secondary small fw-semibold">University Logo</label>
                <div class="d-flex align-items-center gap-3">
                    <div id="logoPreview" class="rounded-circle d-flex align-items-center justify-content-center border border-secondary border-opacity-25" 
                         style="width: 70px; height: 70px; background: rgba(255,255,255,0.02); overflow: hidden;">
                        <i class="fa-regular fa-image text-secondary fa-xl"></i>
                    </div>
                    <div class="flex-grow-1">
                        <input type="file" name="logo" id="logoInput" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                        <small class="text-secondary d-block mt-1">PNG, JPG or SVG formats up to 2MB.</small>
                        @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Default Theme Color --}}
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-semibold">Default Theme Color</label>
                <div class="input-group">
                    <input type="color" name="theme_color" class="form-control form-control-color border-0 p-0" 
                           style="max-width: 45px; height: 38px;" value="#8b5cf6" required>
                    <input type="text" id="themeColorHex" class="form-control font-monospace" value="#8b5cf6" disabled>
                </div>
            </div>

            {{-- Status --}}
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-semibold">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            {{-- Tier Colors Header --}}
            <div class="col-12 mt-4">
                <hr class="border-secondary border-opacity-20 my-3">
                <h6 class="fw-bold text-white mb-3">
                    <i class="fa-solid fa-palette text-info me-2"></i>Default Tier Custom Colors
                </h6>
                <div class="row g-3">
                    {{-- Review Zone --}}
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">Review Zone Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[review_zone]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="#ef4444" required>
                            <input type="text" class="form-control font-monospace small" value="#ef4444" readonly>
                        </div>
                    </div>
                    {{-- Bronze --}}
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">Bronze Tier Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[bronze]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="#b45309" required>
                            <input type="text" class="form-control font-monospace small" value="#b45309" readonly>
                        </div>
                    </div>
                    {{-- Silver --}}
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">Silver Tier Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[silver]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="#9ca3af" required>
                            <input type="text" class="form-control font-monospace small" value="#9ca3af" readonly>
                        </div>
                    </div>
                    {{-- Gold --}}
                    <div class="col-md-4 mt-md-3">
                        <label class="form-label text-secondary small">Gold Tier Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[gold]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="#f59e0b" required>
                            <input type="text" class="form-control font-monospace small" value="#f59e0b" readonly>
                        </div>
                    </div>
                    {{-- Platinum --}}
                    <div class="col-md-4 mt-md-3">
                        <label class="form-label text-secondary small">Platinum Tier Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[platinum]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="#c084fc" required>
                            <input type="text" class="form-control font-monospace small" value="#c084fc" readonly>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end gap-3 mt-5">
            <a href="{{ route('admin.universities.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
            <button type="submit" class="btn btn-primary rounded-3 px-5 fw-semibold">
                <i class="fa-solid fa-floppy-disk me-2"></i>Save University
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('logoInput').addEventListener('change', function(e) {
        const preview = document.getElementById('logoPreview');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.innerHTML = `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = `<i class="fa-regular fa-image text-secondary fa-xl"></i>`;
        }
    });

    // Dynamic hex updates
    document.querySelectorAll('input[type="color"]').forEach(picker => {
        picker.addEventListener('input', function() {
            const textInput = this.parentElement.querySelector('input[type="text"]');
            if (textInput) {
                textInput.value = this.value;
            }
            if (this.name === 'theme_color') {
                document.getElementById('themeColorHex').value = this.value;
            }
        });
    });
</script>
@endsection
