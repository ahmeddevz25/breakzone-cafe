@extends('admin.layouts')
@section('content')
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <div class="layout-page">


                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Update</span> Role</h4>
                    <div class="row">
                        <div class="col-xl">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h2>Update Role</h2>

                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                                        @csrf

                                        {{-- Name --}}
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Role Name</label>
                                            <input type="text" name="name" class="form-control" id="name"
                                                value="{{ old('name', $role->name) }}" placeholder="Enter role name"
                                                required>
                                        </div>

                                        {{-- Permissions --}}
                                        @php use Illuminate\Support\Str; @endphp

                                        <div class="mb-3">
                                            <label class="form-label">Assign Permissions</label>

                                            {{-- Select All --}}
                                            <div class="form-check mb-2">
                                                <input type="checkbox" id="select_all_permissions" class="form-check-input">
                                                <label for="select_all_permissions" class="form-check-label fw-bold">Select
                                                    All Permissions</label>
                                            </div>

                                            {{-- One row per module --}}
                                            <div class="row g-3">
                                                @foreach ($groupedPermissions as $group => $perms)
                                                    @php
                                                        $groupKey = Str::slug($group, '_'); // safe for data-attrs
                                                        $groupLabel = Str::headline($group); // "user management" → "User Management"

                                                        // Base permission (exact match with group name)
                                                        $basePerm = $perms->first(function ($p) use ($group) {
                                                            return strtolower($p->name) === strtolower($group);
                                                        });

                                                        // Children = all except base
                                                        $actions = $perms
                                                            ->filter(function ($p) use ($group) {
                                                                return strtolower($p->name) !== strtolower($group);
                                                            })
                                                            ->values();

                                                        // Child IDs for parent -> children toggle
                                                        $childIds = $actions
                                                            ->pluck('id')
                                                            ->map(fn($id) => "perm_$id")
                                                            ->implode(',');

                                                        // Initial checked states (based on actions)
                                                        $actionsChecked = $actions
                                                            ->filter(
                                                                fn($p) => in_array(strtolower($p->name), $rolePerms),
                                                            )
                                                            ->count();
                                                        $allActions = $actions->count();
                                                        $parentChecked =
                                                            $allActions > 0 && $actionsChecked === $allActions;
                                                    @endphp

                                                    <div class="col-12">
                                                        <div class="border rounded p-3">
                                                            <div class="row align-items-center">
                                                                {{-- Parent (module/base) --}}
                                                                <div class="col-md-3 col-sm-4">
                                                                    <div class="form-check">
                                                                        {{-- Parent itself is also a real permission (if exists) --}}
                                                                        <input type="checkbox"
                                                                            class="form-check-input group-parent"
                                                                            id="group_{{ $groupKey }}"
                                                                            data-group="{{ $groupKey }}"
                                                                            data-children="{{ $childIds }}"
                                                                            name="permissions[]"
                                                                            value="{{ $basePerm?->name ?? $group }}"
                                                                            @checked($parentChecked || ($basePerm && in_array(strtolower($basePerm->name), $rolePerms)))>
                                                                        <label class="form-check-label fw-semibold"
                                                                            for="group_{{ $groupKey }}">
                                                                            {{ $groupLabel }}
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                {{-- Children (inline) --}}
                                                                <div class="col-md-9 col-sm-8">
                                                                    <div class="d-flex flex-wrap gap-4">
                                                                        @foreach ($actions as $permission)
                                                                            @php
                                                                                // Last word = action (add/edit/delete…)
                                                                                $actionRaw = preg_replace(
                                                                                    '/^.*\s+/',
                                                                                    '',
                                                                                    strtolower($permission->name),
                                                                                );
                                                                                $pretty = Str::headline($actionRaw);
                                                                            @endphp
                                                                            <div class="form-check">
                                                                                <input type="checkbox" name="permissions[]"
                                                                                    value="{{ $permission->name }}"
                                                                                    class="form-check-input permission-checkbox"
                                                                                    id="perm_{{ $permission->id }}"
                                                                                    data-group="{{ $groupKey }}"
                                                                                    @checked(in_array(strtolower($permission->name), $rolePerms))>
                                                                                <label for="perm_{{ $permission->id }}"
                                                                                    class="form-check-label">{{ $pretty }}</label>
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


                                        <button type="submit" class="btn btn-primary">Update Role</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layout-over
        lay layout-menu-toggle"></div>
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

            // ✅ UPDATED: parent ko independent rakha gaya
            function updateGroupParent(groupKey) {
                const kids = childChecks.filter(c => c.dataset.group === groupKey);
                const parent = document.querySelector(`.group-parent[data-group="${groupKey}"]`);
                if (!parent) return;

                const total = kids.length;
                const checked = kids.filter(c => c.checked).length;

                // Parent ko sirf tab force-check karein jab saare children checked ho
                if (checked === total && total > 0) {
                    parent.checked = true;
                }
                // ❌ Parent ko auto-uncheck NA karein (user ki marzi par chhodein)

                // Partial selection par bas indeterminate
                parent.indeterminate = (checked > 0 && checked < total);
            }

            // Init states
            [...new Set(childChecks.map(c => c.dataset.group))].forEach(updateGroupParent);
            updateSelectAll();

            // Select All → sab children + sab parents toggle
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
        });
    </script>
@endsection
