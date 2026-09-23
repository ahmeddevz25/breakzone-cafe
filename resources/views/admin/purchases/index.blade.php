@extends('admin.layouts')
@section('title', 'Purchases Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Purchases Management</h5>
                        @can('purchase add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#purchaseModal" onclick="resetPurchaseForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add New Purchase</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Purchases Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top mb-0" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;">#</th>
                                    <th>PO / Invoice #</th>
                                    <th>Store</th>
                                    <th>Supplier</th>
                                    <th>Purchase Date</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-center" style="width: 120px;">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $key => $p)
                                    <tr class="text-dark">
                                        <td class="text-dark fw-medium">{{ $key + 1 }}</td>
                                        <td>
                                            <span class="badge bg-label-dark font-monospace fs-7 px-2 py-1">
                                                {{ $p->po_no ?? 'PO-' . str_pad($p->id, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-dark">
                                            {{ $p->store ? ($p->store->store ?? $p->store->name) : '-' }}
                                        </td>
                                        <td class="text-dark fw-medium">
                                            {{ $p->supplier ? $p->supplier->name : '-' }}
                                        </td>
                                        <td class="text-dark">
                                            {{ $p->purchase_date ? $p->purchase_date->format('Y-m-d') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-primary rounded-pill px-2">
                                                {{ $p->details ? $p->details->count() : 0 }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-success fs-6">
                                            Rs {{ number_format($p->total, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <div class="table-actions justify-content-center">
                                                {{-- View Details --}}
                                                <button type="button" class="action-btn action-btn-view view-purchase-btn"
                                                    data-id="{{ $p->id }}" title="View Purchase Details"
                                                    data-bs-toggle="modal" data-bs-target="#viewPurchaseModal">
                                                    <i class="bx bx-show"></i>
                                                </button>

                                                {{-- Edit --}}
                                                @can('purchase edit')
                                                    <button type="button" class="action-btn action-btn-edit edit-purchase-btn"
                                                        data-id="{{ $p->id }}" title="Edit Purchase"
                                                        data-bs-toggle="modal" data-bs-target="#purchaseModal">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete --}}
                                                @can('purchase delete')
                                                    <a href="{{ route('purchases.delete', $p->id) }}"
                                                        onclick="return confirm('Are you sure you want to delete this purchase? Associated stock increments will be reverted.')"
                                                        class="action-btn action-btn-delete" title="Delete Purchase">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No purchases recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Purchase Modal (Add / Edit) - Landscape Mode -->
                    <div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 1520px; width: 96%; margin: 1.5rem auto;">
                            <div class="modal-content">
                                <form id="purchaseForm" action="{{ route('purchases.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                                        <h5 class="modal-title fw-bold m-0" id="purchaseModalLabel">Add New Purchase</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                            style="position: static !important; margin: 0 !important; transform: none !important; box-shadow: none !important;"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- SECTION 1: Purchase Info (Landscape Grid) --}}
                                        <div class="mb-4">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Store <span class="text-danger">*</span></label>
                                                    <select name="store_id" id="purchaseStore" class="form-select" required>
                                                        <option value="">Select Store</option>
                                                        @foreach ($stores as $store)
                                                            <option value="{{ $store->id }}">
                                                                {{ $store->store ?? $store->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Supplier <span class="text-danger">*</span></label>
                                                    <select name="supplier_id" id="purchaseSupplier" class="form-select" required>
                                                        <option value="">Select Supplier</option>
                                                        @foreach ($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}">
                                                                {{ $supplier->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Purchase Date <span class="text-danger">*</span></label>
                                                    <input type="date" name="purchase_date" id="purchaseDate" class="form-control"
                                                        value="{{ date('Y-m-d') }}" required>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <label class="form-label fw-semibold text-dark mb-0">PO / Invoice #</label>
                                                        <span id="poLoadingIndicator" class="spinner-border spinner-border-sm text-primary d-none"></span>
                                                    </div>
                                                    <input type="text" name="po_no" id="purchasePoNo" class="form-control mt-1"
                                                        value="{{ $nextPoNo }}" placeholder="e.g. PO-20260917-0001">
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold text-dark">Notes / Remarks</label>
                                                    <input type="text" name="notes" id="purchaseNotes" class="form-control" placeholder="Optional remarks or invoice notes...">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SECTION 2: Purchase Line Items Table --}}
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 fs-6">
                                                    <i class="bx bx-cart text-primary font-size-20"></i> Purchase Line Items
                                                </h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="addItemBtn">
                                                        <i class="bx bx-plus"></i> Add Item
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1" id="addIngredientBtn">
                                                        <i class="bx bx-plus"></i> Add Ingredient
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning btn-sm d-flex align-items-center gap-1" id="addFoodBtn">
                                                        <i class="bx bx-plus"></i> Add Food
                                                    </button>
                                                </div>
                                            </div>

                                             <div class="table-responsive bg-white border rounded">
                                                <table class="table table-bordered align-middle mb-0" id="purchaseItemsTable">
                                                    <thead class="table-light">
                                                        <tr class="text-dark small text-uppercase fw-bold">
                                                            <th style="width: 45px;" class="text-center">#</th>
                                                            <th style="min-width: 320px;">Item / Ingredient / Food</th>
                                                            <th style="width: 100px;" class="text-center">Unit</th>
                                                            <th style="width: 125px;" class="text-center">Available Stock</th>
                                                            <th style="width: 115px;" class="text-end">Quantity</th>
                                                            <th style="width: 135px;" class="text-end">Purchase Price</th>
                                                            <th style="width: 135px;" class="text-end">Total Price</th>
                                                            <th style="width: 85px;" class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="purchaseTableBody">
                                                        <!-- Dynamic Rows Injected by JS -->
                                                    </tbody>
                                                    <tfoot class="table-light fw-bold">
                                                        <tr style="background-color: #f8fafc;">
                                                            <td colspan="4" class="text-dark fw-bold">Grand Total</td>
                                                            <td class="text-end">
                                                                <span id="grandTotalQuantity" class="text-dark fw-bold fs-6">0.00</span>
                                                            </td>
                                                            <td class="text-center text-dark fw-bold">-</td>
                                                            <td class="text-end">
                                                                <span id="grandTotalAmount" class="text-success fw-bold fs-6">Rs 0.00</span>
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
                                        <button type="submit" id="submitPurchaseBtn" class="btn btn-primary px-4">Save Purchase</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Landscape View Purchase Details Modal -->
                    <div class="modal fade" id="viewPurchaseModal" tabindex="-1" aria-labelledby="viewPurchaseModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 1520px; width: 96%; margin: 1.5rem auto;">
                            <div class="modal-content">
                                <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bx bx-receipt text-primary fs-4"></i>
                                        <h5 class="modal-title fw-bold m-0" id="viewPurchaseModalLabel">Purchase Details & Receipt</h5>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                        style="position: static !important; margin: 0 !important; transform: none !important; box-shadow: none !important;"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3 mb-4 p-3 bg-light rounded border">
                                        <div class="col-md-3">
                                            <small class="text-dark d-block text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">PO / Invoice #</small>
                                            <span id="v_po_no" class="fw-bold text-dark font-monospace fs-6">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-dark d-block text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Store</small>
                                            <span id="v_store" class="fw-bold text-dark fs-6">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-dark d-block text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Supplier</small>
                                            <span id="v_supplier" class="fw-bold text-dark fs-6">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-dark d-block text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Purchase Date</small>
                                            <span id="v_date" class="fw-bold text-dark fs-6">-</span>
                                        </div>
                                    </div>

                                    <div class="table-responsive border rounded mb-3">
                                        <table class="table table-bordered table-striped align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="text-dark small text-uppercase fw-bold">
                                                    <th style="width: 50px;" class="text-center">#</th>
                                                    <th>Item / Ingredient / Food</th>
                                                    <th style="width: 120px;" class="text-center">Type</th>
                                                    <th style="width: 120px;" class="text-center">Unit</th>
                                                    <th style="width: 130px;" class="text-end">Quantity</th>
                                                    <th style="width: 150px;" class="text-end">Purchase Price</th>
                                                    <th style="width: 160px;" class="text-end">Total Price</th>
                                                </tr>
                                            </thead>
                                            <tbody id="v_items_body">
                                                <!-- Dynamic items -->
                                            </tbody>
                                            <tfoot class="table-light fw-bold">
                                                <tr style="background-color: #f8fafc;">
                                                    <td colspan="4" class="text-dark fw-bold">Grand Total</td>
                                                    <td class="text-end text-dark fw-bold" id="v_grand_qty">0.00</td>
                                                    <td class="text-center text-dark fw-bold">-</td>
                                                    <td class="text-end text-success fs-6 fw-bold" id="v_grand_total">Rs 0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div id="v_notes_container" class="p-3 bg-light rounded border d-none">
                                        <small class="text-dark d-block text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Remarks / Notes</small>
                                        <span id="v_notes" class="text-dark fw-semibold"></span>
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-3 px-4 bg-light">
                                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Catalog Select Templates --}}
    <div id="hiddenCatalogTemplates" class="d-none">
        {{-- Items Options --}}
        <select id="itemsCatalogSelect">
            <option value="">Select Item</option>
            @foreach ($items as $it)
                <option value="{{ $it->id }}"
                    data-type="item"
                    data-unit_id="{{ $it->unit_id ?? '' }}"
                    data-unit_name="{{ $it->unit ? $it->unit->unit : '-' }}"
                    data-stock="{{ max(0, $it->stock ?? 0) }}"
                    data-price="{{ $it->price ?? 0 }}">
                    {{ $it->name }} (Item)
                </option>
            @endforeach
        </select>

        {{-- Ingredients Options --}}
        <select id="ingredientsCatalogSelect">
            <option value="">Select Ingredient</option>
            @foreach ($ingredients as $ing)
                <option value="{{ $ing->id }}"
                    data-type="ingredient"
                    data-unit_id="{{ $ing->buying_unit_id ?? '' }}"
                    data-unit_name="{{ $ing->buyingUnit ? $ing->buyingUnit->unit : '-' }}"
                    data-stock="{{ max(0, $ing->stock ?? 0) }}"
                    data-price="{{ $ing->purchase_price ?? 0 }}">
                    {{ $ing->name }} (Ingredient)
                </option>
            @endforeach
        </select>

        {{-- Foods Options --}}
        <select id="foodsCatalogSelect">
            <option value="">Select Food</option>
            @foreach ($foods as $fd)
                <option value="{{ $fd->id }}"
                    data-type="food"
                    data-unit_id=""
                    data-unit_name="Serving"
                    data-stock="{{ max(0, $fd->stock ?? 0) }}"
                    data-price="{{ $fd->cost_price > 0 ? $fd->cost_price : $fd->price }}">
                    {{ $fd->name }} (Food)
                </option>
            @endforeach
        </select>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const purchaseForm = document.getElementById('purchaseForm');
            const defaultStoreRoute = "{{ route('purchases.store') }}";
            const purchaseModalLabel = document.getElementById('purchaseModalLabel');
            const submitPurchaseBtn = document.getElementById('submitPurchaseBtn');

            const purchaseStore = document.getElementById('purchaseStore');
            const purchaseSupplier = document.getElementById('purchaseSupplier');
            const purchaseDate = document.getElementById('purchaseDate');
            const purchasePoNo = document.getElementById('purchasePoNo');
            const purchaseNotes = document.getElementById('purchaseNotes');

            const purchaseTableBody = document.getElementById('purchaseTableBody');
            const grandTotalQuantity = document.getElementById('grandTotalQuantity');
            const grandTotalAmount = document.getElementById('grandTotalAmount');

            const itemsCatalogSelect = document.getElementById('itemsCatalogSelect');
            const ingredientsCatalogSelect = document.getElementById('ingredientsCatalogSelect');
            const foodsCatalogSelect = document.getElementById('foodsCatalogSelect');

            document.getElementById('addItemBtn').addEventListener('click', () => addPurchaseRow('item'));
            document.getElementById('addIngredientBtn').addEventListener('click', () => addPurchaseRow('ingredient'));
            document.getElementById('addFoodBtn').addEventListener('click', () => addPurchaseRow('food'));

            // Reset modal form for Add New Purchase
            window.resetPurchaseForm = function() {
                purchaseForm.action = defaultStoreRoute;
                purchaseForm.reset();
                purchaseModalLabel.textContent = 'Add New Purchase';
                submitPurchaseBtn.textContent = 'Save Purchase';
                purchaseDate.value = new Date().toISOString().split('T')[0];

                // Auto fetch next PO #
                fetch("{{ route('purchases.generate-po') }}")
                    .then(res => res.json())
                    .then(data => {
                        if (data.status && data.po_no) {
                            purchasePoNo.value = data.po_no;
                        }
                    })
                    .catch(() => {});

                purchaseTableBody.innerHTML = '';
                addPurchaseRow('item');
            };

            function calculateRow(row) {
                const qtyInput = row.querySelector('.row-quantity');
                const priceInput = row.querySelector('.row-price');
                const totalSpan = row.querySelector('.row-total-price');

                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const lineTotal = (qty * price).toFixed(2);

                totalSpan.textContent = lineTotal;
                calculateGrandTotals();
            }

            function calculateGrandTotals() {
                let totalQty = 0;
                let totalAmt = 0;

                purchaseTableBody.querySelectorAll('.purchase-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('.row-quantity').value) || 0;
                    const amt = parseFloat(row.querySelector('.row-total-price').textContent) || 0;
                    totalQty += qty;
                    totalAmt += amt;
                });

                grandTotalQuantity.textContent = totalQty.toFixed(2);
                grandTotalAmount.textContent = 'Rs ' + totalAmt.toFixed(2);
            }

            function renumberRows() {
                const rows = purchaseTableBody.querySelectorAll('.purchase-row');
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
                const stockSpan = row.querySelector('.row-available-stock');
                const unitIdInput = row.querySelector('.row-unit-id');
                const qtyInput = row.querySelector('.row-quantity');
                const priceInput = row.querySelector('.row-price');
                const duplicateBtn = row.querySelector('.row-duplicate-btn');
                const removeBtn = row.querySelector('.row-remove-btn');

                select.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    if (!selected || !selected.value) {
                        unitSpan.textContent = '-';
                        stockSpan.textContent = '0';
                        unitIdInput.value = '';
                        priceInput.value = '0.00';
                        calculateRow(row);
                        return;
                    }

                    const type = row.dataset.type || 'item';
                    const productId = selected.value;

                    // Immediately show data attributes from template as fallback
                    const unitName = selected.dataset.unit_name || '-';
                    const unitId = selected.dataset.unit_id || '';
                    const stock = Math.max(0, parseFloat(selected.dataset.stock) || 0);
                    const price = parseFloat(selected.dataset.price) || 0;

                    unitSpan.textContent = unitName;
                    stockSpan.textContent = stock.toFixed(2);
                    unitIdInput.value = unitId;
                    priceInput.value = (price % 1 === 0) ? price.toFixed(2) : parseFloat(price.toFixed(4));
                    calculateRow(row);

                    // Fetch 100% fresh live price and stock from DB via AJAX
                    fetch(`/purchases/product-info?type=${type}&id=${productId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status) {
                                if (data.unit_name) unitSpan.textContent = data.unit_name;
                                if (data.unit_id !== undefined) unitIdInput.value = data.unit_id || '';
                                stockSpan.textContent = Math.max(0, parseFloat(data.stock || 0)).toFixed(2);
                                const livePrice = parseFloat(data.price || 0);
                                priceInput.value = (livePrice % 1 === 0) ? livePrice.toFixed(2) : parseFloat(livePrice.toFixed(4));
                                calculateRow(row);
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching live product info:', err);
                        });
                });

                qtyInput.addEventListener('input', () => calculateRow(row));
                priceInput.addEventListener('input', () => calculateRow(row));

                duplicateBtn.addEventListener('click', () => {
                    const type = row.dataset.type || 'item';
                    addPurchaseRow(type, row);
                });

                removeBtn.addEventListener('click', () => {
                    if (purchaseTableBody.querySelectorAll('.purchase-row').length > 1) {
                        row.remove();
                        renumberRows();
                    } else {
                        select.value = '';
                        unitSpan.textContent = '-';
                        stockSpan.textContent = '0';
                        unitIdInput.value = '';
                        qtyInput.value = '1';
                        priceInput.value = '0.00';
                        calculateRow(row);
                    }
                });
            }

            function addPurchaseRow(type = 'item', afterRow = null, prefillData = null) {
                const tr = document.createElement('tr');
                tr.className = 'purchase-row';
                tr.dataset.type = type;

                let catalogSelect = itemsCatalogSelect;
                let typeBadge = '<span class="badge bg-label-primary px-2 py-1 fs-8 flex-shrink-0">ITEM</span>';

                if (type === 'ingredient') {
                    catalogSelect = ingredientsCatalogSelect;
                    typeBadge = '<span class="badge bg-label-success px-2 py-1 fs-8 flex-shrink-0">INGREDIENT</span>';
                } else if (type === 'food') {
                    catalogSelect = foodsCatalogSelect;
                    typeBadge = '<span class="badge bg-label-warning px-2 py-1 fs-8 flex-shrink-0">FOOD</span>';
                }

                const optionsHtml = catalogSelect.innerHTML;
                const idx = purchaseTableBody.querySelectorAll('.purchase-row').length;

                tr.innerHTML = `
                    <td class="text-center fw-bold text-dark row-index">${idx + 1}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            ${typeBadge}
                            <input type="hidden" name="items[${idx}][type]" value="${type}">
                            <input type="hidden" name="items[${idx}][unit_id]" class="row-unit-id" value="${prefillData ? prefillData.unit_id || '' : ''}">
                            <select name="items[${idx}][product_id]" class="form-select form-select-sm product-select flex-grow-1 fw-semibold text-dark" style="min-width: 220px;" required>
                                ${optionsHtml}
                            </select>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border row-unit-badge fs-7 fw-semibold">${prefillData ? prefillData.unit_name || '-' : '-'}</span>
                    </td>
                    <td class="text-center">
                        <span class="text-dark fw-bold row-available-stock">${prefillData ? Math.max(0, parseFloat(prefillData.available_stock) || 0).toFixed(2) : '0.00'}</span>
                    </td>
                    <td>
                        <input type="number" step="any" min="0.0001" name="items[${idx}][quantity]"
                            class="form-control form-control-sm text-end row-quantity no-spin fw-semibold text-dark"
                            style="padding: 0.4375rem 0.65rem;"
                            value="${prefillData ? prefillData.quantity : '1'}" placeholder="0" required>
                    </td>
                    <td>
                        <input type="number" step="any" min="0" name="items[${idx}][price]"
                            class="form-control form-control-sm text-end row-price no-spin fw-semibold text-dark"
                            style="padding: 0.4375rem 0.65rem;"
                            value="${prefillData ? ((parseFloat(prefillData.price) % 1 === 0) ? parseFloat(prefillData.price).toFixed(2) : parseFloat(prefillData.price).toFixed(4)) : '0.00'}"
                            placeholder="0.00" required>
                    </td>
                    <td class="text-end">
                        <span class="row-total-price fw-bold text-dark fs-6">${prefillData ? prefillData.total_price.toFixed(2) : '0.00'}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary row-duplicate-btn" title="Add Row Below">
                                <i class="bx bx-plus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger row-remove-btn" title="Remove Row">
                                <i class="bx bx-minus"></i>
                            </button>
                        </div>
                    </td>
                `;

                attachRowEvents(tr);

                if (afterRow && afterRow.nextSibling) {
                    purchaseTableBody.insertBefore(tr, afterRow.nextSibling);
                } else {
                    purchaseTableBody.appendChild(tr);
                }

                if (prefillData && prefillData.product_id) {
                    const sel = tr.querySelector('.product-select');
                    sel.value = prefillData.product_id;
                }

                renumberRows();
            }

            // Edit Purchase Click
            document.querySelectorAll('.edit-purchase-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const purchaseId = this.dataset.id;

                    fetch(`/purchases/${purchaseId}/edit-data`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.status || !data.purchase) return;

                            const p = data.purchase;
                            purchaseForm.action = `/purchases/${p.id}/update`;
                            submitPurchaseBtn.textContent = 'Update Purchase';
                            purchaseModalLabel.textContent = `Edit Purchase (${p.po_no})`;

                            purchaseStore.value = p.store_id || '';
                            purchaseSupplier.value = p.supplier_id || '';
                            purchaseDate.value = p.purchase_date || '';
                            purchasePoNo.value = p.po_no || '';
                            purchaseNotes.value = p.notes || '';

                            // Populate line items
                            purchaseTableBody.innerHTML = '';
                            if (p.items && p.items.length > 0) {
                                p.items.forEach(item => {
                                    addPurchaseRow(item.type, null, item);
                                });
                            } else {
                                addPurchaseRow('item');
                            }
                        })
                        .catch(err => {
                            console.error("Error fetching purchase data:", err);
                        });
                });
            });

            // View Purchase Details Modal
            document.querySelectorAll('.view-purchase-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const purchaseId = this.dataset.id;

                    fetch(`/purchases/${purchaseId}/edit-data`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.status || !data.purchase) return;

                            const p = data.purchase;
                            document.getElementById('v_po_no').textContent = p.po_no || '-';
                            document.getElementById('v_store').textContent = p.store_name || '-';
                            document.getElementById('v_supplier').textContent = p.supplier_name || '-';
                            document.getElementById('v_date').textContent = p.purchase_date || '-';

                            const notesContainer = document.getElementById('v_notes_container');
                            if (p.notes) {
                                document.getElementById('v_notes').textContent = p.notes;
                                notesContainer.classList.remove('d-none');
                            } else {
                                notesContainer.classList.add('d-none');
                            }

                            const vBody = document.getElementById('v_items_body');
                            vBody.innerHTML = '';

                            let sumQty = 0;
                            let sumTotal = 0;

                            p.items.forEach((item, idx) => {
                                sumQty += item.quantity;
                                sumTotal += item.total_price;

                                let badgeClass = 'bg-label-primary';
                                if (item.type === 'ingredient') badgeClass = 'bg-label-success';
                                if (item.type === 'food') badgeClass = 'bg-label-warning';

                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td class="text-center fw-medium text-dark">${idx + 1}</td>
                                    <td class="fw-bold text-dark">${item.name}</td>
                                    <td class="text-center">
                                        <span class="badge ${badgeClass} text-uppercase">${item.type}</span>
                                    </td>
                                    <td class="text-center text-dark">${item.unit_name}</td>
                                    <td class="text-end fw-semibold text-dark">${item.quantity.toFixed(2)}</td>
                                    <td class="text-end text-dark">Rs ${item.price.toFixed(2)}</td>
                                    <td class="text-end fw-bold text-success">Rs ${item.total_price.toFixed(2)}</td>
                                `;
                                vBody.appendChild(tr);
                            });

                            document.getElementById('v_grand_qty').textContent = sumQty.toFixed(2);
                            document.getElementById('v_grand_total').textContent = 'Rs ' + sumTotal.toFixed(2);
                        })
                        .catch(err => {
                            console.error("Error loading purchase details:", err);
                        });
                });
            });
        });
    </script>
@endsection
