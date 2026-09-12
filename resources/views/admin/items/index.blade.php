@extends('admin.layouts')
@section('title', 'Items Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Items Management</h5>
                        @can('item add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#itemModal" onclick="resetItemForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add New Item</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Code</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Purchase Price</th>
                                    <th>Sale Price</th>
                                    <th>Status</th>
                                    <th class="text-center">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $key => $item)
                                    <tr class="text-dark">
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-semibold text-dark">{{ $item->name }}</td>
                                        <td><span class="font-monospace">{{ $item->code }}</span></td>
                                        <td>{!! $item->category ? $item->category->full_path : '-' !!}</td>
                                        <td>{{ number_format($item->stock ?? 0, 2) }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td>{{ number_format($item->sale_price, 2) }}</td>
                                        <td>
                                            @if ($item->status == 'A' || $item->status == 'active' || $item->status === '1' || $item->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                {{-- View Details (Eye Icon) --}}
                                                <button type="button" title="View Details"
                                                    class="btn btn-link text-info fs-5 p-0 view-item-btn"
                                                    data-name="{{ $item->name }}"
                                                    data-store="{{ $item->store->store ?? $item->store->name ?? '-' }}"
                                                    data-category="{!! $item->category ? $item->category->full_path : '-' !!}"
                                                    data-code="{{ $item->code }}"
                                                    data-unit="{{ $item->unit->unit ?? $item->unit->name ?? '-' }}"
                                                    data-brand="{{ $item->brand->name ?? '-' }}"
                                                    data-price="{{ number_format($item->price, 2) }}"
                                                    data-sale_price="{{ number_format($item->sale_price, 2) }}"
                                                    data-stock="{{ number_format($item->stock ?? 0, 2) }}"
                                                    data-details="{{ $item->details ?? '-' }}"
                                                    data-picture="{{ $item->picture_url }}"
                                                    data-status="{{ $item->status }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewItemModal">
                                                    <i class='bx bx-show text-info'></i>
                                                </button>

                                                {{-- Edit Item --}}
                                                @can('item edit')
                                                    <button type="button" title="Edit"
                                                        class="btn btn-link text-primary fs-5 p-0 edit-item-btn"
                                                        data-id="{{ $item->id }}"
                                                        data-name="{{ $item->name }}"
                                                        data-store_id="{{ $item->store_id }}"
                                                        data-category_id="{{ $item->category_id }}"
                                                        data-code="{{ $item->code }}"
                                                        data-unit_id="{{ $item->unit_id }}"
                                                        data-brand_id="{{ $item->brand_id }}"
                                                        data-price="{{ $item->price }}"
                                                        data-sale_price="{{ $item->sale_price }}"
                                                        data-stock="{{ $item->stock }}"
                                                        data-details="{{ $item->details }}"
                                                        data-picture="{{ $item->picture_url }}"
                                                        data-status="{{ $item->status }}"
                                                        data-position="{{ $item->position }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#itemModal">
                                                        <i class='bx bx-edit text-primary'></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete Item --}}
                                                @can('item delete')
                                                    <a href="{{ route('items.delete', $item->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this item?')"
                                                        class="text-danger fs-5">
                                                        <i class='bx bx-trash text-danger'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">No items available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Item Modal (Add / Edit) - Landscape Mode -->
                    <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <form id="itemForm" action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title fw-bold" id="itemModalLabel">Add New Item</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            {{-- Row 1: Identification (3 columns) --}}
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="itemName" class="form-control" placeholder="Item Name" required>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                                                <input type="text" name="code" id="itemCode" class="form-control" placeholder="Item Code" required>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Store <span class="text-danger">*</span></label>
                                                <select name="store_id" id="itemStore" class="form-select" required>
                                                    <option value="">Select Store</option>
                                                    @foreach ($stores as $store)
                                                        <option value="{{ $store->id }}">
                                                            {{ $store->store ?? $store->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Row 2: Classification (3 columns) --}}
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Item Category <span class="text-danger">*</span></label>
                                                <select name="category_id" id="itemCategory" class="form-select" required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $cat)
                                                        <option value="{{ $cat->id }}">
                                                            {!! $cat->full_path ?? $cat->category ?? $cat->name !!}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                                                <select name="unit_id" id="itemUnit" class="form-select" required>
                                                    <option value="">Select Unit</option>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">
                                                            {{ $unit->unit ?? $unit->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Brand</label>
                                                <select name="brand_id" id="itemBrand" class="form-select">
                                                    <option value="">Select Brand</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}">
                                                            {{ $brand->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Row 3: Pricing, Stock & Status (4 columns) --}}
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Purchase Price <span class="text-danger">*</span></label>
                                                <input type="number" step="any" min="0" name="price" id="itemPrice" class="form-control" value="0" required>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Sale Price <span class="text-danger">*</span></label>
                                                <input type="number" step="any" min="0" name="sale_price" id="itemSalePrice" class="form-control" value="0" required>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Minimum Stock (in hand)</label>
                                                <input type="number" step="any" min="0" name="stock" id="itemStock" class="form-control" value="0">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                <select name="status" id="itemStatus" class="form-select" required>
                                                    <option value="A">Active</option>
                                                    <option value="I">Inactive</option>
                                                </select>
                                            </div>

                                            {{-- Row 4: Details & Picture (Landscape split) --}}
                                            <div class="col-md-7">
                                                <label class="form-label fw-semibold">Details</label>
                                                <input type="text" name="details" id="itemDetails" class="form-control" placeholder="Optional details, notes, etc.">
                                            </div>

                                            <div class="col-md-5">
                                                <label class="form-label fw-semibold">Picture</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" name="picture" id="itemPicture" class="form-control" accept="image/*">
                                                    <div id="imagePreviewContainer" class="d-none flex-shrink-0">
                                                        <img id="imagePreview" src="" alt="Preview" style="height: 38px; width: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Position (Hidden) --}}
                                            <input type="hidden" name="position" id="itemPosition" value="0">
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Clear</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary bg-dark border-dark px-4">Save Item</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- View Item Modal - Landscape Mode -->
                    <div class="modal fade" id="viewItemModal" tabindex="-1" aria-labelledby="viewItemModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-bottom py-3">
                                    <h5 class="modal-title fw-bold" id="viewItemModalLabel">Item Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-4 text-center border-end pe-md-3 d-flex flex-column align-items-center justify-content-center">
                                            <div id="v_picture_container" class="mb-3 w-100">
                                                <img id="v_picture" src="" alt="Item Image" style="max-width: 100%; max-height: 180px; border-radius: 8px; border: 1px solid #ddd; object-fit: contain;">
                                            </div>
                                            <div id="v_status"></div>
                                        </div>
                                        <div class="col-md-8 ps-md-3">
                                            <table class="table table-bordered table-striped table-sm mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%;">Name</th>
                                                        <td id="v_name" class="text-dark fw-semibold"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Code</th>
                                                        <td id="v_code" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Store</th>
                                                        <td id="v_store" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Category</th>
                                                        <td id="v_category" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Unit</th>
                                                        <td id="v_unit" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Brand</th>
                                                        <td id="v_brand" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Purchase Price</th>
                                                        <td id="v_price" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Sale Price</th>
                                                        <td id="v_sale_price" class="text-success fw-bold"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Min Stock</th>
                                                        <td id="v_stock" class="text-dark"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Details</th>
                                                        <td id="v_details" class="text-dark"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-2">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="layout-overlay layout-menu-toggle"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemForm = document.getElementById('itemForm');
            const modalTitle = document.getElementById('itemModalLabel');
            const submitBtn = document.getElementById('submitBtn');

            const itemName = document.getElementById('itemName');
            const itemStore = document.getElementById('itemStore');
            const itemCategory = document.getElementById('itemCategory');
            const itemCode = document.getElementById('itemCode');
            const itemUnit = document.getElementById('itemUnit');
            const itemBrand = document.getElementById('itemBrand');
            const itemPrice = document.getElementById('itemPrice');
            const itemSalePrice = document.getElementById('itemSalePrice');
            const itemStock = document.getElementById('itemStock');
            const itemDetails = document.getElementById('itemDetails');
            const itemPicture = document.getElementById('itemPicture');
            const itemStatus = document.getElementById('itemStatus');
            const itemPosition = document.getElementById('itemPosition');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const imagePreview = document.getElementById('imagePreview');

            const defaultStoreRoute = "{{ route('items.store') }}";

            // Live image preview
            if (itemPicture) {
                itemPicture.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreviewContainer.classList.remove('d-none');
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            window.resetItemForm = function() {
                itemForm.action = defaultStoreRoute;
                modalTitle.textContent = 'Add New Item';
                submitBtn.textContent = 'Save Item';
                itemForm.reset();

                itemPrice.value = "0";
                itemSalePrice.value = "0";
                itemStock.value = "0";
                itemPosition.value = "0";
                itemStatus.value = "A";

                imagePreview.src = "";
                imagePreviewContainer.classList.add('d-none');
            };

            // Edit Item
            document.querySelectorAll('.edit-item-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const storeId = this.dataset.store_id;
                    const categoryId = this.dataset.category_id;
                    const code = this.dataset.code;
                    const unitId = this.dataset.unit_id;
                    const brandId = this.dataset.brand_id;
                    const price = this.dataset.price;
                    const salePrice = this.dataset.sale_price;
                    const stock = this.dataset.stock;
                    const details = this.dataset.details;
                    const picture = this.dataset.picture;
                    let status = this.dataset.status;
                    const position = this.dataset.position;

                    if (status === "active" || status === "1" || status === "A") {
                        status = "A";
                    } else {
                        status = "I";
                    }

                    // Update form action for editing
                    itemForm.action = `/items/${id}/update`;
                    modalTitle.textContent = 'Edit Item';
                    submitBtn.textContent = 'Update Item';

                    // Populate form fields
                    itemName.value = name || '';
                    itemStore.value = storeId || '';
                    itemCategory.value = categoryId || '';
                    itemCode.value = code || '';
                    itemUnit.value = unitId || '';
                    itemBrand.value = brandId || '';
                    itemPrice.value = price || '0';
                    itemSalePrice.value = salePrice || '0';
                    itemStock.value = stock || '0';
                    itemDetails.value = details || '';
                    itemStatus.value = status;
                    itemPosition.value = position || '0';

                    if (picture) {
                        imagePreview.src = picture;
                        imagePreviewContainer.classList.remove('d-none');
                    } else {
                        imagePreview.src = "";
                        imagePreviewContainer.classList.add('d-none');
                    }
                });
            });

            // View Item Details
            document.querySelectorAll('.view-item-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('v_name').textContent = this.dataset.name || '-';
                    document.getElementById('v_code').textContent = this.dataset.code || '-';
                    document.getElementById('v_store').textContent = this.dataset.store || '-';
                    document.getElementById('v_category').innerHTML = this.dataset.category || '-';
                    document.getElementById('v_unit').textContent = this.dataset.unit || '-';
                    document.getElementById('v_brand').textContent = this.dataset.brand || '-';
                    document.getElementById('v_price').textContent = this.dataset.price || '0';
                    document.getElementById('v_sale_price').textContent = this.dataset.sale_price || '0';
                    document.getElementById('v_stock').textContent = this.dataset.stock || '0';
                    document.getElementById('v_details').textContent = this.dataset.details || '-';

                    const picture = this.dataset.picture;
                    const vPicture = document.getElementById('v_picture');
                    const vPictureContainer = document.getElementById('v_picture_container');
                    if (picture) {
                        vPicture.src = picture;
                        vPictureContainer.classList.remove('d-none');
                    } else {
                        vPictureContainer.classList.add('d-none');
                    }

                    const status = this.dataset.status;
                    const vStatus = document.getElementById('v_status');
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
