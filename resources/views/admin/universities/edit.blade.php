@extends('layouts.app')

@section('title', 'Edit University')
@section('page_title', 'Edit University Profile')
@section('page_subtitle', 'Modify university details, styling parameters, and status')

@section('content')

<div class="glass-card p-4 col-xl-10 mx-auto mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0 text-white">
            <i class="fa-regular fa-pen-to-square text-primary me-2"></i>Edit {{ $university->name }}
        </h5>
        
        <a href="{{ route('admin.universities.show', $university->id) }}" class="btn btn-sm btn-outline-secondary rounded-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Details
        </a>
    </div>

    {{-- Logo Management Sub-Section --}}
    <div class="p-3 rounded-3 mb-4 border border-secondary border-opacity-10" style="background: rgba(255,255,255,0.01);">
        <div class="row align-items-center">
            <div class="col-md-2 text-center text-md-start">
                @if($university->logo_url)
                    <img src="{{ $university->logo_url }}" 
                         alt="{{ $university->name }}" 
                         class="rounded-circle border border-secondary border-opacity-25" 
                         style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mx-auto shadow-sm" 
                         style="width: 80px; height: 80px; background: linear-gradient(135deg, {{ $university->theme_color }}, #111827); border: 1px solid rgba(255,255,255,0.1); font-size: 1.8rem;">
                        {{ $university->initials }}
                    </div>
                @endif
            </div>
            
            <div class="col-md-5 text-center text-md-start mt-3 mt-md-0">
                <h6 class="fw-bold text-white mb-1">University Logo</h6>
                <p class="text-secondary small m-0">Uploaded logos are displayed in sidebars, rosters, and profiles.</p>
            </div>
            
            <div class="col-md-5 mt-3 mt-md-0 d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
                {{-- Replace Logo --}}
                <button class="btn btn-sm btn-primary px-3 rounded-2" type="button" data-bs-toggle="modal" data-bs-target="#replaceLogoModal">
                    <i class="fa-solid fa-upload me-1"></i> Replace Logo
                </button>

                {{-- Remove Logo --}}
                @if($university->logo)
                <form action="{{ route('admin.universities.logo.remove', $university->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to remove the logo?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger px-3 rounded-2">
                        <i class="fa-regular fa-trash-can me-1"></i> Remove Logo
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Edit Form --}}
    <form method="POST" action="{{ route('admin.universities.update', $university->id) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label text-secondary small fw-semibold">University Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $university->name) }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Code --}}
            <div class="col-md-6">
                <label class="form-label text-secondary small fw-semibold">University Code</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                       value="{{ old('code', $university->code) }}" required>
                @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="col-12">
                <label class="form-label text-secondary small fw-semibold">Description</label>
                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $university->description) }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Theme Color --}}
            <div class="col-md-4">
                <label class="form-label text-secondary small fw-semibold">Default Theme Color</label>
                <div class="input-group">
                    <input type="color" name="theme_color" class="form-control form-control-color border-0 p-0" 
                           style="max-width: 45px; height: 38px;" value="{{ old('theme_color', $university->theme_color) }}" required>
                    <input type="text" id="themeColorHex" class="form-control font-monospace" value="{{ $university->theme_color }}" disabled>
                </div>
            </div>

            {{-- Status --}}
            <div class="col-md-4">
                <label class="form-label text-secondary small fw-semibold">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ old('status', $university->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $university->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            {{-- Tier Colors Header --}}
            <div class="col-12 mt-4">
                <hr class="border-secondary border-opacity-20 my-3">
                <h6 class="fw-bold text-white mb-3">
                    <i class="fa-solid fa-palette text-info me-2"></i>Default Tier Custom Colors
                </h6>
                <div class="row g-3">
                    @php
                        $tc = $university->tier_colors ?? [
                            'review_zone' => '#ef4444',
                            'bronze' => '#b45309',
                            'silver' => '#9ca3af',
                            'gold' => '#f59e0b',
                            'platinum' => '#c084fc',
                        ];
                    @endphp
                    {{-- Review Zone --}}
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">Review Zone Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[review_zone]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="{{ $tc['review_zone'] ?? '#ef4444' }}" required>
                            <input type="text" class="form-control font-monospace small" value="{{ $tc['review_zone'] ?? '#ef4444' }}" readonly>
                        </div>
                    </div>
                    {{-- Bronze --}}
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">Bronze Tier Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[bronze]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="{{ $tc['bronze'] ?? '#b45309' }}" required>
                            <input type="text" class="form-control font-monospace small" value="{{ $tc['bronze'] ?? '#b45309' }}" readonly>
                        </div>
                    </div>
                    {{-- Silver --}}
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">Silver Tier Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[silver]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="{{ $tc['silver'] ?? '#9ca3af' }}" required>
                            <input type="text" class="form-control font-monospace small" value="{{ $tc['silver'] ?? '#9ca3af' }}" readonly>
                        </div>
                    </div>
                    {{-- Gold --}}
                    <div class="col-md-4 mt-md-3">
                        <label class="form-label text-secondary small">Gold Tier Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[gold]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="{{ $tc['gold'] ?? '#f59e0b' }}" required>
                            <input type="text" class="form-control font-monospace small" value="{{ $tc['gold'] ?? '#f59e0b' }}" readonly>
                        </div>
                    </div>
                    {{-- Platinum --}}
                    <div class="col-md-4 mt-md-3">
                        <label class="form-label text-secondary small">Platinum Tier Color</label>
                        <div class="input-group">
                            <input type="color" name="tier_colors[platinum]" class="form-control form-control-color border-0 p-0" 
                                   style="max-width: 45px; height: 38px;" value="{{ $tc['platinum'] ?? '#c084fc' }}" required>
                            <input type="text" class="form-control font-monospace small" value="{{ $tc['platinum'] ?? '#c084fc' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end gap-3 mt-5">
            <a href="{{ route('admin.universities.show', $university->id) }}" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
            <button type="submit" class="btn btn-primary rounded-3 px-5 fw-semibold">
                <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
            </button>
        </div>
    </form>
</div>

{{-- MODAL: Replace Logo --}}
<div class="modal fade" id="replaceLogoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #09090e; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;">
            <div class="modal-header border-bottom border-secondary border-opacity-10 px-4 py-3">
                <h5 class="modal-title fw-bold text-white">Upload New Logo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.universities.logo.replace', $university->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Select File</label>
                        <input type="file" name="logo" class="form-control" accept="image/*" required>
                        <small class="text-secondary d-block mt-1">Accepts PNG, JPG, or SVG up to 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-10 px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Dynamic color picker text updates
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
