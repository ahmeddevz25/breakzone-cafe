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
                                data-bs-target="#addPermissionModal">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add Permissions</span>
                            </button>
                        @endcan
                    </div>


                    <!-- Permissions Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>Sr. No</th>
                                    <th>Permissions Name</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permissions as $key => $permission)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-semibold">{{ $permission->name }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('permission edit')
                                                    <a href="{{ route('permissions.edit', $permission->id) }}" title="Edit"
                                                        class="text-primary fs-5">
                                                        <i class='bx bx-edit'></i>
                                                    </a>
                                                @endcan

                                                @can('permission delete')
                                                    <a href="{{ route('permissions.delete', $permission->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this permission?')"
                                                        class="text-danger fs-5">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No roles available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Add Permission Modal -->
                    <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('permissions.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addPermissionModalLabel">Add New Permission</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        {{-- Permission Name --}}
                                        <div class="mb-3">
                                            <label class="form-label">Permission Name</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Create Permission</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    </div>
@endsection
