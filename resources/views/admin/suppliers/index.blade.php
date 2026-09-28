@extends('admin.layouts')
@section('title', 'Suppliers Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Suppliers Management</h5>
                        <div class="d-flex align-items-center gap-2">
                            <select id="statusFilter" class="form-select form-select-sm" style="width: 140px;">
                                <option value="">All Status</option>
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                            @can('supplier add')
                                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#supplierModal" onclick="resetSupplierForm()">
                                    <i class="bx bx-plus icon-sm"></i>
                                    <span class="d-none d-sm-inline-block">Add New Supplier</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Suppliers Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100" id="suppliersTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Mobile</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-center" style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Supplier Modal (Add / Edit) -->
                    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form id="supplierForm" action="{{ route('suppliers.store') }}" method="POST" data-ajax-table="#suppliersTable">
                                    @csrf
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title fw-bold" id="supplierModalLabel">Add New Supplier</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <div class="row">
                                            {{-- Supplier Name --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Supplier Name</label>
                                                <input type="text" name="name" id="supplierName" class="form-control" placeholder="e.g. John Doe">
                                            </div>

                                            {{-- Company Name --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Company Name</label>
                                                <input type="text" name="company" id="supplierCompany" class="form-control" placeholder="e.g. ABC Beverages">
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- Mobile Number --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                                                <input type="text" name="mobile" id="supplierMobile" class="form-control font-monospace" placeholder="e.g. 03001234567" required>
                                            </div>

                                            {{-- Email Address --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Email Address</label>
                                                <input type="email" name="email" id="supplierEmail" class="form-control" placeholder="e.g. supplier@example.com">
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- NTN Number --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">NTN #</label>
                                                <input type="text" name="ntn_no" id="supplierNtn" class="form-control font-monospace" placeholder="e.g. 1234567-8">
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                <select name="status" id="supplierStatus" class="form-select" required>
                                                    <option value="A">Active</option>
                                                    <option value="I">Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Full Address --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Address</label>
                                            <textarea name="address" id="supplierAddress" class="form-control" rows="2" placeholder="Complete office/warehouse address..."></textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">Save Supplier</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- View Supplier Details Modal -->
                    <div class="modal fade" id="viewSupplierModal" tabindex="-1" aria-labelledby="viewSupplierModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-bottom py-3 bg-light">
                                    <h5 class="modal-title fw-bold" id="viewSupplierModalLabel">
                                        <i class="bx bx-user-check text-primary me-2"></i><span id="v_supplier_title">Supplier Details</span>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted fw-semibold" style="width: 35%;">Name:</td>
                                                <td class="fw-bold text-dark" id="v_name">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Company:</td>
                                                <td class="text-dark fw-medium" id="v_company">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Mobile:</td>
                                                <td class="text-dark font-monospace" id="v_mobile">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Email:</td>
                                                <td class="text-dark" id="v_email">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">NTN #:</td>
                                                <td class="text-dark font-monospace" id="v_ntn">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Address:</td>
                                                <td class="text-dark" id="v_address">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Status:</td>
                                                <td id="v_status">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer border-top py-3 bg-light">
                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                                </div>
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
            const dataTable = $('#suppliersTable').DataTable({
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
                        console.error('Suppliers DataTable Error:', error);
                    }
                },
                dom: 'lfrtip',
                columns: [
                    { data: 'index', name: 'index', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'name', name: 'name' },
                    { data: 'company', name: 'company' },
                    { data: 'mobile', name: 'mobile' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search suppliers...",
                    lengthMenu: "Show _MENU_ entries",
                    processing: '<div class="d-flex justify-content-center align-items-center py-2 text-primary"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading suppliers...</div>',
                    emptyTable: '<div class="text-center text-muted py-4"><i class="bx bx-group fs-2 d-block mb-1"></i> No suppliers found</div>',
                    zeroRecords: '<div class="text-center text-muted py-4"><i class="bx bx-search-alt fs-2 d-block mb-1"></i> No matching suppliers found</div>',
                    info: "Showing _START_ to _END_ of _TOTAL_ suppliers",
                    infoEmpty: "Showing 0 to 0 of 0 suppliers",
                    infoFiltered: "(filtered from _MAX_ total suppliers)"
                }
            });

            // Trigger reload on custom status filter change
            $('#statusFilter').on('change', function() {
                dataTable.ajax.reload();
            });

            const supplierForm = document.getElementById('supplierForm');
            const supplierRoute = "{{ route('suppliers.store') }}";

            // Reset modal for Add Supplier
            window.resetSupplierForm = function() {
                supplierForm.action = supplierRoute;
                $('#supplierModalLabel').text('Add New Supplier');
                $('#submitBtn').text('Save Supplier');
                supplierForm.reset();
                $('#supplierStatus').val("A");
                $(supplierForm).find('.is-invalid').removeClass('is-invalid');
                $(supplierForm).find('.invalid-feedback').remove();
            };

            // Edit Supplier Handler (Delegated for AJAX-rendered rows)
            $(document).on('click', '.edit-supplier-btn', function() {
                var btn = $(this);
                var id = btn.data('id');
                var name = btn.data('name');
                var company = btn.data('company');
                var address = btn.data('address');
                var mobile = btn.data('mobile');
                var ntn = btn.data('ntn');
                var email = btn.data('email');
                var status = btn.data('status');

                if (status === "active" || status === "1" || status === "A") {
                    status = "A";
                } else {
                    status = "I";
                }

                supplierForm.action = `/suppliers/${id}/update`;
                $('#supplierModalLabel').text('Edit Supplier');
                $('#submitBtn').text('Update Supplier');

                $('#supplierName').val(name || '');
                $('#supplierCompany').val(company || '');
                $('#supplierAddress').val(address || '');
                $('#supplierMobile').val(mobile || '');
                $('#supplierNtn').val(ntn || '');
                $('#supplierEmail').val(email || '');
                $('#supplierStatus').val(status);

                $(supplierForm).find('.is-invalid').removeClass('is-invalid');
                $(supplierForm).find('.invalid-feedback').remove();

                var modalEl = document.getElementById('supplierModal');
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.show();
            });

            // View Supplier Handler (Delegated)
            $(document).on('click', '.view-supplier-btn', function() {
                var btn = $(this);
                var name = btn.data('name') || 'N/A';
                $('#v_supplier_title').text(name);
                $('#v_name').text(name);
                $('#v_company').text(btn.data('company') || 'N/A');
                $('#v_mobile').text(btn.data('mobile') || 'N/A');
                $('#v_email').text(btn.data('email') || 'N/A');
                $('#v_ntn').text(btn.data('ntn') || 'N/A');
                $('#v_address').text(btn.data('address') || 'N/A');

                var statusVal = btn.data('status');
                var statusBadge = (statusVal === 'A' || statusVal === 'active' || statusVal === '1') 
                    ? '<span class="badge bg-success">Active</span>' 
                    : '<span class="badge bg-danger">Inactive</span>';
                $('#v_status').html(statusBadge);

                var modalEl = document.getElementById('viewSupplierModal');
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.show();
            });

            // AJAX Delete Supplier Handler with SweetAlert2 Confirmation
            $(document).on('click', '.delete-supplier-ajax-btn', function(e) {
                e.preventDefault();
                var btn = $(this);
                var url = btn.data('url');
                var supplierName = btn.data('name') || 'this supplier';

                Swal.fire({
                    title: 'Delete Supplier?',
                    text: `Are you sure you want to delete "${supplierName}"? You won't be able to revert this!`,
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
                                    toastr.success(response.message || 'Supplier deleted successfully.');
                                    dataTable.ajax.reload(null, false);
                                } else {
                                    toastr.error(response.message || 'Failed to delete supplier.');
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete supplier. Please try again.';
                                toastr.error(errMsg);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
