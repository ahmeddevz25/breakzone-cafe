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
                        <div class="d-flex align-items-center gap-2">
                            <select id="roleFilter" class="form-select form-select-sm" style="width: 150px;">
                                <option value="">All Roles</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                            @can('user add')
                                <button id="addUserBtn" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#userModal">
                                    <i class="bx bx-plus icon-sm"></i>
                                    <span class="d-none d-sm-inline-block">Add User</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100 mb-0" id="usersTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">Sr. No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>School(s)</th>
                                    <th>Store(s)</th>
                                    <th>Roles</th>
                                    <th class="text-center" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Add/Edit User Modal -->
                    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form id="userForm" method="POST">
                                    @csrf
                                    <div id="formMethod"></div>

                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="userModalLabel">Add New User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row">
                                            {{-- Name --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Name</label>
                                                <input type="text" name="name" id="user_name" class="form-control"
                                                    placeholder="Enter full name" required>
                                            </div>

                                            {{-- Email --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Email</label>
                                                <input type="email" name="email" id="user_email" class="form-control"
                                                    placeholder="Enter email address" required>
                                            </div>

                                            {{-- Password --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Password</label>
                                                <input type="password" name="password" id="user_password" class="form-control"
                                                    placeholder="••••••••">
                                                <small class="text-muted d-block mt-1">Leave blank if not changing</small>
                                            </div>

                                            {{-- Role --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Assign Role</label>
                                                <select name="role" id="user_role" class="form-select" required>
                                                    <option value="" disabled>-- Select Role --</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Schools (Compact Scrollable Checkbox Box) --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-label fw-bold text-dark mb-0">
                                                        School(s) <span class="text-danger">*</span> <span class="text-muted fw-normal" style="font-size: 11px;">(<a href="javascript:void(0);" id="checkAllSchools">Check All</a> | <a href="javascript:void(0);" id="clearAllSchools">Clear</a>)</span>
                                                    </label>
                                                    <small class="text-dark fw-bold" id="selectedSchoolsCount">0 selected</small>
                                                </div>
                                                <div id="schoolBoxWrapper" class="border rounded p-2 bg-white" style="max-height: 155px; overflow-y: auto;">
                                                    <input type="text" id="schoolSearch" class="form-control form-control-sm mb-2 text-dark" placeholder="🔍 Search school...">
                                                    <div id="schoolCheckboxContainer">
                                                        @foreach ($schools as $school)
                                                            <div class="form-check school-item mb-1">
                                                                <input class="form-check-input school-checkbox" type="checkbox" name="cafe_school_id[]" value="{{ $school->id }}" id="school_{{ $school->id }}">
                                                                <label class="form-check-label small text-truncate d-block text-dark fw-semibold" for="school_{{ $school->id }}" title="{{ $school->name }}" style="cursor: pointer; color: #111827 !important;">
                                                                    {{ $school->name }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div id="schoolErrorFeedback" class="text-danger small fw-bold mt-1 d-none">
                                                    <i class='bx bx-error-circle me-1'></i> Please select at least one school.
                                                </div>
                                            </div>

                                            {{-- Stores (Compact Scrollable Checkbox Box) --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-label fw-bold text-dark mb-0">
                                                        Store(s) <span class="text-danger">*</span> <span class="text-muted fw-normal" style="font-size: 11px;">(<a href="javascript:void(0);" id="checkAllStores">Check All</a> | <a href="javascript:void(0);" id="clearAllStores">Clear</a>)</span>
                                                    </label>
                                                    <small class="text-dark fw-bold" id="selectedStoresCount">0 selected</small>
                                                </div>
                                                <div id="storeBoxWrapper" class="border rounded p-2 bg-white" style="max-height: 155px; overflow-y: auto;">
                                                    <input type="text" id="storeSearch" class="form-control form-control-sm mb-2 text-dark" placeholder="🔍 Search store...">
                                                    <div id="storeCheckboxContainer">
                                                        @foreach ($stores as $store)
                                                            <div class="form-check store-item mb-1">
                                                                <input class="form-check-input store-checkbox" type="checkbox" name="store_id[]" value="{{ $store->id }}" id="store_{{ $store->id }}">
                                                                <label class="form-check-label small text-truncate d-block text-dark fw-semibold" for="store_{{ $store->id }}" title="{{ $store->store }}" style="cursor: pointer; color: #111827 !important;">
                                                                    {{ $store->store }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div id="storeErrorFeedback" class="text-danger small fw-bold mt-1 d-none">
                                                    <i class='bx bx-error-circle me-1'></i> Please select at least one store.
                                                </div>
                                            </div>
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

                    <!-- User Detail Modal -->
                    <div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-light border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                                            <i class='bx bx-user fs-3 text-primary'></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title fw-bold mb-0 text-dark" id="detail_user_name" style="color: #111827 !important;">User Details</h5>
                                            <small class="text-dark fw-semibold" id="detail_user_email" style="color: #374151 !important;"></small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body p-4">
                                    <!-- Info Cards Row -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-sm-4">
                                            <div class="card border shadow-none bg-light h-100">
                                                <div class="card-body p-3">
                                                    <small class="text-dark text-uppercase fw-bold" style="color: #111827 !important; letter-spacing: 0.5px;">Assigned Role</small>
                                                    <div class="mt-2">
                                                        <span class="badge bg-success text-white fs-6 fw-bold px-3 py-2" id="detail_user_role"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="card border shadow-none bg-light h-100">
                                                <div class="card-body p-3">
                                                    <small class="text-dark text-uppercase fw-bold" style="color: #111827 !important; letter-spacing: 0.5px;">Assigned Schools</small>
                                                    <div class="mt-2">
                                                        <span class="badge bg-info text-white fs-6 fw-bold px-3 py-2" id="detail_school_count"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="card border shadow-none bg-light h-100">
                                                <div class="card-body p-3">
                                                    <small class="text-dark text-uppercase fw-bold" style="color: #111827 !important; letter-spacing: 0.5px;">Assigned Stores</small>
                                                    <div class="mt-2">
                                                        <span class="badge bg-warning text-dark fs-6 fw-bold px-3 py-2" id="detail_store_count" style="color: #111827 !important;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assigned Schools List -->
                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-2 text-uppercase text-dark small d-flex align-items-center gap-1" style="color: #111827 !important;">
                                            <i class='bx bxs-school text-success fs-5'></i> <span style="color: #111827 !important; font-weight: 700;">ASSIGNED SCHOOLS LIST</span>
                                        </h6>
                                        <div class="border rounded p-3 bg-white" style="max-height: 180px; overflow-y: auto;">
                                            <div class="row g-2" id="detail_schools_list">
                                                <!-- Populated dynamically via JS -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assigned Stores List -->
                                    <div>
                                        <h6 class="fw-bold mb-2 text-uppercase text-dark small d-flex align-items-center gap-1" style="color: #111827 !important;">
                                            <i class='bx bx-store-alt text-warning fs-5'></i> <span style="color: #111827 !important; font-weight: 700;">ASSIGNED STORES LIST</span>
                                        </h6>
                                        <div class="border rounded p-3 bg-white" style="max-height: 180px; overflow-y: auto;">
                                            <div class="row g-2" id="detail_stores_list">
                                                <!-- Populated dynamically via JS -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-top bg-light">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- card -->
            </div> <!-- layout-page -->
        </div> <!-- layout-container -->
    </div> <!-- wrapper -->

    <div class="layout-overlay layout-menu-toggle"></div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Users DataTable (Server-Side AJAX)
            const dataTable = $('#usersTable').DataTable({
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
                        d.role_filter = $('#roleFilter').val();
                    },
                    error: function(xhr, error, code) {
                        console.error('Users DataTable Error:', error, xhr.responseText);
                    }
                },
                dom: 'lfrtip',
                columns: [
                    { data: 'index', name: 'index', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'schools', name: 'schools' },
                    { data: 'stores', name: 'stores' },
                    { data: 'roles', name: 'roles' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search users, email, role...",
                    lengthMenu: "Show _MENU_ entries",
                    processing: '<div class="d-flex justify-content-center align-items-center py-2 text-primary"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading users...</div>',
                    emptyTable: '<div class="text-center text-muted py-4"><i class="bx bx-user fs-2 d-block mb-1"></i> No users found</div>',
                    zeroRecords: '<div class="text-center text-muted py-4"><i class="bx bx-search-alt fs-2 d-block mb-1"></i> No matching users found</div>',
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    infoEmpty: "Showing 0 to 0 of 0 users",
                    infoFiltered: "(filtered from _MAX_ total users)"
                }
            });

            $('#roleFilter').on('change', function() {
                dataTable.ajax.reload();
            });

            // === Grab form elements ===
            let form = document.getElementById("userForm");
        let formMethod = document.getElementById("formMethod");
        let nameInput = document.getElementById("user_name");
        let emailInput = document.getElementById("user_email");
        let roleSelect = document.getElementById("user_role");
        let passInput = document.getElementById("user_password");
        let modalTitle = document.getElementById("userModalLabel");
        let submitBtn = document.getElementById("modalSubmitBtn");

        // School controls
        let checkAllSchoolsBtn = document.getElementById("checkAllSchools");
        let clearAllSchoolsBtn = document.getElementById("clearAllSchools");
        let schoolSearchInput = document.getElementById("schoolSearch");

        // Store controls
        let checkAllStoresBtn = document.getElementById("checkAllStores");
        let clearAllStoresBtn = document.getElementById("clearAllStores");
        let storeSearchInput = document.getElementById("storeSearch");

        // Helper: Clear validation errors when valid
        function clearSchoolError() {
            let count = document.querySelectorAll('.school-checkbox:checked').length;
            let schoolWrapper = document.getElementById('schoolBoxWrapper');
            let schoolError = document.getElementById('schoolErrorFeedback');
            if (count > 0) {
                if (schoolWrapper) {
                    schoolWrapper.style.border = '';
                    schoolWrapper.style.boxShadow = '';
                }
                if (schoolError) schoolError.classList.add('d-none');
            }
        }

        function clearStoreError() {
            let count = document.querySelectorAll('.store-checkbox:checked').length;
            let storeWrapper = document.getElementById('storeBoxWrapper');
            let storeError = document.getElementById('storeErrorFeedback');
            if (count > 0) {
                if (storeWrapper) {
                    storeWrapper.style.border = '';
                    storeWrapper.style.boxShadow = '';
                }
                if (storeError) storeError.classList.add('d-none');
            }
        }

        function resetValidationErrors() {
            let schoolWrapper = document.getElementById('schoolBoxWrapper');
            let schoolError = document.getElementById('schoolErrorFeedback');
            let storeWrapper = document.getElementById('storeBoxWrapper');
            let storeError = document.getElementById('storeErrorFeedback');
            if (schoolWrapper) {
                schoolWrapper.style.border = '';
                schoolWrapper.style.boxShadow = '';
            }
            if (schoolError) schoolError.classList.add('d-none');
            if (storeWrapper) {
                storeWrapper.style.border = '';
                storeWrapper.style.boxShadow = '';
            }
            if (storeError) storeError.classList.add('d-none');
        }

        // Helper: Update counts
        function updateSelectedSchoolCount() {
            let count = document.querySelectorAll('.school-checkbox:checked').length;
            document.getElementById('selectedSchoolsCount').textContent = count + " selected";
            clearSchoolError();
        }

        function updateSelectedStoreCount() {
            let count = document.querySelectorAll('.store-checkbox:checked').length;
            document.getElementById('selectedStoresCount').textContent = count + " selected";
            clearStoreError();
        }

        document.querySelectorAll('.school-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedSchoolCount));
        document.querySelectorAll('.store-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedStoreCount));

        // School Search filter
        schoolSearchInput.addEventListener('input', function() {
            let query = this.value.toLowerCase().trim();
            document.querySelectorAll('.school-item').forEach(item => {
                let text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Store Search filter
        storeSearchInput.addEventListener('input', function() {
            let query = this.value.toLowerCase().trim();
            document.querySelectorAll('.store-item').forEach(item => {
                let text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Schools Check All & Clear
        checkAllSchoolsBtn.addEventListener('click', function() {
            document.querySelectorAll('.school-checkbox').forEach(cb => {
                if (cb.closest('.school-item').style.display !== 'none') cb.checked = true;
            });
            updateSelectedSchoolCount();
        });

        clearAllSchoolsBtn.addEventListener('click', function() {
            document.querySelectorAll('.school-checkbox').forEach(cb => cb.checked = false);
            updateSelectedSchoolCount();
        });

        // Stores Check All & Clear
        checkAllStoresBtn.addEventListener('click', function() {
            document.querySelectorAll('.store-checkbox').forEach(cb => {
                if (cb.closest('.store-item').style.display !== 'none') cb.checked = true;
            });
            updateSelectedStoreCount();
        });

        clearAllStoresBtn.addEventListener('click', function() {
            document.querySelectorAll('.store-checkbox').forEach(cb => cb.checked = false);
            updateSelectedStoreCount();
        });

        // === VIEW USER DETAILS MODAL (Delegated for AJAX) ===
        $(document).on("click", ".viewDetailBtn", function() {
            let name = this.dataset.name || "User Details";
                let email = this.dataset.email || "";
                let role = this.dataset.role || "No Role";
                let schools = [];
                let stores = [];
                try { schools = JSON.parse(this.dataset.schools || '[]'); } catch(e) { schools = []; }
                try { stores = JSON.parse(this.dataset.stores || '[]'); } catch(e) { stores = []; }

                document.getElementById("detail_user_name").textContent = name;
                document.getElementById("detail_user_email").textContent = email;
                document.getElementById("detail_user_role").textContent = role;
                document.getElementById("detail_school_count").textContent = schools.length + " Assigned";
                document.getElementById("detail_store_count").textContent = stores.length + " Assigned";

                // Populate schools
                let schoolContainer = document.getElementById("detail_schools_list");
                schoolContainer.innerHTML = "";
                if (schools.length === 0) {
                    schoolContainer.innerHTML = '<div class="col-12 text-center text-dark py-2 fw-semibold" style="color: #111827 !important;">No schools assigned.</div>';
                } else {
                    schools.forEach(school => {
                        let col = document.createElement("div");
                        col.className = "col-md-6";
                        col.innerHTML = `
                            <div class="d-flex align-items-center p-2 rounded border bg-white shadow-sm h-100">
                                <i class='bx bxs-school text-success fs-5 me-2 flex-shrink-0'></i>
                                <span class="small fw-bold text-dark text-truncate" title="${school}" style="color: #111827 !important; font-size: 13px;">${school}</span>
                            </div>
                        `;
                        schoolContainer.appendChild(col);
                    });
                }

                // Populate stores
                let storeContainer = document.getElementById("detail_stores_list");
                storeContainer.innerHTML = "";
                if (stores.length === 0) {
                    storeContainer.innerHTML = '<div class="col-12 text-center text-dark py-2 fw-semibold" style="color: #111827 !important;">No stores assigned.</div>';
                } else {
                    stores.forEach(store => {
                        let col = document.createElement("div");
                        col.className = "col-md-6";
                        col.innerHTML = `
                            <div class="d-flex align-items-center p-2 rounded border bg-white shadow-sm h-100">
                                <i class='bx bx-store-alt text-warning fs-5 me-2 flex-shrink-0'></i>
                                <span class="small fw-bold text-dark text-truncate" title="${store}" style="color: #111827 !important; font-size: 13px;">${store}</span>
                            </div>
                        `;
                        storeContainer.appendChild(col);
                    });
                }
            });

            // === ADD USER ===
            document.getElementById("addUserBtn").addEventListener("click", function() {
                form.action = "{{ route('users.store') }}"; // /users
                formMethod.innerHTML = "";

                nameInput.value = "";
                emailInput.value = "";
                roleSelect.value = "";
                passInput.value = "";
                passInput.required = true;

                // Reset schools
                document.querySelectorAll('.school-checkbox').forEach(cb => cb.checked = false);
                schoolSearchInput.value = "";
                document.querySelectorAll('.school-item').forEach(item => item.style.display = '');
                updateSelectedSchoolCount();

                // Reset stores
                document.querySelectorAll('.store-checkbox').forEach(cb => cb.checked = false);
                storeSearchInput.value = "";
                document.querySelectorAll('.store-item').forEach(item => item.style.display = '');
                updateSelectedStoreCount();

                // Reset errors
                resetValidationErrors();

                modalTitle.textContent = "Add New User";
                submitBtn.textContent = "Add User";
            });

            // === EDIT USER (Delegated for AJAX) ===
            $(document).on("click", ".editUserBtn", function() {
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

                // Reset search & errors
                resetValidationErrors();
                schoolSearchInput.value = "";
                document.querySelectorAll('.school-item').forEach(item => item.style.display = '');
                document.querySelectorAll('.school-checkbox').forEach(cb => cb.checked = false);

                storeSearchInput.value = "";
                document.querySelectorAll('.store-item').forEach(item => item.style.display = '');
                document.querySelectorAll('.store-checkbox').forEach(cb => cb.checked = false);

                // Parse school IDs
                let schoolIds = [];
                try { schoolIds = JSON.parse(this.dataset.schools || '[]'); } catch (e) { schoolIds = []; }
                schoolIds = schoolIds.map(Number);
                document.querySelectorAll('.school-checkbox').forEach(cb => {
                    if (schoolIds.includes(Number(cb.value))) {
                        cb.checked = true;
                    }
                });
                updateSelectedSchoolCount();

                // Parse store IDs
                let storeIds = [];
                try { storeIds = JSON.parse(this.dataset.stores || '[]'); } catch (e) { storeIds = []; }
                storeIds = storeIds.map(Number);
                document.querySelectorAll('.store-checkbox').forEach(cb => {
                    if (storeIds.includes(Number(cb.value))) {
                        cb.checked = true;
                    }
                });
                updateSelectedStoreCount();

                modalTitle.textContent = "Edit User";
                submitBtn.textContent = "Update User";
            });

            // === FORM SUBMIT VALIDATION (Prevents creation without School & Store) ===
            form.addEventListener("submit", function(e) {
                let schoolCount = document.querySelectorAll('.school-checkbox:checked').length;
                let storeCount = document.querySelectorAll('.store-checkbox:checked').length;

                let schoolWrapper = document.getElementById('schoolBoxWrapper');
                let schoolError = document.getElementById('schoolErrorFeedback');
                let storeWrapper = document.getElementById('storeBoxWrapper');
                let storeError = document.getElementById('storeErrorFeedback');

                let hasError = false;

                if (schoolCount === 0) {
                    if (schoolWrapper) {
                        schoolWrapper.style.border = '2px solid #ff3e1d';
                        schoolWrapper.style.boxShadow = '0 0 0 0.2rem rgba(255, 62, 29, 0.2)';
                    }
                    if (schoolError) schoolError.classList.remove('d-none');
                    hasError = true;
                } else {
                    if (schoolWrapper) {
                        schoolWrapper.style.border = '';
                        schoolWrapper.style.boxShadow = '';
                    }
                    if (schoolError) schoolError.classList.add('d-none');
                }

                if (storeCount === 0) {
                    if (storeWrapper) {
                        storeWrapper.style.border = '2px solid #ff3e1d';
                        storeWrapper.style.boxShadow = '0 0 0 0.2rem rgba(255, 62, 29, 0.2)';
                    }
                    if (storeError) storeError.classList.remove('d-none');
                    hasError = true;
                } else {
                    if (storeWrapper) {
                        storeWrapper.style.border = '';
                        storeWrapper.style.boxShadow = '';
                    }
                    if (storeError) storeError.classList.add('d-none');
                }

                if (hasError) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (schoolCount === 0 && storeCount === 0) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Please select at least one School and one Store.');
                        }
                    } else if (schoolCount === 0) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Please select at least one School.');
                        }
                    } else if (storeCount === 0) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Please select at least one Store.');
                        }
                    }
                    return false;
                }
            });

            // Delegated AJAX Delete User
            $(document).on('click', '.delete-user-ajax-btn', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const name = $(this).data('name') || 'this user';

                Swal.fire({
                    title: 'Delete User?',
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
                                Swal.fire('Deleted!', res.message || 'User has been deleted.', 'success');
                                dataTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to delete user.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
@endsection
