@extends('admin.layouts')
@section('title', 'Purchase Returns Management')
@section('content')

    @include('sweetalert::alert')

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            width: 100% !important;
            height: 38px !important;
        }
        .select2-container .selection {
            display: block !important;
            height: 38px !important;
        }
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #d9dee3 !important;
            border-radius: 0.375rem !important;
            display: flex !important;
            align-items: center !important;
            background-color: #fff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #435971 !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            line-height: 36px !important;
            padding-left: 0.85rem !important;
        }
        .select2-dropdown {
            border: 1px solid #d9dee3 !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
            z-index: 1070 !important;
        }

        .return-qty-input {
            width: 110px;
            font-weight: 700;
            text-align: right;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 8px;
            transition: border-color 0.15s ease;
        }
        .return-qty-input:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
            outline: none;
        }
        .return-qty-input.has-value {
            border-color: #ea580c;
            background-color: #fff7ed;
            color: #9a3412;
        }
    </style>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Purchase Returns Management</h5>
                        <div class="d-flex align-items-center gap-2">
                            @can('purchase return add')
                                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#returnModal" onclick="resetReturnForm()">
                                    <i class="bx bx-plus icon-sm"></i>
                                    <span class="d-none d-sm-inline-block">Add New Purchase Return</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Purchase Returns Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100 mb-0" id="returnsTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Return #</th>
                                    <th>PO / Invoice #</th>
                                    <th>Store</th>
                                    <th>Supplier</th>
                                    <th>Return Date</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-center" style="width: 120px;">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Purchase Return Modal (Add / Landscape Mode) -->
                    <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 1400px; width: 96%; margin: 1.5rem auto;">
                            <div class="modal-content">
                                <form id="purchaseReturnForm" action="{{ route('purchase-returns.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                                        <h5 class="modal-title fw-bold m-0" id="returnModalLabel">Add New Purchase Return</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                            style="position: static !important; margin: 0 !important; transform: none !important; box-shadow: none !important;"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- SECTION 1: Return Info (Landscape Grid) --}}
                                        <div class="mb-4">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark mb-1">Store <span class="text-danger">*</span></label>
                                                    <select name="store_id" id="returnStore" class="form-select" onchange="window.loadPurchasesDropdown()" required>
                                                        <option value="">Select Store</option>
                                                        @foreach ($stores as $store)
                                                            <option value="{{ $store->id }}">
                                                                {{ $store->store ?? $store->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label fw-semibold text-dark mb-0">Supplier <span class="text-danger">*</span></label>
                                                        <span id="supplierSpinner" class="spinner-border spinner-border-sm text-primary d-none"></span>
                                                    </div>
                                                    <select name="supplier_id" id="returnSupplier" class="form-select" onchange="window.loadPurchasesDropdown()" required>
                                                        <option value="">Select Supplier</option>
                                                        @foreach ($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}">
                                                                {{ $supplier->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label fw-semibold text-dark mb-0">Purchase # <span class="text-danger">*</span></label>
                                                        <span id="purchaseSpinner" class="spinner-border spinner-border-sm text-primary d-none"></span>
                                                    </div>
                                                    <select name="purchase_id" id="returnPurchase" class="form-select" onchange="window.handlePurchaseSelect(this.value)" required disabled>
                                                        <option value="">Select Store & Supplier first</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark mb-1">Return Date <span class="text-danger">*</span></label>
                                                    <input type="date" name="return_date" id="returnDate" class="form-control"
                                                        value="{{ date('Y-m-d') }}" required>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold text-dark">Notes / Reason for Return</label>
                                                    <input type="text" name="reason" id="returnReason" class="form-control"
                                                        placeholder="e.g. Excess stock ordered, goods damaged in transit, or quality issue...">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SECTION 2: Return Items Table --}}
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 fs-6">
                                                    <i class="bx bx-package text-primary font-size-20"></i> Returnable Items from Selected Purchase
                                                </h6>
                                                <span class="badge bg-label-info fw-semibold" id="purchaseSummaryBadge"></span>
                                            </div>

                                            <!-- Loading State for Items -->
                                            <div id="itemsLoadingBox" class="text-center py-4 d-none bg-white border rounded">
                                                <div class="spinner-border text-primary spinner-border-sm mb-2" role="status"></div>
                                                <div class="text-muted small fw-semibold">Loading purchased items...</div>
                                            </div>

                                            <!-- Placeholder when No Purchase is selected -->
                                            <div id="itemsPlaceholderBox" class="text-center py-4 bg-white border rounded">
                                                <i class="bx bx-cart text-muted font-size-36 opacity-50 mb-1"></i>
                                                <div class="fw-semibold text-dark small">No Purchase Order Selected</div>
                                                <small class="text-muted">Please select Store, Supplier, and Purchase # above to load returnable items.</small>
                                            </div>

                                            <!-- Table Container -->
                                            <div id="itemsContainer" class="d-none">
                                                <div class="table-responsive bg-white border rounded">
                                                    <table class="table table-bordered align-middle mb-0" id="returnItemsTable">
                                                        <thead class="table-light">
                                                            <tr class="text-dark small text-uppercase fw-bold">
                                                                <th style="width: 45px;" class="text-center">#</th>
                                                                <th style="min-width: 280px;">Item / Ingredient / Food</th>
                                                                <th style="width: 90px;" class="text-center">Unit</th>
                                                                <th style="width: 110px;" class="text-end">Purchased Qty</th>
                                                                <th style="width: 110px;" class="text-end">Prev Returned</th>
                                                                <th style="width: 110px;" class="text-end">Available</th>
                                                                <th style="width: 130px;" class="text-end">Return Qty</th>
                                                                <th style="width: 120px;" class="text-end">Unit Price</th>
                                                                <th style="width: 130px;" class="text-end">Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="returnItemsTbody">
                                                            {{-- Populated dynamically via JS --}}
                                                        </tbody>
                                                        <tfoot class="table-light fw-bold">
                                                            <tr style="background-color: #f8fafc;">
                                                                <td colspan="6" class="text-dark fw-bold">Grand Total Return Amount</td>
                                                                <td class="text-end" colspan="3">
                                                                    <span class="text-danger fw-bold fs-6" id="displayTotalReturn">Rs. 0.00</span>
                                                                    <input type="hidden" name="total_amount" id="inputTotalAmount" value="0">
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-between align-items-center">
                                        <div class="fw-bold fs-5 text-dark">
                                            Total Return: <span class="text-danger fw-bolder" id="footerTotalDisplay">Rs. 0.00</span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" id="saveReturnBtn">
                                                <i class="bx bx-check"></i>
                                                <span>Save Purchase Return</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- View Return Details Modal -->
                    <div class="modal fade" id="viewReturnModal" tabindex="-1" aria-labelledby="viewReturnModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-light border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                                    <h5 class="modal-title fw-bold text-dark m-0" id="viewReturnModalLabel">
                                        <i class="bx bx-show-alt text-primary me-1"></i> Purchase Return Details
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4" id="viewReturnModalBody">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status"></div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light py-2 px-4">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 & SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Global Modal & Calculation Helpers (Available immediately)
        window.resetItemsContainer = function() {
            $('#itemsContainer').addClass('d-none');
            $('#itemsLoadingBox').addClass('d-none');
            $('#itemsPlaceholderBox').removeClass('d-none');
            $('#returnItemsTbody').html('');
            $('#displayTotalReturn').text('Rs. 0.00');
            $('#footerTotalDisplay').text('Rs. 0.00');
            $('#inputTotalAmount').val('0');
        };

        window.calculateTotalReturn = function() {
            let grandTotal = 0;
            $('.return-qty-input').each(function() {
                const val = parseFloat($(this).val()) || 0;
                const price = parseFloat($(this).data('price')) || 0;
                if (val > 0) {
                    grandTotal += (val * price);
                }
            });
            const formatted = `Rs. ${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            $('#displayTotalReturn').text(formatted);
            $('#footerTotalDisplay').text(formatted);
            $('#inputTotalAmount').val(grandTotal.toFixed(2));
        };

        window.renderPurchaseItems = function(purchase) {
            $('#purchaseSummaryBadge').text(`Invoice: ${purchase.po_no} | Original Total: Rs. ${parseFloat(purchase.total).toLocaleString('en-US', {minimumFractionDigits: 2})}`);

            let tbodyHtml = '';
            purchase.items.forEach(function(item, idx) {
                const rowIdx = idx + 1;
                const isAvailable = item.available_to_return > 0;
                const badgeClass = item.product_type === 'item' ? 'bg-label-primary' : (item.product_type === 'ingredient' ? 'bg-label-success' : 'bg-label-info');

                tbodyHtml += `
                    <tr class="${!isAvailable ? 'table-light opacity-75' : ''}">
                        <td class="text-center fw-bold text-muted">${rowIdx}</td>
                        <td>
                            <div class="fw-bold text-dark">${item.name}</div>
                            <span class="badge ${badgeClass} fs-tiny text-uppercase">${item.product_type}</span>
                        </td>
                        <td class="text-center font-monospace">${item.unit_name}</td>
                        <td class="text-end fw-semibold">${parseFloat(item.purchased_qty).toFixed(2)}</td>
                        <td class="text-end text-muted">${parseFloat(item.already_returned_qty).toFixed(2)}</td>
                        <td class="text-end fw-bold ${isAvailable ? 'text-success' : 'text-danger'}">${parseFloat(item.available_to_return).toFixed(2)}</td>
                        <td class="text-end">
                            <input type="hidden" name="items[${idx}][purchase_detail_id]" value="${item.purchase_detail_id}">
                            <input type="hidden" name="items[${idx}][price]" value="${item.unit_price}">
                            <input type="number" 
                                name="items[${idx}][return_qty]" 
                                class="form-control form-control-sm return-qty-input d-inline-block" 
                                data-max="${item.available_to_return}"
                                data-price="${item.unit_price}"
                                data-idx="${idx}"
                                min="0" 
                                max="${item.available_to_return}" 
                                step="any" 
                                value="0" 
                                ${!isAvailable ? 'disabled' : ''} 
                                placeholder="0">
                        </td>
                        <td class="text-end fw-medium font-monospace">Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td class="text-end fw-bold font-monospace text-dark item-subtotal" id="subtotal-${idx}">Rs. 0.00</td>
                    </tr>
                `;
            });

            $('#returnItemsTbody').html(tbodyHtml);
            $('#itemsContainer').removeClass('d-none');
            $('#itemsPlaceholderBox').addClass('d-none');
            window.calculateTotalReturn();
        };

        window.resetReturnForm = function() {
            const form = document.getElementById('purchaseReturnForm');
            if (form) form.reset();
            $('#returnStore').val('');
            $('#returnSupplier').val('');
            $('#returnPurchase').html('<option value="">Select Store & Supplier first</option>').prop('disabled', true);
            window.resetItemsContainer();
        };

        window.loadPurchasesDropdown = function() {
            const storeId = $('#returnStore').val();
            const supplierId = $('#returnSupplier').val();

            if (!storeId && !supplierId) {
                $('#returnPurchase').html('<option value="">Select Store & Supplier first</option>').prop('disabled', true);
                window.resetItemsContainer();
                return;
            }

            $('#purchaseSpinner').removeClass('d-none');
            $('#returnPurchase').prop('disabled', true).html('<option value="">Loading purchases...</option>');

            $.ajax({
                url: "{{ route('purchase-returns.purchases') }}",
                type: "GET",
                data: { store_id: storeId, supplier_id: supplierId },
                success: function(res) {
                    $('#purchaseSpinner').addClass('d-none');
                    if (res.status && res.purchases && res.purchases.length > 0) {
                        let options = '<option value="">-- Select Purchase # --</option>';
                        res.purchases.forEach(function(p) {
                            options += `<option value="${p.id}">${p.po_no}</option>`;
                        });
                        $('#returnPurchase').html(options).prop('disabled', false);
                    } else {
                        $('#returnPurchase').html('<option value="">No purchases found for selected criteria</option>').prop('disabled', true);
                        window.resetItemsContainer();
                    }
                },
                error: function() {
                    $('#purchaseSpinner').addClass('d-none');
                    $('#returnPurchase').html('<option value="">Error loading purchases</option>').prop('disabled', true);
                }
            });
        };

        window.handlePurchaseSelect = function(purchaseId) {
            if (!purchaseId) {
                window.resetItemsContainer();
                return;
            }

            $('#itemsPlaceholderBox').addClass('d-none');
            $('#itemsContainer').addClass('d-none');
            $('#itemsLoadingBox').removeClass('d-none');

            $.ajax({
                url: "{{ url('purchase-returns/purchase-items') }}/" + purchaseId,
                type: "GET",
                success: function(res) {
                    $('#itemsLoadingBox').addClass('d-none');
                    if (res.status && res.purchase && res.purchase.items && res.purchase.items.length > 0) {
                        window.renderPurchaseItems(res.purchase);
                    } else {
                        $('#itemsPlaceholderBox').removeClass('d-none');
                        $('#itemsContainer').addClass('d-none');
                        Swal.fire({
                            icon: 'info',
                            title: 'No Items Found',
                            text: 'This purchase has no items or all items have already been returned.'
                        });
                    }
                },
                error: function(xhr) {
                    $('#itemsLoadingBox').addClass('d-none');
                    $('#itemsPlaceholderBox').removeClass('d-none');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load purchase items.'
                    });
                }
            });
        };

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTables
            const returnsTable = $('#returnsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('purchase-returns.index') }}",
                    type: "GET",
                    error: function(xhr, error, code) {
                        console.error('DataTables error:', error, code);
                    }
                },
                columns: [
                    { data: 'index', name: 'index', className: 'text-center', orderable: false, searchable: false },
                    { data: 'return_no', name: 'return_no' },
                    { data: 'po_no', name: 'purchase.po_no' },
                    { data: 'store', name: 'store.store' },
                    { data: 'supplier', name: 'supplier.name' },
                    { data: 'return_date', name: 'return_date' },
                    { data: 'items_count', name: 'items_count', className: 'text-center', orderable: false, searchable: false },
                    { data: 'total_amount', name: 'total_amount', className: 'text-end fw-bold' },
                    { data: 'options', name: 'options', className: 'text-center', orderable: false, searchable: false }
                ],
                order: [[1, 'desc']],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search Return #, PO, Store...",
                    processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading returns...'
                }
            });

            $('#returnStore, #returnSupplier').on('change', function() {
                window.loadPurchasesDropdown();
            });

            $('#returnPurchase').on('change', function() {
                window.handlePurchaseSelect($(this).val());
            });

            $('#returnModal').on('shown.bs.modal', function() {
                const storeId = $('#returnStore').val();
                const supplierId = $('#returnSupplier').val();
                if (storeId || supplierId) {
                    window.loadPurchasesDropdown();
                }
            });

            // Real-time Subtotal & Total Return Calculation
            $(document).on('input change', '.return-qty-input', function() {
                const $input = $(this);
                const max = parseFloat($input.data('max')) || 0;
                const price = parseFloat($input.data('price')) || 0;
                const idx = $input.data('idx');
                let val = parseFloat($input.val()) || 0;

                if (val < 0) {
                    val = 0;
                    $input.val(0);
                }

                if (val > max) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Quantity Exceeded',
                        text: `Return quantity cannot exceed available returnable quantity (${max}).`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    val = max;
                    $input.val(max);
                }

                if (val > 0) {
                    $input.addClass('has-value');
                } else {
                    $input.removeClass('has-value');
                }

                const subtotal = val * price;
                $(`#subtotal-${idx}`).text(`Rs. ${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);

                window.calculateTotalReturn();
            });

            // Submit Form via AJAX
            $('#purchaseReturnForm').on('submit', function(e) {
                e.preventDefault();

                let hasValidReturn = false;
                $('.return-qty-input').each(function() {
                    if (parseFloat($(this).val()) > 0) {
                        hasValidReturn = true;
                    }
                });

                if (!hasValidReturn) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Return Quantity',
                        text: 'Please enter a return quantity greater than 0 for at least one item.'
                    });
                    return;
                }

                const submitBtn = $('#saveReturnBtn');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving & Deducting Stock...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        submitBtn.prop('disabled', false).html('<i class="bx bx-check"></i><span>Save Purchase Return</span>');
                        if (res.status) {
                            $('#returnModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: res.message || 'Purchase Return recorded and inventory stock deducted successfully!'
                            });
                            returnsTable.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message || 'Failed to save purchase return.'
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html('<i class="bx bx-check"></i><span>Save Purchase Return</span>');
                        let errMsg = 'Something went wrong while processing the return.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: errMsg
                        });
                    }
                });
            });

            // View Return Details Modal Handler
            $(document).on('click', '.view-return-btn', function() {
                const id = $(this).data('id');
                $('#viewReturnModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
                $('#viewReturnModal').modal('show');

                $.ajax({
                    url: "{{ url('purchase-returns') }}/" + id + "/show",
                    type: 'GET',
                    success: function(res) {
                        if (res.status && res.return) {
                            const r = res.return;
                            let itemsRows = '';
                            r.items.forEach(function(item, idx) {
                                itemsRows += `
                                    <tr>
                                        <td class="text-center">${idx + 1}</td>
                                        <td>
                                            <div class="fw-bold">${item.name}</div>
                                            <span class="badge bg-label-secondary fs-tiny">${item.type}</span>
                                        </td>
                                        <td class="text-center">${item.unit}</td>
                                        <td class="text-end fw-bold text-danger">${parseFloat(item.quantity).toFixed(2)}</td>
                                        <td class="text-end">Rs. ${parseFloat(item.price).toFixed(2)}</td>
                                        <td class="text-end fw-bold">Rs. ${parseFloat(item.total_price).toFixed(2)}</td>
                                    </tr>
                                `;
                            });

                            const modalHtml = `
                                <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                                    <div class="col-sm-4">
                                        <span class="text-muted small d-block">Return Invoice #</span>
                                        <span class="fw-bold fs-6 text-dark">${r.return_no}</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="text-muted small d-block">Original PO #</span>
                                        <span class="fw-bold fs-6 text-primary">${r.po_no}</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="text-muted small d-block">Return Date</span>
                                        <span class="fw-bold fs-6 text-dark">${r.return_date}</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="text-muted small d-block">Store</span>
                                        <span class="fw-bold text-dark">${r.store_name}</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="text-muted small d-block">Supplier</span>
                                        <span class="fw-bold text-dark">${r.supplier_name}</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="text-muted small d-block">Processed By</span>
                                        <span class="fw-bold text-dark">${r.created_by}</span>
                                    </div>
                                    ${r.reason ? `
                                        <div class="col-12 border-top pt-2 mt-2">
                                            <span class="text-muted small d-block">Reason / Remarks:</span>
                                            <span class="text-dark fst-italic">${r.reason}</span>
                                        </div>
                                    ` : ''}
                                </div>

                                <h6 class="fw-bold text-dark mb-2">Returned Items & Stock Deductions</h6>
                                <div class="table-responsive border rounded-3 mb-3">
                                    <table class="table table-sm table-striped m-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">#</th>
                                                <th>Item Name</th>
                                                <th class="text-center">Unit</th>
                                                <th class="text-end">Returned Qty</th>
                                                <th class="text-end">Unit Price</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${itemsRows}
                                        </tbody>
                                        <tfoot class="table-light border-top">
                                            <tr>
                                                <th colspan="5" class="text-end fw-bold">Total Return Amount:</th>
                                                <th class="text-end fw-bold text-danger fs-6">Rs. ${parseFloat(r.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            `;

                            $('#viewReturnModalBody').html(modalHtml);
                        } else {
                            $('#viewReturnModalBody').html('<div class="alert alert-danger">Failed to load return details.</div>');
                        }
                    },
                    error: function() {
                        $('#viewReturnModalBody').html('<div class="alert alert-danger">Error retrieving return information.</div>');
                    }
                });
            });

            // Delete Return & Restore Stock
            $(document).on('click', '.delete-return-btn', function() {
                const id = $(this).data('id');
                const returnNo = $(this).data('return-no') || 'Record';

                Swal.fire({
                    title: `Delete Return?`,
                    text: 'Deleting this return will automatically RESTORE the returned quantities back into inventory stock!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6e7881',
                    confirmButtonText: 'Yes, Restore Stock & Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('purchase-returns') }}/" + id + "/delete",
                            type: 'DELETE',
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire('Deleted!', res.message, 'success');
                                    returnsTable.ajax.reload(null, false);
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                let errMsg = 'Failed to delete purchase return.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errMsg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', errMsg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
