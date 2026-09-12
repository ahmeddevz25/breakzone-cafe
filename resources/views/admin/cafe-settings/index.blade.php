@extends('admin.layouts')
@section('title', 'Cafe Settings')
@section('content')
    @include('sweetalert::alert')

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0">Cafe Settings</h5>
                    </div>

                    <div class="card-body mt-4">
                        <form action="{{ route('cafe-settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Logo Column -->
                                <div class="col-md-4 text-center mb-4">
                                    <label class="form-label d-block">Cafe Logo</label>
                                    <div class="mb-3">
                                        @if($setting && $setting->logo)
                                            <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" class="img-thumbnail mb-2" style="max-height: 150px;">
                                        @else
                                            <div class="bg-light border rounded d-flex align-items-center justify-content-center mx-auto" style="height: 150px; width: 150px;">
                                                <i class='bx bx-image-alt fs-1 text-muted'></i>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Recommended size: 200x200px (Max 2MB)</small>
                                </div>

                                <!-- Info Column -->
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Cafe Name</label>
                                        <input type="text" name="cafe_name" class="form-control @error('cafe_name') is-invalid @enderror" 
                                               value="{{ old('cafe_name', $setting->cafe_name ?? '') }}" required>
                                        @error('cafe_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                               value="{{ old('phone_number', $setting->phone_number ?? '') }}" required>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Cafe Address</label>
                                        <textarea name="cafe_address" class="form-control @error('cafe_address') is-invalid @enderror" 
                                                  rows="3" required>{{ old('cafe_address', $setting->cafe_address ?? '') }}</textarea>
                                        @error('cafe_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary px-5">Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
