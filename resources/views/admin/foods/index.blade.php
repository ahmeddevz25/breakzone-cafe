@extends('admin.layouts')
@section('title', 'Foods Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Foods Management</h5>
                        @can('food add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#foodModal" onclick="resetFoodForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add New Food</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Foods Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>#</th>
                                    <th>Food</th>
                                    <th>Code</th>
                                    <th>Category</th>
                                    <th>Store</th>
                                    <th>Stock</th>
                                    <th>Cost Price</th>
                                    <th>Sale Price</th>
                                    <th>Status</th>
                                    <th class="text-center">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($foods as $key => $food)
                                    <tr class="text-dark">
                                        <td class="text-dark fw-medium">{{ $key + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ $food->name }}</td>
                                        <td class="text-dark font-monospace fw-medium">{{ $food->code }}</td>
                                        <td class="text-dark fw-medium">{!! $food->category ? $food->category->full_path : '-' !!}</td>
                                        <td class="text-dark fw-medium">{{ $food->store->store ?? $food->store->name ?? '-' }}</td>
                                        <td class="text-dark fw-bold text-primary">{{ number_format($food->stock ?? 0, 2) }}</td>
                                        <td class="text-dark fw-medium">{{ number_format($food->cost_price, 2) }}</td>
                                        <td class="text-dark fw-bold text-success">{{ number_format($food->price, 2) }}</td>
                                        <td>
                                            @if ($food->status == 'A' || $food->status == 'active' || $food->status === '1' || $food->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="table-actions">
                                                {{-- View Details --}}
                                                <button type="button" title="View Details"
                                                    class="action-btn action-btn-view view-food-btn"
                                                    data-id="{{ $food->id }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewFoodModal">
                                                    <i class='bx bx-show'></i>
                                                </button>

                                                {{-- Edit --}}
                                                @can('food edit')
                                                    <button type="button" title="Edit"
                                                        class="action-btn action-btn-edit edit-food-btn"
                                                        data-id="{{ $food->id }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#foodModal">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete --}}
                                                @can('food delete')
                                                    <a href="{{ route('foods.delete', $food->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this food item?')"
                                                        class="action-btn action-btn-delete">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">No food items available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Food Modal (Add / Edit) - Landscape Mode -->
                    <div class="modal fade" id="foodModal" tabindex="-1" aria-labelledby="foodModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 1300px; width: 94%; margin: 1.75rem auto;">
                            <div class="modal-content">
                                <form id="foodForm" action="{{ route('foods.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                                        <h5 class="modal-title fw-bold m-0" id="foodModalLabel">Add New Food</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: static !important; margin: 0 !important; transform: none !important; box-shadow: none !important;"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- SECTION 1: Food Information (Landscape Grid) --}}
                                        <div class="mb-4">
                                            <div class="row g-3">
                                                {{-- Row 1: 4 columns --}}
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold">Store <span class="text-danger">*</span></label>
                                                    <select name="store_id" id="foodStore" class="form-select" required>
                                                        <option value="">Select Store</option>
                                                        @foreach ($stores as $store)
                                                            <option value="{{ $store->id }}">
                                                                {{ $store->store ?? $store->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold">Food Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" id="foodName" class="form-control" placeholder="e.g. Club Sandwich" required>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold">Food Category <span class="text-danger">*</span></label>
                                                    <select name="category_id" id="foodCategory" class="form-select" required>
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $cat)
                                                            <option value="{{ $cat->id }}">
                                                                {!! $cat->full_path ?? $cat->category ?? $cat->name !!}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <label class="form-label fw-semibold mb-0">Code <span class="text-danger">*</span></label>
                                                        <span id="codeLoadingIndicator" class="spinner-border spinner-border-sm text-primary d-none"></span>
                                                    </div>
                                                    <input type="text" name="code" id="foodCode" class="form-control mt-1" placeholder="e.g. 02050104" required>
                                                </div>

                                                {{-- Row 2: Pricing, Status & Picture --}}
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold">Price (Sale Price) <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" min="0" name="price" id="foodPrice" class="form-control" placeholder="0.00" required>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                    <select name="status" id="foodStatus" class="form-select" required>
                                                        <option value="A">Active</option>
                                                        <option value="I">Inactive</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Picture</label>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file" name="picture" id="foodPicture" class="form-control" accept="image/*">
                                                        <div id="foodPicturePreviewContainer" class="d-none flex-shrink-0">
                                                            <img id="foodPicturePreview" src="" alt="Preview" style="height: 38px; width: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SECTION 2: Recipe / Ingredients Composition Table --}}
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 fs-6">
                                                    <i class="bx bx-dish text-primary font-size-20"></i> Recipe / Ingredients Composition
                                                </h6>
                                                <button type="button" class="btn btn-primary d-flex align-items-center gap-1" id="addIngredientRowBtn">
                                                    <i class="bx bx-plus"></i> Add Ingredient
                                                </button>
                                            </div>

                                             <div class="table-responsive bg-white border rounded">
                                                <table class="table table-bordered align-middle mb-0" id="recipeTable">
                                                    <thead class="table-light">
                                                        <tr class="text-dark small text-uppercase fw-bold">
                                                            <th style="width: 45px;" class="text-center">#</th>
                                                            <th style="width: 30%; min-width: 180px;">Ingredients</th>
                                                            <th style="width: 12%; min-width: 90px;" class="text-center">Usage Unit</th>
                                                            <th style="width: 13%; min-width: 100px;" class="text-end">Quantity</th>
                                                            <th style="width: 17%; min-width: 130px;" class="text-end">Price / Unit</th>
                                                            <th style="width: 15%; min-width: 110px;" class="text-end">Total Price</th>
                                                            <th style="width: 90px; min-width: 90px;" class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="recipeTableBody">
                                                        <!-- Initial row inserted by JS -->
                                                    </tbody>
                                                    <tfoot class="table-light fw-bold">
                                                        <tr style="background-color: #f8fafc;">
                                                            <td colspan="3" class="text-dark fw-bold">Grand Total</td>
                                                            <td class="text-end">
                                                                <span id="grandTotalQty" class="text-dark fw-bold fs-6">0.00</span>
                                                            </td>
                                                            <td class="text-center text-dark fw-bold">-</td>
                                                            <td class="text-end">
                                                                <span id="grandTotalPrice" class="text-success fw-bold fs-6">0.00</span>
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
                                        <button type="submit" id="submitFoodBtn" class="btn btn-primary px-4">Save Food</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- View Food Details Modal - Landscape Mode -->
                    <div class="modal fade" id="viewFoodModal" tabindex="-1" aria-labelledby="viewFoodModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 1220px; width: 92%; margin: 1.75rem auto;">
                            <div class="modal-content">
                                <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                                    <h5 class="modal-title fw-bold m-0" id="viewFoodModalLabel">Food Details & Recipe</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: static !important; margin: 0 !important; transform: none !important; box-shadow: none !important;"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-4 text-center border-end pe-md-3 d-flex flex-column align-items-center justify-content-center">
                                            <div id="v_food_picture_container" class="mb-3 w-100 d-none">
                                                <img id="v_food_picture" src="" alt="Food Image" style="max-width: 100%; max-height: 180px; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: contain;">
                                            </div>
                                            <div id="v_food_no_picture" class="mb-3 d-flex flex-column align-items-center justify-content-center bg-light text-muted rounded p-4 w-100 d-none" style="height: 160px; border: 1px dashed #cbd5e1;">
                                                <i class='bx bx-image' style="font-size: 2.5rem; color: #94a3b8;"></i>
                                                <span class="small mt-1 text-muted">No Image</span>
                                            </div>
                                            <div id="v_food_status" class="mb-2"></div>
                                            <div class="mt-2 w-100">
                                                <div class="badge bg-label-primary w-100 py-2 mb-1 fs-6">
                                                    Stock: <span id="v_food_stock">0.00</span>
                                                </div>
                                                <div class="badge bg-label-success w-100 py-2 mb-1 fs-6">
                                                    Sale Price: <span id="v_food_sale_price">0.00</span>
                                                </div>
                                                <div class="badge bg-label-info w-100 py-2 fs-6">
                                                    Cost Price: <span id="v_food_cost_price">0.00</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 ps-md-3">
                                            <table class="table table-bordered table-striped table-sm mb-3">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%;">Food Name</th>
                                                        <td id="v_food_name" class="text-dark fw-bold"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Code</th>
                                                        <td id="v_food_code" class="text-dark fw-semibold"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Category</th>
                                                        <td id="v_food_category" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Store</th>
                                                        <td id="v_food_store" class="text-dark"></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <h6 class="fw-bold text-dark border-bottom pb-1 mb-2">Recipe Ingredients Breakdown</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm align-middle mb-0">
                                                    <thead class="table-light small text-uppercase">
                                                        <tr>
                                                            <th>Ingredient</th>
                                                            <th class="text-center">Usage Unit</th>
                                                            <th class="text-end">Quantity</th>
                                                            <th class="text-end">Unit Cost</th>
                                                            <th class="text-end">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="v_recipe_tbody">
                                                        <!-- Dynamic Rows -->
                                                    </tbody>
                                                    <tfoot class="table-light fw-bold small">
                                                        <tr>
                                                            <td colspan="4">Total Recipe Cost</td>
                                                            <td id="v_recipe_grand_total" class="text-end text-success">0.00</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
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

    <!-- Hidden Template for New Recipe Row -->
    <template id="recipeRowTemplate">
        <tr class="recipe-row">
            <td class="row-number text-center fw-bold text-dark">#</td>
            <td>
                <select name="ingredients[INDEX][ingredient_id]" class="form-select ingredient-select" required>
                    <option value="">Select Ingredient</option>
                    @foreach ($ingredients as $ing)
                        @php
                            $usageUnitName = $ing->usageUnit->unit ?? $ing->usageUnit->name ?? '-';
                            $conv = $ing->conversion_value > 0 ? $ing->conversion_value : 1;
                            $unitCost = round($ing->purchase_price / $conv, 4);
                        @endphp
                        <option value="{{ $ing->id }}"
                            data-usage_unit="{{ $usageUnitName }}"
                            data-unit_price="{{ $unitCost }}">
                            {{ $ing->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="text-center">
                <span class="row-usage-unit badge bg-label-primary fs-7">-</span>
            </td>
            <td>
                <input type="number" step="any" min="0" name="ingredients[INDEX][quantity]" class="form-control row-qty text-end no-spin" style="padding: 0.4375rem 0.65rem;" value="1" placeholder="0">
            </td>
            <td>
                <input type="number" step="any" min="0" name="ingredients[INDEX][unit_price]" class="form-control row-price text-end no-spin" style="padding: 0.4375rem 0.65rem;" value="0.00" placeholder="0.00">
            </td>
            <td class="text-end">
                <span class="row-total fw-bold text-dark fs-6">0.00</span>
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary add-row-btn" title="Add Row below">
                        <i class="bx bx-plus"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" title="Remove Row">
                        <i class="bx bx-minus"></i>
                    </button>
                </div>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const foodForm = document.getElementById('foodForm');
            const defaultStoreRoute = "{{ route('foods.store') }}";
            const foodModalLabel = document.getElementById('foodModalLabel');
            const submitFoodBtn = document.getElementById('submitFoodBtn');

            const foodStore = document.getElementById('foodStore');
            const foodName = document.getElementById('foodName');
            const foodCategory = document.getElementById('foodCategory');
            const foodCode = document.getElementById('foodCode');
            const foodPrice = document.getElementById('foodPrice');
            const foodStatus = document.getElementById('foodStatus');
            const foodPicture = document.getElementById('foodPicture');
            const foodPicturePreview = document.getElementById('foodPicturePreview');
            const foodPicturePreviewContainer = document.getElementById('foodPicturePreviewContainer');
            const codeLoadingIndicator = document.getElementById('codeLoadingIndicator');

            const recipeTableBody = document.getElementById('recipeTableBody');
            const addIngredientRowBtn = document.getElementById('addIngredientRowBtn');
            const grandTotalQty = document.getElementById('grandTotalQty');
            const grandTotalPrice = document.getElementById('grandTotalPrice');

            // Image Preview
            if (foodPicture) {
                foodPicture.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            foodPicturePreview.src = e.target.result;
                            foodPicturePreviewContainer.classList.remove('d-none');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Category Selection -> Auto Fetch & Populate Code
            if (foodCategory) {
                foodCategory.addEventListener('change', function() {
                    const categoryId = this.value;
                    if (!categoryId) return;

                    codeLoadingIndicator.classList.remove('d-none');
                    fetch(`/foods/generate-code?category_id=${categoryId}`)
                        .then(res => res.json())
                        .then(data => {
                            codeLoadingIndicator.classList.add('d-none');
                            if (data.status && data.code) {
                                foodCode.value = data.code;
                            }
                        })
                        .catch(err => {
                            codeLoadingIndicator.classList.add('d-none');
                            console.error("Error generating code:", err);
                        });
                });
            }

            // Recipe Table Calculations
            function renumberRows() {
                const rows = recipeTableBody.querySelectorAll('.recipe-row');
                rows.forEach((row, idx) => {
                    row.querySelector('.row-number').textContent = idx + 1;
                    row.querySelectorAll('select, input').forEach(input => {
                        input.name = input.name.replace(/\[\d+\]/, `[${idx}]`);
                    });
                });
                calculateTotals();
            }

            function calculateRow(row) {
                const qtyInput = row.querySelector('.row-qty');
                const priceInput = row.querySelector('.row-price');
                const totalSpan = row.querySelector('.row-total');

                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const lineTotal = (qty * price).toFixed(2);
                totalSpan.textContent = lineTotal;

                calculateTotals();
            }

            function calculateTotals() {
                let totalQty = 0;
                let totalPrice = 0;

                recipeTableBody.querySelectorAll('.recipe-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
                    const total = parseFloat(row.querySelector('.row-total').textContent) || 0;
                    totalQty += qty;
                    totalPrice += total;
                });

                grandTotalQty.textContent = totalQty.toFixed(2);
                grandTotalPrice.textContent = totalPrice.toFixed(2);
            }

            function attachRowListeners(row) {
                const select = row.querySelector('.ingredient-select');
                const qtyInput = row.querySelector('.row-qty');
                const priceInput = row.querySelector('.row-price');
                const unitSpan = row.querySelector('.row-usage-unit');
                const addBtn = row.querySelector('.add-row-btn');
                const removeBtn = row.querySelector('.remove-row-btn');

                select.addEventListener('change', function() {
                    const selectedOpt = this.options[this.selectedIndex];
                    const unit = selectedOpt.dataset.usage_unit || '-';
                    const unitPrice = parseFloat(selectedOpt.dataset.unit_price) || 0;

                    unitSpan.textContent = unit;
                    priceInput.value = (unitPrice % 1 === 0) ? unitPrice.toFixed(2) : parseFloat(unitPrice.toFixed(4));
                    calculateRow(row);
                });

                qtyInput.addEventListener('input', () => calculateRow(row));
                priceInput.addEventListener('input', () => calculateRow(row));

                addBtn.addEventListener('click', () => {
                    addNewRow(row);
                });

                removeBtn.addEventListener('click', () => {
                    if (recipeTableBody.querySelectorAll('.recipe-row').length > 1) {
                        row.remove();
                        renumberRows();
                    } else {
                        // Reset the only row
                        select.value = '';
                        unitSpan.textContent = '-';
                        qtyInput.value = '1';
                        priceInput.value = '0.00';
                        calculateRow(row);
                    }
                });
            }

            function addNewRow(afterRow = null) {
                const template = document.getElementById('recipeRowTemplate').content.cloneNode(true);
                const newRow = template.querySelector('.recipe-row');
                
                attachRowListeners(newRow);

                if (afterRow && afterRow.nextSibling) {
                    recipeTableBody.insertBefore(newRow, afterRow.nextSibling);
                } else {
                    recipeTableBody.appendChild(newRow);
                }

                renumberRows();
            }

            if (addIngredientRowBtn) {
                addIngredientRowBtn.addEventListener('click', () => addNewRow());
            }

            // Reset modal form for adding new food
            window.resetFoodForm = function() {
                foodForm.action = defaultStoreRoute;
                foodForm.reset();
                foodModalLabel.textContent = 'Add New Food';
                submitFoodBtn.textContent = 'Save Food';
                foodPrice.value = '';
                foodCode.value = '';
                foodStatus.value = 'A';
                foodPicturePreview.src = '';
                foodPicturePreviewContainer.classList.add('d-none');

                // Reset recipe table to 1 empty row
                recipeTableBody.innerHTML = '';
                addNewRow();
            };

            // Edit Food logic
            document.querySelectorAll('.edit-food-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const foodId = this.dataset.id;
                    
                    fetch(`/foods/${foodId}/edit-data`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.status || !data.food) return;

                            const f = data.food;
                            foodForm.action = `/foods/${f.id}/update`;
                            foodModalLabel.textContent = 'Edit Food';
                            submitFoodBtn.textContent = 'Update Food';

                            foodStore.value = f.store_id || '';
                            foodName.value = f.name || '';
                            foodCategory.value = f.category_id || '';
                            foodCode.value = f.code || '';
                            foodPrice.value = f.price || '0';
                            foodStatus.value = f.status === 'A' || f.status === 'active' || f.status === '1' ? 'A' : 'I';

                            if (f.picture_url) {
                                foodPicturePreview.src = f.picture_url;
                                foodPicturePreviewContainer.classList.remove('d-none');
                            } else {
                                foodPicturePreview.src = '';
                                foodPicturePreviewContainer.classList.add('d-none');
                            }

                            // Populate Recipe Ingredients
                            recipeTableBody.innerHTML = '';
                            if (f.ingredients && f.ingredients.length > 0) {
                                f.ingredients.forEach(item => {
                                    const template = document.getElementById('recipeRowTemplate').content.cloneNode(true);
                                    const row = template.querySelector('.recipe-row');

                                    const select = row.querySelector('.ingredient-select');
                                    select.value = item.ingredient_id;

                                    row.querySelector('.row-usage-unit').textContent = item.usage_unit;
                                    row.querySelector('.row-qty').value = parseFloat(item.quantity) || item.quantity;
                                    const uPrice = parseFloat(item.unit_price) || 0;
                                    row.querySelector('.row-price').value = (uPrice % 1 === 0) ? uPrice.toFixed(2) : parseFloat(uPrice.toFixed(4));
                                    row.querySelector('.row-total').textContent = item.total_price.toFixed(2);

                                    attachRowListeners(row);
                                    recipeTableBody.appendChild(row);
                                });
                            } else {
                                addNewRow();
                            }

                            renumberRows();
                        })
                        .catch(err => {
                            console.error("Error fetching food data:", err);
                        });
                });
            });

            // View Food Details Modal
            document.querySelectorAll('.view-food-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const foodId = this.dataset.id;
                    
                    fetch(`/foods/${foodId}/edit-data`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.status || !data.food) return;

                            const f = data.food;
                            document.getElementById('v_food_name').textContent = f.name;
                            document.getElementById('v_food_code').textContent = f.code;
                            document.getElementById('v_food_stock').textContent = (f.stock !== undefined ? parseFloat(f.stock).toFixed(2) : '0.00');
                            document.getElementById('v_food_sale_price').textContent = 'Rs ' + f.price.toFixed(2);
                            document.getElementById('v_food_cost_price').textContent = 'Rs ' + f.cost_price.toFixed(2);

                            // Find category and store names from selects
                            const catOpt = foodCategory.querySelector(`option[value="${f.category_id}"]`);
                            document.getElementById('v_food_category').textContent = catOpt ? catOpt.textContent.trim() : '-';

                            const storeOpt = foodStore.querySelector(`option[value="${f.store_id}"]`);
                            document.getElementById('v_food_store').textContent = storeOpt ? storeOpt.textContent.trim() : '-';

                            // Picture
                            const vPic = document.getElementById('v_food_picture');
                            const vPicContainer = document.getElementById('v_food_picture_container');
                            const vNoPic = document.getElementById('v_food_no_picture');

                            if (f.picture_url) {
                                vPic.src = f.picture_url;
                                vPicContainer.classList.remove('d-none');
                                vNoPic.classList.add('d-none');
                            } else {
                                vPic.src = '';
                                vPicContainer.classList.add('d-none');
                                vNoPic.classList.remove('d-none');
                            }

                            // Status
                            const vStatus = document.getElementById('v_food_status');
                            if (f.status === 'A' || f.status === 'active' || f.status === '1') {
                                vStatus.innerHTML = '<span class="badge bg-success">Active</span>';
                            } else {
                                vStatus.innerHTML = '<span class="badge bg-danger">Inactive</span>';
                            }

                            // Populate Recipe
                            const vTbody = document.getElementById('v_recipe_tbody');
                            vTbody.innerHTML = '';
                            let totalRecipeCost = 0;

                            if (f.ingredients && f.ingredients.length > 0) {
                                f.ingredients.forEach(item => {
                                    totalRecipeCost += item.total_price;
                                    const tr = document.createElement('tr');
                                    tr.innerHTML = `
                                        <td class="fw-semibold text-dark">${item.ingredient_name}</td>
                                        <td class="text-center"><span class="badge bg-label-primary">${item.usage_unit}</span></td>
                                        <td class="text-end text-dark">${item.quantity}</td>
                                        <td class="text-end text-dark">${item.unit_price.toFixed(2)}</td>
                                        <td class="text-end fw-bold text-dark">${item.total_price.toFixed(2)}</td>
                                    `;
                                    vTbody.appendChild(tr);
                                });
                            } else {
                                vTbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-2">No ingredients recorded.</td></tr>';
                            }

                            document.getElementById('v_recipe_grand_total').textContent = 'Rs ' + totalRecipeCost.toFixed(2);
                        })
                        .catch(err => {
                            console.error("Error fetching food details:", err);
                        });
                });
            });

            // Initialize default row
            addNewRow();
        });
    </script>
@endsection
