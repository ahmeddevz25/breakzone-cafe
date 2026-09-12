@extends('admin.layouts')
@section('content')
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <div class="layout-page">


                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Update</span> User</h4>
                    <div class="row">
                        <div class="col-xl">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h2>Update User</h2>

                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif

                                    {{-- Validation Errors --}}
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf

                                        {{-- Name --}}
                                        <div class="mb-3">
                                            <label for="name" class="form-label">User Name</label>
                                            <input type="text" name="name" class="form-control" id="name"
                                                value="{{ old('name', $user->name) }}" placeholder="Enter user name"
                                                required>
                                        </div>

                                        {{-- Email --}}
                                        <div class="mb-3">
                                            <label for="email" class="form-label">User Email</label>
                                            <input type="email" name="email" class="form-control" id="email"
                                                value="{{ old('email', $user->email) }}" placeholder="Enter user email"
                                                required>
                                        </div>

                                        {{-- Password (Optional) --}}
                                        <div class="mb-3">

                                            <label for="password" class="form-label" data-bs-toggle="tooltip"
                                                data-bs-offset="0,6" data-bs-placement="right" data-bs-html="true"
                                                data-bs-original-title="Leave
                                                        blank to keep current password">New
                                                Password</label>

                                            <input type="password" name="password" data-bs-toggle="tooltip"
                                                data-bs-offset="0,6" data-bs-placement="top" data-bs-html="true"
                                                data-bs-original-title="Leave
                                                        blank to keep current password"
                                                class="form-control" id="password" placeholder="Enter new password">
                                        </div>

                                        {{-- Role --}}
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Assign Role</label>
                                            <select name="role" id="role" class="form-select" required>
                                                <option value="" disabled>-- Select Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ $user->roles->first()?->name === $role->name ? 'selected' : '' }}>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Update User</button>
                                    </form>


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
