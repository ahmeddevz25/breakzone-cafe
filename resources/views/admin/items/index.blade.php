@extends('admin.layouts')
@section('title', 'Items Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Items Management</h5>
                        <div class="d-flex align-items-center gap-2">
                            <select id="statusFilter" class="form-select form-select-sm" style="width: 140px;">
                                <option value="">All Status</option>
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                            @can('item add')
                                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#itemModal" onclick="resetItemForm()">
                                    <i class="bx bx-plus icon-sm"></i>
                                    <span class="d-none d-sm-inline-block">Add New Item</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Items Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100" id="itemsTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Item</th>
                                    <th>Code</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Purchase Price</th>
                                    <th>Sale Price</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-center" style="width: 130px;">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Item Modal (Add / Edit) - Landscape Mode -->
                    <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <form id="itemForm" action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" data-ajax-table="#itemsTable">
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

                                            {{-- Row 3: Pricing & Status (3 columns) --}}
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Purchase Price <span class="text-danger">*</span></label>
                                                <input type="number" step="any" min="0" name="price" id="itemPrice" class="form-control" value="0" required>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Sale Price <span class="text-danger">*</span></label>
                                                <input type="number" step="any" min="0" name="sale_price" id="itemSalePrice" class="form-control" value="0" required>
                                            </div>

                                            <div class="col-md-4">
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
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">Save Item</button>
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
            // Server-Side Processing AJAX DataTable Setup for Items
            const dataTable = $('#itemsTable').DataTable({
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
                        console.error('Items DataTable Error:', error, xhr.responseText);
                    }
                },
                dom: 'lfrtip',
                columns: [
                    { data: 'index', name: 'index', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'item', name: 'item' },
                    { data: 'code', name: 'code' },
                    { data: 'category', name: 'category' },
                    { data: 'stock', name: 'stock' },
                    { data: 'purchase_price', name: 'purchase_price' },
                    { data: 'sale_price', name: 'sale_price' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'options', name: 'options', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "_INPUT_",
                    searchPlaceholder: "Search items, codes, categories...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ items",
                    infoEmpty: "Showing 0 to 0 of 0 items",
                    infoFiltered: "(filtered from _MAX_ total items)",
                    zeroRecords: "No matching items found",
                    emptyTable: "No items available"
                }
            });

            // Filter by Status
            $('#statusFilter').on('change', function() {
                dataTable.ajax.reload();
            });

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
                itemPosition.value = "0";
                itemStatus.value = "A";

                imagePreview.src = "";
                imagePreviewContainer.classList.add('d-none');
            };

            // Delegated Edit Item
            $(document).on('click', '.edit-item-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const storeId = $(this).data('store_id');
                const categoryId = $(this).data('category_id');
                const code = $(this).data('code');
                const unitId = $(this).data('unit_id');
                const brandId = $(this).data('brand_id');
                const price = $(this).data('price');
                const salePrice = $(this).data('sale_price');
                const stock = $(this).data('stock');
                const details = $(this).data('details');
                const picture = $(this).data('picture');
                let status = $(this).data('status');
                const position = $(this).data('position');

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

            // Delegated View Item Details
            $(document).on('click', '.view-item-btn', function() {
                $('#v_name').text($(this).data('name') || '-');
                $('#v_code').text($(this).data('code') || '-');
                $('#v_store').text($(this).data('store') || '-');
                $('#v_category').html($(this).data('category') || '-');
                $('#v_unit').text($(this).data('unit') || '-');
                $('#v_brand').text($(this).data('brand') || '-');
                $('#v_price').text($(this).data('price') || '0');
                $('#v_sale_price').text($(this).data('sale_price') || '0');
                $('#v_stock').text($(this).data('stock') || '0');
                $('#v_details').text($(this).data('details') || '-');

                const picture = $(this).data('picture');
                if (picture) {
                    $('#v_picture').attr('src', picture);
                    $('#v_picture_container').removeClass('d-none');
                } else {
                    $('#v_picture_container').addClass('d-none');
                }

                const status = $(this).data('status');
                if (status === 'A' || status === 'active' || status === '1') {
                    $('#v_status').html('<span class="badge bg-success">Active</span>');
                } else {
                    $('#v_status').html('<span class="badge bg-danger">Inactive</span>');
                }
            });

            // Delegated AJAX Delete with SweetAlert2
            $(document).on('click', '.delete-item-ajax-btn', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const name = $(this).data('name') || 'this item';

                Swal.fire({
                    title: 'Delete Item?',
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
                                Swal.fire('Deleted!', res.message || 'Item has been deleted.', 'success');
                                dataTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to delete item.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
