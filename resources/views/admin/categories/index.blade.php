@extends('admin.layouts')
@section('title', 'Categories Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Categories Management</h5>
                        <div class="d-flex align-items-center gap-2">
                            <select id="statusFilter" class="form-select form-select-sm" style="width: 140px;">
                                <option value="">All Status</option>
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                            @can('category add')
                                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#categoryModal" onclick="resetCategoryForm()">
                                    <i class="bx bx-plus icon-sm"></i>
                                    <span class="d-none d-sm-inline-block">Add New Category</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Categories Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100" id="categoriesTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Item Category</th>
                                    <th class="text-center" style="width: 120px;">Status</th>
                                    <th class="text-center" style="width: 120px;">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Category Modal (Add / Edit) -->
                    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="categoryForm" action="{{ route('categories.store') }}" method="POST" data-ajax-table="#categoriesTable">
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
                                                @if(isset($categories))
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}">{!! $cat->full_path !!}</option>
                                                    @endforeach
                                                @endif
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Server-Side Processing AJAX DataTable Setup
            const dataTable = $('#categoriesTable').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 300,
                ordering: false,
                autoWidth: false,
                pageLength: 10,
                ajax: {
                    url: window.location.href,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: function(d) {
                        d.status_filter = $('#statusFilter').val();
                    },
                    error: function(xhr, error, code) {
                        console.error('Categories DataTable Error:', error);
                    }
                },
                dom: 'lfrtip',
                columns: [
                    { data: 'index', name: 'index', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'category', name: 'category' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search categories...",
                    lengthMenu: "Show _MENU_ entries",
                    processing: '<div class="d-flex justify-content-center align-items-center py-2 text-primary"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading categories...</div>',
                    emptyTable: '<div class="text-center text-muted py-4"><i class="bx bx-category-alt fs-2 d-block mb-1"></i> No categories found</div>',
                    zeroRecords: '<div class="text-center text-muted py-4"><i class="bx bx-search-alt fs-2 d-block mb-1"></i> No matching categories found</div>',
                    info: "Showing _START_ to _END_ of _TOTAL_ categories",
                    infoEmpty: "Showing 0 to 0 of 0 categories",
                    infoFiltered: "(filtered from _MAX_ total categories)"
                }
            });

            // Trigger reload on custom status filter change
            $('#statusFilter').on('change', function() {
                dataTable.ajax.reload();
            });

            const categoryForm = document.getElementById('categoryForm');
            const categoryParent = document.getElementById('categoryParent');
            const categoryRoute = "{{ route('categories.store') }}";

            // Reset modal for Add Category
            window.resetCategoryForm = function() {
                categoryForm.action = categoryRoute;
                $('#categoryModalLabel').text('Add New Category');
                $('#submitBtn').text('Save Category');
                categoryForm.reset();
                $('#categoryPosition').val("0");
                $('#categoryStatus').val("A");
                $(categoryForm).find('.is-invalid').removeClass('is-invalid');
                $(categoryForm).find('.invalid-feedback').remove();
                
                if (categoryParent) {
                    Array.from(categoryParent.options).forEach(opt => {
                        opt.style.display = 'block';
                    });
                }
            };

            // Edit Category Handler (Delegated for AJAX-rendered rows)
            $(document).on('click', '.edit-category-btn', function() {
                var btn = $(this);
                var id = btn.data('id');
                var category = btn.data('category');
                var parentId = btn.data('parent_id');
                var position = btn.data('position');
                var status = btn.data('status');

                if (status === "active" || status === "1" || status === "A") {
                    status = "A";
                } else {
                    status = "I";
                }

                categoryForm.action = `/categories/${id}/update`;
                $('#categoryModalLabel').text('Edit Category');
                $('#submitBtn').text('Update Category');

                $('#categoryName').val(category || '');
                $('#categoryParent').val(parentId || '');
                $('#categoryPosition').val(position || '0');
                $('#categoryStatus').val(status);

                $(categoryForm).find('.is-invalid').removeClass('is-invalid');
                $(categoryForm).find('.invalid-feedback').remove();

                if (categoryParent) {
                    Array.from(categoryParent.options).forEach(opt => {
                        if (opt.value == id) {
                            opt.style.display = 'none';
                        } else {
                            opt.style.display = 'block';
                        }
                    });
                }

                var modalEl = document.getElementById('categoryModal');
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.show();
            });

            // AJAX Delete Category Handler with SweetAlert2 Confirmation
            $(document).on('click', '.delete-category-ajax-btn', function(e) {
                e.preventDefault();
                var btn = $(this);
                var url = btn.data('url');
                var categoryName = btn.data('name') || 'this category';

                Swal.fire({
                    title: 'Delete Category?',
                    text: `Are you sure you want to delete "${categoryName}"? Note: This will delete all its sub-categories as well!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: url,
                            type: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            success: function(response) {
                                Swal.close();
                                if (response.status || response.success) {
                                    toastr.success(response.message || 'Category deleted successfully.');
                                    dataTable.ajax.reload(null, false);
                                } else {
                                    toastr.error(response.message || 'Failed to delete category.');
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete category. Please try again.';
                                toastr.error(errMsg);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
