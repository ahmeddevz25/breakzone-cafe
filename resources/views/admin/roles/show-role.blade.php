@extends('admin.layouts')
@section('title', 'Roles Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">All Roles</h5>
                        @can('role add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#addRoleModal" onclick="resetRoleForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add Role</span>
                            </button>
                        @endcan
                    </div>


                    <!-- Roles Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>Sr. No</th>
                                    <th>Role Name</th>
                                    <th>Permissions</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $key => $role)
                                    <tr class="text-dark">
                                        <td class="text-dark fw-medium">{{ $key + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ $role->name }}</td>
                                        <td>
                                            @if ($role->permissions->isNotEmpty())
                                                <button type="button" 
                                                    class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 view-permissions-modal-btn fw-semibold"
                                                    data-role="{{ $role->name }}"
                                                    data-permissions='@json($role->permissions->pluck("name"))'
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewRolePermissionsModal">
                                                    <i class="bx bx-shield-quarter"></i>
                                                    <span>{{ $role->permissions->count() }} Permissions</span>
                                                    <span class="badge bg-primary text-white rounded-pill ms-1" style="font-size: 10px;">View</span>
                                                </button>
                                            @else
                                                <span class="badge bg-label-secondary text-muted">0 Permissions</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <div class="table-actions">
                                                {{-- Edit --}}
                                                @can('role edit')
                                                    <button type="button" title="Edit"
                                                        class="action-btn action-btn-edit edit-role-btn"
                                                        data-id="{{ $role->id }}" 
                                                        data-name="{{ $role->name }}" 
                                                        data-permissions="{{ $role->permissions->pluck('name')->toJson() }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#addRoleModal">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete --}}
                                                @can('role delete')
                                                    <a href="{{ route('roles.delete', $role->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this role?')"
                                                        class="action-btn action-btn-delete">
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

                    <!-- Add / Edit Role Modal -->
                    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form id="roleForm" action="{{ route('roles.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title fw-bold" id="addRoleModalLabel">Add New Role</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        {{-- Role Name --}}
                                        <div class="mb-3">
                                            <label class="form-label">Role Name</label>
                                            <input type="text" name="name" id="roleNameInput" class="form-control" required>
                                        </div>

                                        {{-- Permissions --}}
                                        <div class="mb-3">
                                            <label class="form-label">Assign Permissions</label>

                                            <!-- Select All Permissions -->
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="select_all_permissions">
                                                <label class="form-check-label fw-bold" for="select_all_permissions">
                                                    Select All Permissions
                                                </label>
                                            </div>

                                            <!-- Permissions Group -->
                                            <div class="row">
                                                @foreach ($groupedPermissions as $group => $perms)
                                                    @php
                                                        $groupKey = Str::slug($group, '_');
                                                        $groupLabel = Str::headline($group);
                                                        $actions = $perms->filter(function ($p) use ($group) {
                                                            return strtolower($p->name) !== strtolower($group);
                                                        });
                                                        $childIds = $actions
                                                            ->pluck('id')
                                                            ->map(fn($id) => "perm_$id")
                                                            ->implode(',');
                                                        $actionsChecked = $actions
                                                            ->filter(
                                                                fn($p) => in_array($p->name, old('permissions', [])),
                                                            )
                                                            ->count();
                                                        $parentChecked =
                                                            $actions->count() > 0 &&
                                                            $actionsChecked === $actions->count();
                                                    @endphp

                                                    <div class="col-12 mb-2">
                                                        <div class="border rounded p-3">
                                                            <div class="row align-items-center">
                                                                {{-- Parent Permission (e.g. categories) --}}
                                                                <div class="col-md-3 col-sm-4">
                                                                    <div class="form-check">
                                                                        <input type="checkbox"
                                                                            class="form-check-input group-parent"
                                                                            id="group_{{ $groupKey }}"
                                                                            data-group="{{ $groupKey }}"
                                                                            data-children="{{ $childIds }}"
                                                                            name="permissions[]" value="{{ $group }}"
                                                                            @checked($parentChecked)>
                                                                        <label class="form-check-label fw-semibold"
                                                                            for="group_{{ $groupKey }}">
                                                                            {{ $groupLabel }}
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                {{-- Children Permissions --}}
                                                                <div class="col-md-9 col-sm-8">
                                                                    <div class="d-flex flex-wrap gap-4">
                                                                        @foreach ($actions as $permission)
                                                                            @php
                                                                                $action = Str::after(
                                                                                    $permission->name,
                                                                                    $group,
                                                                                );
                                                                                $prettyAction = Str::headline(
                                                                                    trim($action),
                                                                                );
                                                                            @endphp
                                                                            <div class="form-check">
                                                                                <input type="checkbox" name="permissions[]"
                                                                                    value="{{ $permission->name }}"
                                                                                    class="form-check-input permission-checkbox"
                                                                                    id="perm_{{ $permission->id }}"
                                                                                    data-group="{{ $groupKey }}"
                                                                                    @checked(in_array($permission->name, old('permissions', [])))>
                                                                                <label class="form-check-label"
                                                                                    for="perm_{{ $permission->id }}">
                                                                                    {{ $prettyAction }}
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary px-4"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">Create Role</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- View Role Permissions Modal -->
                    <div class="modal fade" id="viewRolePermissionsModal" tabindex="-1" aria-labelledby="viewRolePermissionsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header border-bottom py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar bg-label-primary rounded p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bx bx-shield-quarter text-primary fs-4"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title fw-bold text-dark mb-0" id="viewRolePermissionsModalLabel">
                                                Role: <span id="viewModalRoleName" class="text-primary"></span>
                                            </h5>
                                            <small class="text-muted">Assigned module permissions</small>
                                        </div>
                                        <span id="viewModalPermissionCount" class="badge bg-label-success text-dark fw-bold ms-2 px-2.5 py-1"></span>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-dark border-end-0">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" id="permissionSearchInput" class="form-control border-start-0 ps-0 text-dark" placeholder="Filter permissions (e.g. food, purchase, store, user)...">
                                        </div>
                                    </div>

                                    <div id="viewPermissionsContainer" style="min-height: 120px;">
                                        <!-- Dynamically loaded via JS -->
                                    </div>
                                </div>

                                <div class="modal-footer border-top py-3">
                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                                </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('select_all_permissions');
            const childChecks = Array.from(document.querySelectorAll('.permission-checkbox')); // sirf children
            const groupParents = Array.from(document.querySelectorAll('.group-parent')); // base/parent

            function updateSelectAll() {
                const total = childChecks.length;
                const checked = childChecks.filter(c => c.checked).length;
                selectAll.checked = (total > 0 && checked === total);
                selectAll.indeterminate = (checked > 0 && checked < total);
            }

            function updateGroupParent(groupKey) {
                const kids = childChecks.filter(c => c.dataset.group === groupKey);
                const parent = document.querySelector(`.group-parent[data-group="${groupKey}"]`);
                if (!parent) return;

                const total = kids.length;
                const checked = kids.filter(c => c.checked).length;

                if (checked === total && total > 0) {
                    parent.checked = true;
                    parent.indeterminate = false;
                } else if (checked > 0) {
                    parent.checked = true;
                    parent.indeterminate = false;
                }
            }

            // Select All
            selectAll.addEventListener('change', () => {
                childChecks.forEach(cb => cb.checked = selectAll.checked);
                groupParents.forEach(p => {
                    p.checked = selectAll.checked;
                    p.indeterminate = false;
                });
            });

            // Parent → children
            groupParents.forEach(parent => {
                parent.addEventListener('change', () => {
                    const ids = (parent.dataset.children || '').split(',').filter(Boolean);
                    ids.forEach(id => {
                        const cb = document.getElementById(id);
                        if (cb) cb.checked = parent.checked;
                    });
                    parent.indeterminate = false;
                    updateSelectAll();
                });
            });

            // Child → parent & global
            childChecks.forEach(cb => {
                cb.addEventListener('change', () => {
                    updateGroupParent(cb.dataset.group);
                    updateSelectAll();
                });
            });
            // Add new logic for modal Add and Edit actions
            const roleForm = document.getElementById('roleForm');
            const modalTitle = document.getElementById('addRoleModalLabel');
            const submitBtn = document.getElementById('submitBtn');
            const roleNameInput = document.getElementById('roleNameInput');
            const storeRoute = "{{ route('roles.store') }}";

            window.resetRoleForm = function() {
                roleForm.action = storeRoute;
                modalTitle.textContent = 'Add New Role';
                submitBtn.textContent = 'Create Role';
                roleNameInput.value = '';
                
                // uncheck all permissions
                childChecks.forEach(cb => cb.checked = false);
                groupParents.forEach(p => {
                    p.checked = false;
                    p.indeterminate = false;
                });
                selectAll.checked = false;
                selectAll.indeterminate = false;
            };

            document.querySelectorAll('.edit-role-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const perms = JSON.parse(this.dataset.permissions);

                    // Update form action for editing
                    roleForm.action = `/roles/${id}/update`;
                    modalTitle.textContent = 'Edit Role';
                    submitBtn.textContent = 'Update Role';
                    roleNameInput.value = name;

                    // Uncheck all first
                    childChecks.forEach(cb => cb.checked = false);
                    groupParents.forEach(p => {
                        p.checked = false;
                        p.indeterminate = false;
                    });

                    // Check assigned permissions (both children and parent)
                    perms.forEach(pName => {
                        const cb = document.querySelector(`.permission-checkbox[value="${pName}"]`);
                        if(cb) cb.checked = true;
                        const parent = document.querySelector(`.group-parent[value="${pName}"]`);
                        if(parent) parent.checked = true;
                    });

                    // Trigger update to fix parents and select all checkboxes
                    groupParents.forEach(p => updateGroupParent(p.dataset.group));
                    updateSelectAll();
                });
            });

            // View Role Permissions Modal event delegation (works with DataTables pagination)
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.view-permissions-modal-btn');
                if (!btn) return;

                const roleName = btn.dataset.role || '';
                let permissions = [];
                try {
                    permissions = JSON.parse(btn.dataset.permissions || '[]');
                } catch(err) {
                    permissions = [];
                }

                document.getElementById('viewModalRoleName').textContent = roleName;
                document.getElementById('viewModalPermissionCount').textContent = permissions.length + ' Total';

                const container = document.getElementById('viewPermissionsContainer');
                const searchInput = document.getElementById('permissionSearchInput');
                searchInput.value = '';

                function renderBadges(filterText = '') {
                    container.innerHTML = '';
                    const query = filterText.toLowerCase().trim();
                    const filtered = permissions.filter(p => p.toLowerCase().includes(query));

                    if (filtered.length === 0) {
                        container.innerHTML = `
                            <div class="text-center py-4">
                                <i class="bx bx-info-circle fs-2 text-muted d-block mb-1"></i>
                                <span class="text-dark fw-semibold">No matching permissions found.</span>
                            </div>
                        `;
                        return;
                    }

                    const badgesHtml = filtered.map(p => `
                        <span class="badge bg-label-primary text-dark border px-3 py-2 fw-bold text-uppercase d-inline-flex align-items-center" style="font-size: 12px; letter-spacing: 0.3px; border-radius: 6px;">
                            <i class="bx bx-check text-success me-1 fs-6"></i>${p}
                        </span>
                    `).join('');

                    container.innerHTML = `<div class="d-flex flex-wrap gap-2">${badgesHtml}</div>`;
                }

                renderBadges();

                searchInput.oninput = function() {
                    renderBadges(this.value);
                };
            });

        });
    </script>
@endsection
