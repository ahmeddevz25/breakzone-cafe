@extends('admin.layouts')
@section('title', 'Permissions Management')
@section('content')
    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">All Permissions</h5>
                        @can('permission add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#permissionModal" onclick="resetPermissionForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add Permission</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Permissions Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100 mb-0" id="permissionsTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">Sr. No</th>
                                    <th>Permission Name</th>
                                    <th>Guard</th>
                                    <th class="text-center" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Permission Modal (Add / Edit) -->
                    <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="permissionForm" action="{{ route('permissions.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title fw-bold" id="permissionModalLabel">Add New Permission</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Permission Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="permissionNameInput" class="form-control" placeholder="e.g. category add, user view" required>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="permissionSubmitBtn" class="btn btn-primary px-4">Save Permission</button>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Permissions DataTable (Server-Side AJAX)
            const dataTable = $('#permissionsTable').DataTable({
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
                    error: function(xhr, error, code) {
                        console.error('Permissions DataTable Error:', error, xhr.responseText);
                    }
                },
                dom: 'lfrtip',
                columns: [
                    { data: 'index', name: 'index', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'name', name: 'name' },
                    { data: 'guard', name: 'guard' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search permissions...",
                    lengthMenu: "Show _MENU_ entries",
                    processing: '<div class="d-flex justify-content-center align-items-center py-2 text-primary"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading permissions...</div>',
                    emptyTable: '<div class="text-center text-muted py-4"><i class="bx bx-key fs-2 d-block mb-1"></i> No permissions found</div>',
                    zeroRecords: '<div class="text-center text-muted py-4"><i class="bx bx-search-alt fs-2 d-block mb-1"></i> No matching permissions found</div>',
                    info: "Showing _START_ to _END_ of _TOTAL_ permissions",
                    infoEmpty: "Showing 0 to 0 of 0 permissions",
                    infoFiltered: "(filtered from _MAX_ total permissions)"
                }
            });

            const form = document.getElementById('permissionForm');
            const modalTitle = document.getElementById('permissionModalLabel');
            const submitBtn = document.getElementById('permissionSubmitBtn');
            const nameInput = document.getElementById('permissionNameInput');
            const storeRoute = "{{ route('permissions.store') }}";

            window.resetPermissionForm = function() {
                form.action = storeRoute;
                modalTitle.textContent = 'Add New Permission';
                submitBtn.textContent = 'Save Permission';
                form.reset();
            };

            // Delegated Edit Permission
            $(document).on('click', '.edit-permission-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                form.action = `/permissions/update/${id}`;
                modalTitle.textContent = 'Edit Permission';
                submitBtn.textContent = 'Update Permission';
                nameInput.value = name;
            });

            // Delegated AJAX Delete Permission
            $(document).on('click', '.delete-permission-ajax-btn', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const name = $(this).data('name') || 'this permission';

                Swal.fire({
                    title: 'Delete Permission?',
                    text: `Are you sure you want to delete "${name}"? You won't be able to revert this!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                _method: 'DELETE'
                            },
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            success: function(res) {
                                Swal.fire('Deleted!', res.message || 'Permission has been deleted.', 'success');
                                dataTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to delete permission.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
@endsection
