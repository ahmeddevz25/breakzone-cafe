@extends('admin.layouts')
@section('title', 'Users Management')
@section('content')
    @include('sweetalert::alert')

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">

                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <!-- Header -->
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">All Users</h5>
                        @can('user add')
                            <button id="addUserBtn" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#userModal">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add User</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>Sr. No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $key => $user)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-semibold">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>

                                        {{-- Role(s) --}}
                                        <td>
                                            @foreach ($user->getRoleNames() as $role)
                                                <span class="badge bg-primary">{{ $role }}</span>
                                            @endforeach
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('user edit')
                                                    <a href="javascript:void(0);" class="text-primary fs-5 editUserBtn"
                                                        data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                        data-email="{{ $user->email }}"
                                                        data-role="{{ $user->roles->first()->name ?? '' }}" data-bs-toggle="modal"
                                                        data-bs-target="#userModal">
                                                        <i class='bx bx-edit'></i>
                                                    </a>
                                                @endcan


                                                @can('user delete')
                                                    <a href="{{ route('users.destroy', $user->id) }}"
                                                        onclick="return confirm('Are you sure you want to delete this user?')"
                                                        class="text-danger fs-5">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No users available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Add/Edit User Modal -->
                    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="userForm" method="POST">
                                    @csrf
                                    <div id="formMethod"></div>

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        {{-- Name --}}
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" id="user_name" class="form-control"
                                                required>
                                        </div>

                                        {{-- Email --}}
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" id="user_email" class="form-control"
                                                required>
                                        </div>

                                        {{-- Password --}}
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" name="password" id="user_password" class="form-control">
                                            <small class="text-muted">Leave blank if not changing</small>
                                        </div>

                                        {{-- Role --}}
                                        <div class="mb-3">
                                            <label class="form-label">Assign Role</label>
                                            <select name="role" id="user_role" class="form-select" required>
                                                <option value="" disabled>-- Select Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Add
                                            User</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div> <!-- card -->
            </div> <!-- layout-page -->
        </div> <!-- layout-container -->
    </div> <!-- wrapper -->

    <div class="layout-overlay layout-menu-toggle"></div>

    <script>
        // === Grab form elements ===
        let form = document.getElementById("userForm");
        let formMethod = document.getElementById("formMethod");
        let nameInput = document.getElementById("user_name");
        let emailInput = document.getElementById("user_email");
        let roleSelect = document.getElementById("user_role");
        let passInput = document.getElementById("user_password");
        let modalTitle = document.getElementById("userModalLabel");
        let submitBtn = document.getElementById("modalSubmitBtn");

        // === ADD USER ===
        document.getElementById("addUserBtn").addEventListener("click", function() {
            form.action = "{{ route('users.store') }}"; // /users
            formMethod.innerHTML = "";

            nameInput.value = "";
            emailInput.value = "";
            roleSelect.value = "";
            passInput.value = "";
            passInput.required = true;



            modalTitle.textContent = "Add New User";
            submitBtn.textContent = "Add User";
        });

        // === EDIT USER ===
        document.querySelectorAll(".editUserBtn").forEach(btn => {
            btn.addEventListener("click", function() {
                let id = this.dataset.id;
                let name = this.dataset.name;
                let email = this.dataset.email;
                let role = this.dataset.role;



                form.action = "/users/" + id; // /users/{id}
                formMethod.innerHTML = `{!! method_field('PUT') !!}`;

                nameInput.value = name;
                emailInput.value = email;
                roleSelect.value = role;
                passInput.value = "";
                passInput.required = false;



                modalTitle.textContent = "Edit User";
                submitBtn.textContent = "Update User";
            });
        });
    </script>
@endsection
