@extends('admin.layouts')
@section('title', 'Combo Products Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Combo Products Management</h5>
                        <div class="d-flex align-items-center gap-2">
                            <select id="statusFilter" class="form-select form-select-sm" style="width: 140px;">
                                <option value="">All Status</option>
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                            @can('combo product add')
                                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#comboModal" onclick="resetComboForm()">
                                    <i class="bx bx-plus icon-sm"></i>
                                    <span class="d-none d-sm-inline-block">Add New Combo Product</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Combo Products Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100" id="comboProductsTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Name</th>
                                    <th class="text-end" style="width: 160px;">Price</th>
                                    <th class="text-center" style="width: 110px;">Status</th>
                                    <th class="text-center" style="width: 130px;">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Combo Modal (Add / Edit) - Landscape Layout like Purchases Module -->
                    <div class="modal fade" id="comboModal" tabindex="-1" aria-labelledby="comboModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 1350px; width: 95%; margin: 1.5rem auto;">
                            <div class="modal-content">
                                <form id="comboForm" action="{{ route('combo-products.store') }}" method="POST" enctype="multipart/form-data" data-ajax-table="#comboProductsTable">
                                    @csrf
                                    <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                                        <h5 class="modal-title fw-bold m-0" id="comboModalLabel">Add New Combo Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                            style="position: static !important; margin: 0 !important; transform: none !important; box-shadow: none !important;"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- SECTION 1: Combo Information (Landscape Top Grid like Purchases) --}}
                                        <div class="mb-4">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-dark">Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" id="comboName" class="form-control"
                                                        placeholder="e.g. Wednesday Lunch Box" required>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Price</label>
                                                    <input type="number" step="any" min="0" name="price" id="comboPrice"
                                                        class="form-control" placeholder="0.00">
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="form-label fw-semibold text-dark">Status <span class="text-danger">*</span></label>
                                                    <select name="status" id="comboStatus" class="form-select" required>
                                                        <option value="A">Active</option>
                                                        <option value="I">Inactive</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Picture</label>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file" name="picture" id="comboPicture" class="form-control" accept="image/*">
                                                        <div id="comboPicturePreviewContainer" class="d-none flex-shrink-0">
                                                            <img id="comboPicturePreview" src="" alt="Preview" style="height: 38px; width: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SECTION 2: Combo Line Items Table (Matches Purchases Module) --}}
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 fs-6">
                                                    <i class="bx bx-package text-primary font-size-20"></i> Combo Line Items
                                                </h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="addItemBtn">
                                                        <i class="bx bx-plus"></i> Add Item
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning btn-sm d-flex align-items-center gap-1" id="addFoodBtn">
                                                        <i class="bx bx-plus"></i> Add Food
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="table-responsive bg-white border rounded">
                                                <table class="table table-bordered align-middle mb-0" id="comboItemsTable">
                                                    <thead class="table-light">
                                                        <tr class="text-dark small text-uppercase fw-bold">
                                                            <th style="width: 45px;" class="text-center">#</th>
                                                            <th style="min-width: 320px;">Items / Food</th>
                                                            <th style="width: 120px;" class="text-center">Unit</th>
                                                            <th style="width: 125px;" class="text-end">Quantity</th>
                                                            <th style="width: 135px;" class="text-end">Sale Price</th>
                                                            <th style="width: 135px;" class="text-end">Total Price</th>
                                                            <th style="width: 125px;" class="text-end">Discount</th>
                                                            <th style="width: 85px;" class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="comboTableBody">
                                                        <!-- Dynamic Rows Injected by JS -->
                                                    </tbody>
                                                    <tfoot class="table-light fw-bold">
                                                        <tr style="background-color: #f8fafc;">
                                                            <td colspan="3" class="text-dark fw-bold ps-3">Grand Total</td>
                                                            <td class="text-end">
                                                                <span id="grandTotalQuantity" class="text-dark fw-bold fs-6">0.00</span>
                                                            </td>
                                                            <td class="text-center text-dark fw-bold">-</td>
                                                            <td class="text-end">
                                                                <span id="grandTotalAmount" class="text-success fw-bold fs-6">Rs 0.00</span>
                                                            </td>
                                                            <td class="text-end">
                                                                <span id="grandTotalDiscount" class="text-dark fw-bold fs-6">0.00</span>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3 px-4 bg-light">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitComboBtn" class="btn btn-primary px-4">Save Combo Product</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Landscape View Combo Details Modal (Matches Purchases View Modal) -->
                    <div class="modal fade" id="viewComboModal" tabindex="-1" aria-labelledby="viewComboModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 1200px; width: 95%; margin: 1.5rem auto;">
                            <div class="modal-content">
                                <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bx bx-package text-primary fs-4"></i>
                                        <h5 class="modal-title fw-bold m-0" id="viewComboModalLabel">Combo Product Details</h5>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                        style="position: static !important; margin: 0 !important; transform: none !important; box-shadow: none !important;"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3 mb-4 pb-3 border-bottom">
                                        <div class="col-md-2 text-center" id="v_pic_col">
                                            <img id="v_picture" src="" alt="Combo Image" style="max-height: 100px; max-width: 100%; object-fit: contain; border-radius: 6px; border: 1px solid #ddd;">
                                            <div id="v_no_picture" class="bg-light p-3 text-muted small rounded border">No Image</div>
                                        </div>
                                        <div class="col-md-10 d-flex flex-column justify-content-center">
                                            <h4 class="fw-bold text-dark mb-1" id="v_name"></h4>
                                            <div class="d-flex flex-wrap align-items-center gap-3 mt-2">
                                                <span class="fs-6">Combo Price: <span class="text-success fw-bold fs-5" id="v_price"></span></span>
                                                <span class="fs-6">Status: <span id="v_status_badge"></span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-dark mb-2">Included Items / Food Line Items</h6>
                                    <div class="table-responsive border rounded">
                                        <table class="table table-bordered align-middle mb-0">
                                            <thead class="table-light small text-uppercase">
                                                <tr>
                                                    <th style="width: 50px;" class="text-center">#</th>
                                                    <th style="width: 100px;">Type</th>
                                                    <th>Product / Food</th>
                                                    <th class="text-center" style="width: 120px;">Unit</th>
                                                    <th class="text-end" style="width: 120px;">Quantity</th>
                                                    <th class="text-end" style="width: 130px;">Sale Price</th>
                                                    <th class="text-end" style="width: 130px;">Total Price</th>
                                                    <th class="text-end" style="width: 120px;">Discount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="v_items_tbody">
                                                {{-- Dynamically populated --}}
                                            </tbody>
                                            <tfoot class="table-light fw-bold" id="v_items_tfoot">
                                                <tr style="background-color: #f8fafc;">
                                                    <td colspan="4" class="text-dark fw-bold ps-3">Grand Total</td>
                                                    <td class="text-end fw-bold text-dark fs-6" id="v_grand_qty">0</td>
                                                    <td class="text-center text-muted fw-bold">-</td>
                                                    <td class="text-end text-success fs-6 fw-bold" id="v_grand_total">Rs 0.00</td>
                                                    <td class="text-end text-dark fw-bold" id="v_grand_discount">0</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-2 px-4 bg-light">
                                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Select Catalogs (Preloaded for lightning-fast row cloning, just like Purchases) --}}
    <div class="d-none">
        {{-- Items Options --}}
        <select id="itemsCatalogSelect">
            <option value="">Select Item</option>
            @foreach ($items as $it)
                <option value="{{ $it->id }}"
                    data-type="item"
                    data-unit_name="{{ $it->unit ? ($it->unit->unit ?? $it->unit->name) : 'Packet' }}"
                    data-price="{{ $it->sale_price ?? 0 }}">
                    {{ $it->name }}
                </option>
            @endforeach
        </select>

        {{-- Foods Options --}}
        <select id="foodsCatalogSelect">
            <option value="">Select Food</option>
            @foreach ($foods as $fd)
                <option value="{{ $fd->id }}"
                    data-type="food"
                    data-unit_name="Serving"
                    data-price="{{ $fd->price ?? 0 }}">
                    {{ $fd->name }}
                </option>
            @endforeach
        </select>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Combo Products DataTable (Server-Side AJAX)
        const dataTable = $('#comboProductsTable').DataTable({
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
                    console.error('Combo Products DataTable Error:', error, xhr.responseText);
                }
            },
            dom: 'lfrtip',
            columns: [
                { data: 'index', name: 'index', orderable: false, searchable: false, className: 'text-center' },
                { data: 'name', name: 'name' },
                { data: 'price', name: 'price', className: 'text-end' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'options', name: 'options', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search combos...",
                lengthMenu: "Show _MENU_ entries",
                processing: '<div class="d-flex justify-content-center align-items-center py-2 text-primary"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading combos...</div>',
                emptyTable: '<div class="text-center text-muted py-4"><i class="bx bx-package fs-2 d-block mb-1"></i> No combos found</div>',
                zeroRecords: '<div class="text-center text-muted py-4"><i class="bx bx-search-alt fs-2 d-block mb-1"></i> No matching combos found</div>',
                info: "Showing _START_ to _END_ of _TOTAL_ combos",
                infoEmpty: "Showing 0 to 0 of 0 combos",
                infoFiltered: "(filtered from _MAX_ total combos)"
            }
        });

        // Status Filter Change
        $('#statusFilter').on('change', function() {
            dataTable.ajax.reload();
        });

        // Form Elements
        const comboForm = document.getElementById('comboForm');
        const defaultStoreRoute = "{{ route('combo-products.store') }}";
        const comboModalLabel = document.getElementById('comboModalLabel');
        const submitComboBtn = document.getElementById('submitComboBtn');

        const comboName = document.getElementById('comboName');
        const comboPrice = document.getElementById('comboPrice');
        const comboStatus = document.getElementById('comboStatus');
        const comboPicture = document.getElementById('comboPicture');
        const comboPicturePreview = document.getElementById('comboPicturePreview');
        const comboPicturePreviewContainer = document.getElementById('comboPicturePreviewContainer');

        const comboTableBody = document.getElementById('comboTableBody');
        const grandTotalQuantity = document.getElementById('grandTotalQuantity');
        const grandTotalAmount = document.getElementById('grandTotalAmount');
        const grandTotalDiscount = document.getElementById('grandTotalDiscount');

        const itemsCatalogSelect = document.getElementById('itemsCatalogSelect');
        const foodsCatalogSelect = document.getElementById('foodsCatalogSelect');

        // Add Item & Add Food Event Listeners (Just like in Purchases module!)
        document.getElementById('addItemBtn').addEventListener('click', function(e) {
            e.preventDefault();
            addComboRow('item');
        });

        document.getElementById('addFoodBtn').addEventListener('click', function(e) {
            e.preventDefault();
            addComboRow('food');
        });

        // Image Preview
        if (comboPicture) {
            comboPicture.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        comboPicturePreview.src = e.target.result;
                        comboPicturePreviewContainer.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Reset modal form for Add New Combo
        window.resetComboForm = function() {
            comboForm.action = defaultStoreRoute;
            comboForm.reset();
            comboModalLabel.textContent = 'Add New Combo Product';
            submitComboBtn.textContent = 'Save Combo Product';

            comboPicturePreviewContainer.classList.add('d-none');
            comboPicturePreview.src = '';
            comboTableBody.innerHTML = '';

            addComboRow('item');
        };

        // Add Row Function (Matches addPurchaseRow in Purchases module)
        window.addComboRow = function(type = 'item', prefillData = null) {
            const idx = comboTableBody.querySelectorAll('.combo-row').length;
            const tr = document.createElement('tr');
            tr.className = 'combo-row';
            tr.dataset.type = type;

            const isFood = (type === 'food');
            const typeBadge = isFood
                ? '<span class="badge bg-label-warning me-1">Food</span>'
                : '<span class="badge bg-label-info me-1">Item</span>';

            const catalogSelect = isFood ? foodsCatalogSelect : itemsCatalogSelect;
            const selectHtml = `<select name="components[${idx}][product_id]" class="form-select product-select" required>
                ${catalogSelect.innerHTML}
            </select>`;

            const initialQty = prefillData ? prefillData.quantity : 1;
            const initialPrice = prefillData ? parseFloat(prefillData.sale_price).toFixed(2) : '0.00';
            const initialDiscount = prefillData ? parseFloat(prefillData.discount) : 0;
            const initialTotal = prefillData ? parseFloat(prefillData.total_price).toFixed(2) : '0.00';

            tr.innerHTML = `
                <td class="text-center fw-bold text-dark row-index">${idx + 1}</td>
                <td>
                    <input type="hidden" name="components[${idx}][type]" value="${type}">
                    <div class="d-flex align-items-center">
                        ${typeBadge}
                        <div class="flex-grow-1">${selectHtml}</div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="row-unit-badge badge bg-label-primary fs-7">-</span>
                </td>
                <td>
                    <input type="number" step="any" min="0" name="components[${idx}][quantity]"
                        class="form-control row-quantity text-end no-spin" style="padding: 0.4375rem 0.65rem;"
                        value="${initialQty}" placeholder="1">
                </td>
                <td>
                    <input type="number" step="any" min="0" name="components[${idx}][unit_price]"
                        class="form-control row-price text-end no-spin bg-light" style="padding: 0.4375rem 0.65rem;"
                        value="${initialPrice}" readonly>
                </td>
                <td class="text-end">
                    <span class="row-total-price fw-bold text-dark fs-6">${initialTotal}</span>
                </td>
                <td>
                    <input type="number" step="any" min="0" name="components[${idx}][discount]"
                        class="form-control row-discount text-end no-spin" style="padding: 0.4375rem 0.65rem;"
                        value="${initialDiscount}" placeholder="0">
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary row-duplicate-btn" title="Duplicate Row">
                            <i class="bx bx-plus"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger row-remove-btn" title="Remove Row">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </td>
            `;

            comboTableBody.appendChild(tr);
            attachRowEvents(tr);

            if (prefillData) {
                const select = tr.querySelector('.product-select');
                select.value = prefillData.product_id;
                tr.querySelector('.row-unit-badge').textContent = prefillData.unit || '-';
                tr.querySelector('.row-price').value = parseFloat(prefillData.sale_price).toFixed(2);
                calculateRow(tr);
            }

            renumberRows();
        };

        function calculateRow(row) {
            const qtyInput = row.querySelector('.row-quantity');
            const priceInput = row.querySelector('.row-price');
            const discountInput = row.querySelector('.row-discount');
            const totalSpan = row.querySelector('.row-total-price');

            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const lineTotal = Math.max(0, (qty * price) - discount).toFixed(2);

            totalSpan.textContent = lineTotal;
            calculateGrandTotals();
        }

        function calculateGrandTotals() {
            let totalQty = 0;
            let totalAmt = 0;
            let totalDisc = 0;

            comboTableBody.querySelectorAll('.combo-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.row-quantity').value) || 0;
                const amt = parseFloat(row.querySelector('.row-total-price').textContent) || 0;
                const disc = parseFloat(row.querySelector('.row-discount').value) || 0;

                totalQty += qty;
                totalAmt += amt;
                totalDisc += disc;
            });

            grandTotalQuantity.textContent = Number.isInteger(totalQty) ? totalQty : totalQty.toFixed(2);
            grandTotalAmount.textContent = 'Rs ' + totalAmt.toFixed(2);
            grandTotalDiscount.textContent = Number.isInteger(totalDisc) ? totalDisc : totalDisc.toFixed(2);
        }

        function renumberRows() {
            const rows = comboTableBody.querySelectorAll('.combo-row');
            rows.forEach((row, idx) => {
                row.querySelector('.row-index').textContent = idx + 1;
                row.querySelectorAll('select, input').forEach(input => {
                    input.name = input.name.replace(/\[\d+\]/, `[${idx}]`);
                });
            });
            calculateGrandTotals();
        }

        function attachRowEvents(row) {
            const select = row.querySelector('.product-select');
            const unitSpan = row.querySelector('.row-unit-badge');
            const priceInput = row.querySelector('.row-price');
            const qtyInput = row.querySelector('.row-quantity');
            const discountInput = row.querySelector('.row-discount');
            const duplicateBtn = row.querySelector('.row-duplicate-btn');
            const removeBtn = row.querySelector('.row-remove-btn');

            select.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (!selected || !selected.value) {
                    unitSpan.textContent = '-';
                    priceInput.value = '0.00';
                    calculateRow(row);
                    return;
                }

                const unitName = selected.dataset.unit_name || '-';
                const price = parseFloat(selected.dataset.price) || 0;

                unitSpan.textContent = unitName;
                priceInput.value = price.toFixed(2);
                calculateRow(row);
            });

            qtyInput.addEventListener('input', () => calculateRow(row));
            discountInput.addEventListener('input', () => calculateRow(row));

            duplicateBtn.addEventListener('click', function() {
                const type = row.dataset.type || 'item';
                addComboRow(type);
            });

            removeBtn.addEventListener('click', function() {
                if (comboTableBody.querySelectorAll('.combo-row').length > 1) {
                    row.remove();
                    renumberRows();
                } else {
                    toastr.warning('At least one item or food is required in a combo.');
                }
            });
        }

        // Form Validation before Global AJAX Submit
        $(comboForm).on('submit', function(e) {
            let hasValidItem = false;
            comboTableBody.querySelectorAll('.combo-row').forEach(row => {
                const select = row.querySelector('.product-select');
                if (select && select.value) {
                    hasValidItem = true;
                }
            });

            if (!hasValidItem) {
                e.preventDefault();
                e.stopImmediatePropagation();
                toastr.error('Please select at least one item or food in the combo.');
                return false;
            }
        });

        // Edit Functionality
        window.editComboProduct = function(id) {
            fetch(`/combo-products/${id}/details`)
                .then(res => res.json())
                .then(data => {
                    if (!data.status) {
                        toastr.error(data.message || 'Failed to load combo.');
                        return;
                    }

                    const combo = data.combo;
                    comboForm.action = `/combo-products/${id}/update`;
                    comboModalLabel.textContent = 'Edit Combo Product: ' + combo.name;
                    submitComboBtn.textContent = 'Update Combo Product';

                    comboName.value = combo.name;
                    comboPrice.value = combo.price;
                    comboStatus.value = combo.status;

                    if (combo.picture_url) {
                        comboPicturePreview.src = combo.picture_url;
                        comboPicturePreviewContainer.classList.remove('d-none');
                    } else {
                        comboPicturePreviewContainer.classList.add('d-none');
                        comboPicturePreview.src = '';
                    }

                    // Populate rows
                    comboTableBody.innerHTML = '';
                    if (combo.details && combo.details.length > 0) {
                        combo.details.forEach(item => {
                            addComboRow(item.type, item);
                        });
                    } else {
                        addComboRow('item');
                    }

                    const modal = new bootstrap.Modal(document.getElementById('comboModal'));
                    modal.show();
                })
                .catch(err => {
                    console.error('Edit error:', err);
                    toastr.error('Error loading combo details.');
                });
        };

        // Quick View Functionality (Matches Purchases View Modal)
        window.viewComboDetails = function(id) {
            fetch(`/combo-products/${id}/details`)
                .then(res => res.json())
                .then(data => {
                    if (!data.status) {
                        toastr.error(data.message || 'Failed to load details.');
                        return;
                    }

                    const combo = data.combo;
                    document.getElementById('v_name').textContent = combo.name;
                    document.getElementById('v_price').textContent = 'Rs ' + Number(combo.price).toFixed(2);
                    document.getElementById('v_status_badge').innerHTML = (combo.status === 'A')
                        ? '<span class="badge bg-success">ACTIVE</span>'
                        : '<span class="badge bg-danger">INACTIVE</span>';

                    const vPic = document.getElementById('v_picture');
                    const vNoPic = document.getElementById('v_no_picture');
                    if (combo.picture_url) {
                        vPic.src = combo.picture_url;
                        vPic.classList.remove('d-none');
                        vNoPic.classList.add('d-none');
                    } else {
                        vPic.classList.add('d-none');
                        vNoPic.classList.remove('d-none');
                    }

                    const tbody = document.getElementById('v_items_tbody');
                    const tfoot = document.getElementById('v_items_tfoot');
                    tbody.innerHTML = '';

                    let sumQty = 0;
                    let sumTotal = 0;
                    let sumDiscount = 0;

                    if (combo.details && combo.details.length > 0) {
                        combo.details.forEach((item, idx) => {
                            const qty = parseFloat(item.quantity) || 0;
                            const salePrice = parseFloat(item.sale_price) || 0;
                            const totalPrice = parseFloat(item.total_price) || 0;
                            const discount = parseFloat(item.discount) || 0;

                            sumQty += qty;
                            sumTotal += totalPrice;
                            sumDiscount += discount;

                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="text-center fw-bold text-dark">${idx + 1}</td>
                                <td><span class="badge ${item.type === 'item' ? 'bg-label-info' : 'bg-label-warning'}">${item.type === 'item' ? 'Retail Item' : 'Prepared Food'}</span></td>
                                <td class="fw-semibold text-dark">${escapeHtml(item.product_name)}</td>
                                <td class="text-center">${escapeHtml(item.unit)}</td>
                                <td class="text-end fw-bold text-dark">${Number.isInteger(qty) ? qty : qty.toFixed(2)}</td>
                                <td class="text-end">Rs ${salePrice.toFixed(2)}</td>
                                <td class="text-end fw-bold text-dark">Rs ${totalPrice.toFixed(2)}</td>
                                <td class="text-end text-dark">${Number.isInteger(discount) ? discount : discount.toFixed(2)}</td>
                            `;
                            tbody.appendChild(tr);
                        });

                        document.getElementById('v_grand_qty').textContent = Number.isInteger(sumQty) ? sumQty : sumQty.toFixed(2);
                        document.getElementById('v_grand_total').textContent = 'Rs ' + sumTotal.toFixed(2);
                        document.getElementById('v_grand_discount').textContent = Number.isInteger(sumDiscount) ? sumDiscount : sumDiscount.toFixed(2);
                        if (tfoot) tfoot.classList.remove('d-none');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3">No line items found in this combo.</td></tr>';
                        if (tfoot) tfoot.classList.add('d-none');
                    }

                    const modal = new bootstrap.Modal(document.getElementById('viewComboModal'));
                    modal.show();
                })
                .catch(err => {
                    console.error('View error:', err);
                    toastr.error('Error loading combo details.');
                });
        };

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
    });
</script>
@endpush
