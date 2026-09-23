@extends('admin.layouts')
@section('title', 'Categories Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Categories Management</h5>
                        @can('category add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#categoryModal" onclick="resetCategoryForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add New Category</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Categories Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>#</th>
                                    <th>Item Category</th>
                                    <th>Status</th>
                                    <th class="text-center">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $key => $category)
                                    <tr class="text-dark">
                                        <td class="text-dark fw-medium">{{ $key + 1 }}</td>
                                        <td class="fw-bold text-dark">{!! $category->full_path !!}</td>
                                        <td>
                                            @if ($category->status == 'A' || $category->status == 'active' || $category->status === '1' || $category->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="table-actions">
                                                {{-- Edit --}}
                                                @can('category edit')
                                                    <button type="button" title="Edit"
                                                        class="action-btn action-btn-edit edit-category-btn"
                                                        data-id="{{ $category->id }}" 
                                                        data-category="{{ $category->category ?? $category->name }}" 
                                                        data-parent_id="{{ $category->parent_id }}" 
                                                        data-position="{{ $category->position ?? 0 }}" 
                                                        data-status="{{ $category->status }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#categoryModal">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete --}}
                                                @can('category delete')
                                                    <a href="{{ route('categories.delete', $category->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this category? Note: This will delete all its sub-categories as well.')"
                                                        class="action-btn action-btn-delete">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No categories available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Category Modal (Add / Edit) -->
                    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="categoryForm" action="{{ route('categories.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title fw-bold" id="categoryModalLabel">Add New Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- Category Name --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Item Category <span class="text-danger">*</span></label>
                                            <input type="text" name="category" id="categoryName" class="form-control" placeholder="Enter Category Name" required>
                                        </div>

                                        {{-- Parent Category --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Parent Category</label>
                                            <select name="parent_id" id="categoryParent" class="form-select">
                                                <option value="">None (Top Level)</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{!! $cat->full_path !!}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Position --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Position / Order</label>
                                            <input type="number" name="position" id="categoryPosition" class="form-control" value="0">
                                        </div>

                                        {{-- Status --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="categoryStatus" class="form-select" required>
                                                <option value="A">Active</option>
                                                <option value="I">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">Save Category</button>
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
            const categoryForm = document.getElementById('categoryForm');
            const modalTitle = document.getElementById('categoryModalLabel');
            const submitBtn = document.getElementById('submitBtn');
            
            const categoryName = document.getElementById('categoryName');
            const categoryParent = document.getElementById('categoryParent');
            const categoryPosition = document.getElementById('categoryPosition');
            const categoryStatus = document.getElementById('categoryStatus');
            
            const categoryRoute = "{{ route('categories.store') }}";

            window.resetCategoryForm = function() {
                categoryForm.action = categoryRoute;
                modalTitle.textContent = 'Add New Category';
                submitBtn.textContent = 'Save Category';
                categoryForm.reset();
                categoryPosition.value = "0";
                categoryStatus.value = "A";
                
                Array.from(categoryParent.options).forEach(opt => {
                    opt.style.display = 'block';
                });
            };

            // Edit Category
            document.querySelectorAll('.edit-category-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const category = this.dataset.category;
                    const parentId = this.dataset.parent_id;
                    const position = this.dataset.position;
                    let status = this.dataset.status;
                    if (status === "active" || status === "1" || status === "A") {
                        status = "A";
                    } else {
                        status = "I";
                    }

                    categoryForm.action = `/categories/${id}/update`;
                    modalTitle.textContent = 'Edit Category';
                    submitBtn.textContent = 'Update Category';
                    
                    categoryName.value = category || '';
                    categoryParent.value = parentId || "";
                    categoryPosition.value = position || '0';
                    categoryStatus.value = status;
                    
                    Array.from(categoryParent.options).forEach(opt => {
                        if(opt.value === id) {
                            opt.style.display = 'none';
                        } else {
                            opt.style.display = 'block';
                        }
                    });
                });
            });


        });
    </script>
@endsection
