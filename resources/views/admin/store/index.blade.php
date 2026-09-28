@extends('admin.layouts')
@section('title', 'Store Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Store Management</h5>
                        <div class="d-flex align-items-center gap-2">
                            <select id="statusFilter" class="form-select form-select-sm" style="width: 140px;">
                                <option value="">All Status</option>
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                            @can('store add')
                                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#storeModal" onclick="resetStoreForm()">
                                    <i class="bx bx-plus icon-sm"></i>
                                    <span class="d-none d-sm-inline-block">Add Store</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Stores Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100" id="storesTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Store</th>
                                    <th>Printer IP</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-center" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Store Modal (Add / Edit) -->
                    <div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="storeForm" action="{{ route('stores.store') }}" method="POST" data-ajax-table="#storesTable">
                                    @csrf
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title fw-bold" id="storeModalLabel">Add New Store</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- Store Name --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Store <span class="text-danger">*</span></label>
                                            <input type="text" name="store" id="storeName" class="form-control" placeholder="Store Name" required>
                                        </div>

                                        <div class="row">
                                            {{-- Printer IP --}}
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-semibold">Printer IP Address</label>
                                                <input type="text" name="printer_ip_address" id="printerIp" class="form-control font-monospace" placeholder="192.168.1.100">
                                            </div>

                                            {{-- Printer Port --}}
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-semibold">Port</label>
                                                <input type="text" name="printer_port" id="printerPort" class="form-control font-monospace" value="9100">
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- Opening Balance --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Opening Balance</label>
                                                <input type="number" name="opening_balance" id="openingBalance" class="form-control" value="0">
                                            </div>

                                            {{-- Position / Order --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Position / Order</label>
                                                <input type="number" name="position" id="storePosition" class="form-control" value="0">
                                            </div>
                                        </div>

                                        {{-- Status --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="storeStatus" class="form-select" required>
                                                <option value="A">Active</option>
                                                <option value="I">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">Save Store</button>
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
            const dataTable = $('#storesTable').DataTable({
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
                        console.error('Stores DataTable Error:', error, xhr.responseText);
                    }
                },
                dom: 'lfrtip',
                columns: [
                    { data: 'index', name: 'index', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'store', name: 'store' },
                    { data: 'printer_ip', name: 'printer_ip' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search stores...",
                    lengthMenu: "Show _MENU_ entries",
                    processing: '<div class="d-flex justify-content-center align-items-center py-2 text-primary"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading stores...</div>',
                    emptyTable: '<div class="text-center text-muted py-4"><i class="bx bx-store-alt fs-2 d-block mb-1"></i> No stores found</div>',
                    zeroRecords: '<div class="text-center text-muted py-4"><i class="bx bx-search-alt fs-2 d-block mb-1"></i> No matching stores found</div>',
                    info: "Showing _START_ to _END_ of _TOTAL_ stores",
                    infoEmpty: "Showing 0 to 0 of 0 stores",
                    infoFiltered: "(filtered from _MAX_ total stores)"
                }
            });

            // Trigger reload on custom status filter change
            $('#statusFilter').on('change', function() {
                dataTable.ajax.reload();
            });

            const storeForm = document.getElementById('storeForm');
            const storeRoute = "{{ route('stores.store') }}";

            // Reset modal for Add Store
            window.resetStoreForm = function() {
                storeForm.action = storeRoute;
                $('#storeModalLabel').text('Add New Store');
                $('#submitBtn').text('Save Store');
                storeForm.reset();
                $('#printerPort').val("9100");
                $('#openingBalance').val("0");
                $('#storePosition').val("0");
                $('#storeStatus').val("A");
                $(storeForm).find('.is-invalid').removeClass('is-invalid');
                $(storeForm).find('.invalid-feedback').remove();
            };

            // Edit Store Handler (Delegated for AJAX-rendered rows)
            $(document).on('click', '.edit-store-btn', function() {
                var btn = $(this);
                var id = btn.data('id');
                var store = btn.data('store');
                var ip = btn.data('ip');
                var port = btn.data('port');
                var balance = btn.data('balance');
                var position = btn.data('position');
                var status = btn.data('status');

                if (status === "active" || status === "1" || status === "A") {
                    status = "A";
                } else {
                    status = "I";
                }

                storeForm.action = `/stores/${id}/update`;
                $('#storeModalLabel').text('Edit Store');
                $('#submitBtn').text('Update Store');

                $('#storeName').val(store || '');
                $('#printerIp').val(ip || '');
                $('#printerPort').val(port || '9100');
                $('#openingBalance').val(balance || '0');
                $('#storePosition').val(position || '0');
                $('#storeStatus').val(status);

                $(storeForm).find('.is-invalid').removeClass('is-invalid');
                $(storeForm).find('.invalid-feedback').remove();

                var modalEl = document.getElementById('storeModal');
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.show();
            });

            // AJAX Delete Store Handler with SweetAlert2 Confirmation
            $(document).on('click', '.delete-store-ajax-btn', function(e) {
                e.preventDefault();
                var btn = $(this);
                var url = btn.data('url');
                var storeName = btn.data('name') || 'this store';

                Swal.fire({
                    title: 'Delete Store?',
                    text: `Are you sure you want to delete "${storeName}"? You won't be able to revert this!`,
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
                                    toastr.success(response.message || 'Store deleted successfully.');
                                    dataTable.ajax.reload(null, false);
                                } else {
                                    toastr.error(response.message || 'Failed to delete store.');
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete store. Please try again.';
                                toastr.error(errMsg);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
