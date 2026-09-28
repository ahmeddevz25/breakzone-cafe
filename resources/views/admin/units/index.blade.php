@extends('admin.layouts')
@section('title', 'Units Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Units Management</h5>
                        <div class="d-flex align-items-center gap-2">
                            <select id="statusFilter" class="form-select form-select-sm" style="width: 140px;">
                                <option value="">All Status</option>
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                            @can('unit add')
                                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#unitModal" onclick="resetUnitForm()">
                                    <i class="bx bx-plus icon-sm"></i>
                                    <span class="d-none d-sm-inline-block">Add New Unit</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Units Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100" id="unitsTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Unit Name</th>
                                    <th>Quantity</th>
                                    <th class="text-center" style="width: 120px;">Status</th>
                                    <th class="text-center" style="width: 120px;">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Unit Modal (Add / Edit) -->
                    <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="unitForm" action="{{ route('units.store') }}" method="POST" data-ajax-table="#unitsTable">
                                    @csrf
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title fw-bold" id="unitModalLabel">Add New Unit</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- Unit Name --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                                            <input type="text" name="unit" id="unitName" class="form-control" placeholder="e.g. Kg, Pcs, Box, Liter" required>
                                        </div>

                                        {{-- Quantity --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Quantity / Value</label>
                                            <input type="number" step="any" name="quantity" id="unitQuantity" class="form-control" value="0">
                                        </div>

                                        {{-- Position --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Position / Order</label>
                                            <input type="number" name="position" id="unitPosition" class="form-control" value="0">
                                        </div>

                                        {{-- Status --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="unitStatus" class="form-select" required>
                                                <option value="A">Active</option>
                                                <option value="I">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">Save Unit</button>
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
            const dataTable = $('#unitsTable').DataTable({
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
                        console.error('Units DataTable Error:', error);
                    }
                },
                dom: 'lfrtip',
                columns: [
                    { data: 'index', name: 'index', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'unit', name: 'unit' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search units...",
                    lengthMenu: "Show _MENU_ entries",
                    processing: '<div class="d-flex justify-content-center align-items-center py-2 text-primary"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading units...</div>',
                    emptyTable: '<div class="text-center text-muted py-4"><i class="bx bx-ruler fs-2 d-block mb-1"></i> No units found</div>',
                    zeroRecords: '<div class="text-center text-muted py-4"><i class="bx bx-search-alt fs-2 d-block mb-1"></i> No matching units found</div>',
                    info: "Showing _START_ to _END_ of _TOTAL_ units",
                    infoEmpty: "Showing 0 to 0 of 0 units",
                    infoFiltered: "(filtered from _MAX_ total units)"
                }
            });

            // Trigger reload on custom status filter change
            $('#statusFilter').on('change', function() {
                dataTable.ajax.reload();
            });

            const unitForm = document.getElementById('unitForm');
            const unitRoute = "{{ route('units.store') }}";

            // Reset modal for Add Unit
            window.resetUnitForm = function() {
                unitForm.action = unitRoute;
                $('#unitModalLabel').text('Add New Unit');
                $('#submitBtn').text('Save Unit');
                unitForm.reset();
                $('#unitPosition').val("0");
                $('#unitQuantity').val("0");
                $('#unitStatus').val("A");
                $(unitForm).find('.is-invalid').removeClass('is-invalid');
                $(unitForm).find('.invalid-feedback').remove();
            };

            // Edit Unit Handler (Delegated for AJAX-rendered rows)
            $(document).on('click', '.edit-unit-btn', function() {
                var btn = $(this);
                var id = btn.data('id');
                var unit = btn.data('unit');
                var quantity = btn.data('quantity');
                var position = btn.data('position');
                var status = btn.data('status');

                if (status === "active" || status === "1" || status === "A") {
                    status = "A";
                } else {
                    status = "I";
                }

                unitForm.action = `/units/${id}/update`;
                $('#unitModalLabel').text('Edit Unit');
                $('#submitBtn').text('Update Unit');

                $('#unitName').val(unit || '');
                $('#unitQuantity').val(quantity || '0');
                $('#unitPosition').val(position || '0');
                $('#unitStatus').val(status);

                $(unitForm).find('.is-invalid').removeClass('is-invalid');
                $(unitForm).find('.invalid-feedback').remove();

                var modalEl = document.getElementById('unitModal');
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.show();
            });

            // AJAX Delete Unit Handler with SweetAlert2 Confirmation
            $(document).on('click', '.delete-unit-ajax-btn', function(e) {
                e.preventDefault();
                var btn = $(this);
                var url = btn.data('url');
                var unitName = btn.data('name') || 'this unit';

                Swal.fire({
                    title: 'Delete Unit?',
                    text: `Are you sure you want to delete "${unitName}"? You won't be able to revert this!`,
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
                                    toastr.success(response.message || 'Unit deleted successfully.');
                                    dataTable.ajax.reload(null, false);
                                } else {
                                    toastr.error(response.message || 'Failed to delete unit.');
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete unit. Please try again.';
                                toastr.error(errMsg);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
