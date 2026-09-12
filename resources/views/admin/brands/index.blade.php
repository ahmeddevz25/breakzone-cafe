@extends('admin.layouts')
@section('title', 'Brands Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Brands Management</h5>
                        @can('brand add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#brandModal" onclick="resetBrandForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add New Brand</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Brands Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>#</th>
                                    <th>Brand Name</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($brands as $key => $brand)
                                    <tr class="text-dark">
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-semibold text-dark">{{ $brand->name }}</td>
                                        <td>
                                            @if ($brand->status == 'A' || $brand->status == 'active' || $brand->status === '1' || $brand->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('brand edit')
                                                    <button type="button" title="Edit"
                                                        class="btn btn-link text-primary fs-5 p-0 edit-brand-btn"
                                                        data-id="{{ $brand->id }}" 
                                                        data-name="{{ $brand->name }}" 
                                                        data-position="{{ $brand->position ?? 0 }}"
                                                        data-status="{{ $brand->status }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#brandModal">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                @can('brand delete')
                                                    <a href="{{ route('brands.delete', $brand->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this brand?')"
                                                        class="text-danger fs-5">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No brands available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Brand Modal -->
                    <div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="brandForm" action="{{ route('brands.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title" id="brandModalLabel">Add New Brand</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- Brand Name --}}
                                        <div class="mb-3">
                                            <label class="form-label">Brand Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="brandName" class="form-control" required>
                                        </div>

                                        {{-- Position --}}
                                        <div class="mb-3">
                                            <label class="form-label">Position</label>
                                            <input type="number" name="position" id="brandPosition" class="form-control" value="0">
                                        </div>

                                        {{-- Status --}}
                                        <div class="mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="brandStatus" class="form-select" required>
                                                <option value="A">Active</option>
                                                <option value="I">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Clear</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary bg-dark border-dark">Save Brand</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="layout-overlay layout-menu-toggle"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const brandForm = document.getElementById('brandForm');
            const modalTitle = document.getElementById('brandModalLabel');
            const submitBtn = document.getElementById('submitBtn');
            
            const brandName = document.getElementById('brandName');
            const brandPosition = document.getElementById('brandPosition');
            const brandStatus = document.getElementById('brandStatus');
            
            const brandRoute = "{{ route('brands.store') }}";

            window.resetBrandForm = function() {
                brandForm.action = brandRoute;
                modalTitle.textContent = 'Add New Brand';
                submitBtn.textContent = 'Save Brand';
                brandForm.reset();
                brandPosition.value = "0";
                brandStatus.value = "A"; // Default to Active (A)
            };

            document.querySelectorAll('.edit-brand-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const position = this.dataset.position;
                    let status = this.dataset.status;
                    if (status === "active" || status === "1" || status === "A") {
                        status = "A";
                    } else {
                        status = "I";
                    }

                    // Update form action for editing
                    brandForm.action = `/brands/${id}/update`;
                    modalTitle.textContent = 'Edit Brand';
                    submitBtn.textContent = 'Update Brand';
                    
                    brandName.value = name;
                    brandPosition.value = position || '0';
                    brandStatus.value = status;
                });
            });
        });
    </script>
@endsection
