@extends('admin.layouts')
@section('title', 'Ingredients Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Ingredients Management</h5>
                        @can('ingredient add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#ingredientModal" onclick="resetIngredientForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add New Ingredient</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Ingredients Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>#</th>
                                    <th>Ingredient</th>
                                    <th>Buying Unit</th>
                                    <th>Usage Unit</th>
                                    <th>Purchase Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th class="text-center">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ingredients as $key => $ingredient)
                                    <tr class="text-dark">
                                        <td class="text-dark fw-medium">{{ $key + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ $ingredient->name }}</td>
                                        <td class="text-dark fw-medium">{{ $ingredient->buyingUnit->unit ?? $ingredient->buyingUnit->name ?? '-' }}</td>
                                        <td class="text-dark fw-medium">{{ $ingredient->usageUnit->unit ?? $ingredient->usageUnit->name ?? '-' }}</td>
                                        <td class="text-dark fw-medium">{{ number_format($ingredient->purchase_price, 2) }}</td>
                                        <td class="text-dark fw-bold">{{ number_format($ingredient->stock, 2) }}</td>
                                        <td>
                                            @if ($ingredient->status == 'A' || $ingredient->status == 'active' || $ingredient->status === '1' || $ingredient->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="table-actions">
                                                {{-- View Details --}}
                                                <button type="button" title="View Details"
                                                    class="action-btn action-btn-view view-ingredient-btn"
                                                    data-name="{{ $ingredient->name }}"
                                                    data-store="{{ $ingredient->store->store ?? $ingredient->store->name ?? '-' }}"
                                                    data-buying_unit="{{ $ingredient->buyingUnit->unit ?? $ingredient->buyingUnit->name ?? '-' }}"
                                                    data-usage_unit="{{ $ingredient->usageUnit->unit ?? $ingredient->usageUnit->name ?? '-' }}"
                                                    data-conversion="{{ $ingredient->conversion_value ?? 0 }}"
                                                    data-purchase_price="{{ number_format($ingredient->purchase_price, 2) }}"
                                                    data-stock="{{ number_format($ingredient->stock, 2) }}"
                                                    data-details="{{ $ingredient->details ?? '-' }}"
                                                    data-picture="{{ $ingredient->picture_url ?? '' }}"
                                                    data-status="{{ $ingredient->status }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewIngredientModal">
                                                    <i class='bx bx-show'></i>
                                                </button>
                                                {{-- Edit --}}
                                                @can('ingredient edit')
                                                    <button type="button" title="Edit"
                                                        class="action-btn action-btn-edit edit-ingredient-btn"
                                                        data-id="{{ $ingredient->id }}"
                                                        data-name="{{ $ingredient->name }}"
                                                        data-store_id="{{ $ingredient->store_id }}"
                                                        data-buying_unit_id="{{ $ingredient->buying_unit_id }}"
                                                        data-usage_unit_id="{{ $ingredient->usage_unit_id }}"
                                                        data-conversion_value="{{ $ingredient->conversion_value }}"
                                                        data-purchase_price="{{ $ingredient->purchase_price }}"
                                                        data-stock="{{ $ingredient->stock }}"
                                                        data-details="{{ $ingredient->details }}"
                                                        data-picture="{{ $ingredient->picture_url }}"
                                                        data-status="{{ $ingredient->status }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#ingredientModal">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete --}}
                                                @can('ingredient delete')
                                                    <a href="{{ route('ingredients.delete', $ingredient->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this ingredient?')"
                                                        class="action-btn action-btn-delete">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No ingredients available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Ingredient Modal (Add / Edit) -->
                    <div class="modal fade" id="ingredientModal" tabindex="-1" aria-labelledby="ingredientModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form id="ingredientForm" action="{{ route('ingredients.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title fw-bold" id="ingredientModalLabel">Add New Ingredient</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <div class="row">
                                            {{-- Ingredient Name --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Ingredient <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="ingredientName" class="form-control" placeholder="e.g. Butter, Chicken, Cheese" required>
                                            </div>

                                            {{-- Store --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Store <span class="text-danger">*</span></label>
                                                <select name="store_id" id="ingredientStore" class="form-select" required>
                                                    <option value="">Select Store</option>
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}">{{ $store->store ?? $store->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- Buying Unit --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Buying Unit <span class="text-danger">*</span></label>
                                                <select name="buying_unit_id" id="ingredientBuyingUnit" class="form-select" required>
                                                    <option value="">Select Buying Unit</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->unit ?? $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Usage Unit --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Usage Unit <span class="text-danger">*</span></label>
                                                <select name="usage_unit_id" id="ingredientUsageUnit" class="form-select" required>
                                                    <option value="">Select Usage Unit</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->unit ?? $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- Unit Conversion Value --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Unit Conversion Value</label>
                                                <input type="number" step="any" name="conversion_value" id="ingredientConversion" class="form-control" value="0" placeholder="e.g. 1000">
                                            </div>

                                            {{-- Purchase Price --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Purchase Price</label>
                                                <input type="number" step="any" name="purchase_price" id="ingredientPrice" class="form-control" value="0" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- Picture --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Picture</label>
                                                <input type="file" name="picture" id="ingredientPicture" class="form-control" accept="image/*">
                                                <div id="picturePreviewContainer" class="mt-2" style="display: none;">
                                                    <img id="picturePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 80px;">
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                <select name="status" id="ingredientStatus" class="form-select" required>
                                                    <option value="A">Active</option>
                                                    <option value="I">Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Details --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Details / Notes</label>
                                            <textarea name="details" id="ingredientDetails" class="form-control" rows="2" placeholder="Additional details..."></textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">Save Ingredient</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- View Ingredient Modal - Details -->
                    <div class="modal fade" id="viewIngredientModal" tabindex="-1" aria-labelledby="viewIngredientModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-bottom py-3">
                                    <h5 class="modal-title fw-bold" id="viewIngredientModalLabel">Ingredient Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-4 text-center border-end pe-md-3 d-flex flex-column align-items-center justify-content-center">
                                            <div id="v_ing_picture_container" class="mb-3 w-100 d-none">
                                                <img id="v_ing_picture" src="" alt="Ingredient Image" style="max-width: 100%; max-height: 180px; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: contain;">
                                            </div>
                                            <div id="v_ing_no_picture" class="mb-3 d-flex flex-column align-items-center justify-content-center bg-light text-muted rounded p-4 w-100 d-none" style="height: 160px; border: 1px dashed #cbd5e1;">
                                                <i class='bx bx-image' style="font-size: 2.5rem; color: #94a3b8;"></i>
                                                <span class="small mt-1 text-muted">No Image</span>
                                            </div>
                                            <div id="v_ing_status"></div>
                                        </div>
                                        <div class="col-md-8 ps-md-3">
                                            <table class="table table-bordered table-striped table-sm mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%;">Ingredient Name</th>
                                                        <td id="v_ing_name" class="text-dark fw-bold"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Store</th>
                                                        <td id="v_ing_store" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Buying Unit</th>
                                                        <td id="v_ing_buying_unit" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Usage Unit</th>
                                                        <td id="v_ing_usage_unit" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Conversion Value</th>
                                                        <td id="v_ing_conversion" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Purchase Price</th>
                                                        <td id="v_ing_price" class="text-dark fw-semibold"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Stock</th>
                                                        <td id="v_ing_stock" class="text-dark fw-bold"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Details / Notes</th>
                                                        <td id="v_ing_details" class="text-dark"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('ingredientForm');
            const modalTitle = document.getElementById('ingredientModalLabel');
            const submitBtn = document.getElementById('submitBtn');
            
            const nameInput = document.getElementById('ingredientName');
            const storeInput = document.getElementById('ingredientStore');
            const buyingUnitInput = document.getElementById('ingredientBuyingUnit');
            const usageUnitInput = document.getElementById('ingredientUsageUnit');
            const conversionInput = document.getElementById('ingredientConversion');
            const priceInput = document.getElementById('ingredientPrice');
            const detailsInput = document.getElementById('ingredientDetails');
            const statusInput = document.getElementById('ingredientStatus');
            const pictureInput = document.getElementById('ingredientPicture');
            const previewContainer = document.getElementById('picturePreviewContainer');
            const previewImg = document.getElementById('picturePreview');

            const storeRoute = "{{ route('ingredients.store') }}";

            window.resetIngredientForm = function() {
                form.action = storeRoute;
                modalTitle.textContent = 'Add New Ingredient';
                submitBtn.textContent = 'Save Ingredient';
                form.reset();
                conversionInput.value = '0';
                priceInput.value = '0';
                statusInput.value = 'A';
                previewContainer.style.display = 'none';
                previewImg.src = '';
            };

            // Edit Ingredient
            document.querySelectorAll('.edit-ingredient-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const storeId = this.dataset.store_id;
                    const buyingUnitId = this.dataset.buying_unit_id;
                    const usageUnitId = this.dataset.usage_unit_id;
                    const conversion = this.dataset.conversion_value;
                    const price = this.dataset.purchase_price;
                    const stock = this.dataset.stock;
                    const details = this.dataset.details;
                    const picture = this.dataset.picture;
                    let status = this.dataset.status;
                    if (status === 'active' || status === '1' || status === 'A') {
                        status = 'A';
                    } else {
                        status = 'I';
                    }

                    form.action = `/ingredients/${id}/update`;
                    modalTitle.textContent = 'Edit Ingredient';
                    submitBtn.textContent = 'Update Ingredient';

                    nameInput.value = name || '';
                    storeInput.value = storeId || '';
                    buyingUnitInput.value = buyingUnitId || '';
                    usageUnitInput.value = usageUnitId || '';
                    conversionInput.value = conversion || '0';
                    priceInput.value = price || '0';
                    detailsInput.value = details || '';
                    statusInput.value = status;
                    pictureInput.value = '';

                    if (picture) {
                        previewImg.src = picture;
                        previewContainer.style.display = 'block';
                    } else {
                        previewContainer.style.display = 'none';
                        previewImg.src = '';
                    }
                });
            });

            // View Ingredient Details
            document.querySelectorAll('.view-ingredient-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('v_ing_name').textContent = this.dataset.name || '-';
                    document.getElementById('v_ing_store').textContent = this.dataset.store || '-';
                    document.getElementById('v_ing_buying_unit').textContent = this.dataset.buying_unit || '-';
                    document.getElementById('v_ing_usage_unit').textContent = this.dataset.usage_unit || '-';
                    document.getElementById('v_ing_conversion').textContent = this.dataset.conversion || '0';
                    document.getElementById('v_ing_price').textContent = this.dataset.purchase_price || '0.00';
                    document.getElementById('v_ing_stock').textContent = this.dataset.stock || '0.00';
                    document.getElementById('v_ing_details').textContent = this.dataset.details || '-';

                    const picture = this.dataset.picture;
                    const vPicture = document.getElementById('v_ing_picture');
                    const vPictureContainer = document.getElementById('v_ing_picture_container');
                    const vNoPicture = document.getElementById('v_ing_no_picture');

                    if (picture && picture.trim() !== '') {
                        vPicture.src = picture;
                        vPictureContainer.classList.remove('d-none');
                        vNoPicture.classList.add('d-none');
                    } else {
                        vPicture.src = '';
                        vPictureContainer.classList.add('d-none');
                        vNoPicture.classList.remove('d-none');
                    }

                    const status = this.dataset.status;
                    const vStatus = document.getElementById('v_ing_status');
                    if (status === 'A' || status === 'active' || status === '1') {
                        vStatus.innerHTML = '<span class="badge bg-success">Active</span>';
                    } else {
                        vStatus.innerHTML = '<span class="badge bg-danger">Inactive</span>';
                    }
                });
            });
        });
    </script>
@endsection
