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

                    <!-- Permissions Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>Sr. No</th>
                                    <th>Permission Name</th>
                                    <th>Guard</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permissions as $key => $permission)
                                    <tr class="text-dark">
                                        <td class="text-dark fw-medium">{{ $key + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ $permission->name }}</td>
                                        <td>
                                            <span class="badge bg-label-info text-dark border fw-bold">{{ $permission->guard_name ?? 'web' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="table-actions">
                                                {{-- Edit --}}
                                                @can('permission edit')
                                                    <button type="button" title="Edit"
                                                        class="action-btn action-btn-edit edit-permission-btn"
                                                        data-id="{{ $permission->id }}"
                                                        data-name="{{ $permission->name }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#permissionModal">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete --}}
                                                @can('permission delete')
                                                    <a href="{{ route('permissions.delete', $permission->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this permission?')"
                                                        class="action-btn action-btn-delete">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No permissions available.</td>
                                    </tr>
                                @endforelse
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
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

            // Edit Permission
            document.querySelectorAll('.edit-permission-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;

                    form.action = `/permissions/update/${id}`;
                    modalTitle.textContent = 'Edit Permission';
                    submitBtn.textContent = 'Update Permission';
                    nameInput.value = name;
                });
            });


        });
    </script>
@endsection
