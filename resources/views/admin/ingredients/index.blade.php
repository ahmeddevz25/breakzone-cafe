@extends('admin.layouts')
@section('title', 'Ingredients Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Ingredients Management</h5>
                        <div class="d-flex align-items-center gap-2">
                            <select id="statusFilter" class="form-select form-select-sm" style="width: 140px;">
                                <option value="">All Status</option>
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                            @can('ingredient add')
                                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#ingredientModal" onclick="resetIngredientForm()">
                                    <i class="bx bx-plus icon-sm"></i>
                                    <span class="d-none d-sm-inline-block">Add New Ingredient</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Ingredients Table (Server-Side AJAX) -->
                    <div class="table-responsive p-3">
                        <table class="table table-hover align-middle table-striped border-top w-100" id="ingredientsTable">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Ingredient</th>
                                    <th>Buying Unit</th>
                                    <th>Usage Unit</th>
                                    <th>Purchase Price</th>
                                    <th>Stock</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-center" style="width: 130px;">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamically populated via DataTables Server-Side AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Ingredient Modal (Add / Edit) -->
                    <div class="modal fade" id="ingredientModal" tabindex="-1" aria-labelledby="ingredientModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form id="ingredientForm" action="{{ route('ingredients.store') }}" method="POST" enctype="multipart/form-data" data-ajax-table="#ingredientsTable">
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Server-Side Processing AJAX DataTable Setup for Ingredients
            const dataTable = $('#ingredientsTable').DataTable({
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
                        console.error('Ingredients DataTable Error:', error, xhr.responseText);
                    }
                },
                dom: 'lfrtip',
                columns: [
                    { data: 'index', name: 'index', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'ingredient', name: 'ingredient' },
                    { data: 'buying_unit', name: 'buying_unit' },
                    { data: 'usage_unit', name: 'usage_unit' },
                    { data: 'purchase_price', name: 'purchase_price' },
                    { data: 'stock', name: 'stock' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'options', name: 'options', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "_INPUT_",
                    searchPlaceholder: "Search ingredients, units, stores...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ ingredients",
                    infoEmpty: "Showing 0 to 0 of 0 ingredients",
                    infoFiltered: "(filtered from _MAX_ total ingredients)",
                    zeroRecords: "No matching ingredients found",
                    emptyTable: "No ingredients available"
                }
            });

            // Status Filter Change
            $('#statusFilter').on('change', function() {
                dataTable.ajax.reload();
            });

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

            // Delegated Edit Ingredient
            $(document).on('click', '.edit-ingredient-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const storeId = $(this).data('store_id');
                const buyingUnitId = $(this).data('buying_unit_id');
                const usageUnitId = $(this).data('usage_unit_id');
                const conversion = $(this).data('conversion_value');
                const price = $(this).data('purchase_price');
                const stock = $(this).data('stock');
                const details = $(this).data('details');
                const picture = $(this).data('picture');
                let status = $(this).data('status');
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

            // Delegated View Ingredient Details
            $(document).on('click', '.view-ingredient-btn', function() {
                $('#v_ing_name').text($(this).data('name') || '-');
                $('#v_ing_store').text($(this).data('store') || '-');
                $('#v_ing_buying_unit').text($(this).data('buying_unit') || '-');
                $('#v_ing_usage_unit').text($(this).data('usage_unit') || '-');
                $('#v_ing_conversion').text($(this).data('conversion') || '0');
                $('#v_ing_price').text($(this).data('purchase_price') || '0.00');
                $('#v_ing_stock').text($(this).data('stock') || '0.00');
                $('#v_ing_details').text($(this).data('details') || '-');

                const picture = $(this).data('picture');
                if (picture && picture.trim() !== '') {
                    $('#v_ing_picture').attr('src', picture);
                    $('#v_ing_picture_container').removeClass('d-none');
                    $('#v_ing_no_picture').addClass('d-none');
                } else {
                    $('#v_ing_picture').attr('src', '');
                    $('#v_ing_picture_container').addClass('d-none');
                    $('#v_ing_no_picture').removeClass('d-none');
                }

                const status = $(this).data('status');
                if (status === 'A' || status === 'active' || status === '1') {
                    $('#v_ing_status').html('<span class="badge bg-success">Active</span>');
                } else {
                    $('#v_ing_status').html('<span class="badge bg-danger">Inactive</span>');
                }
            });

            // Delegated AJAX Delete with SweetAlert2
            $(document).on('click', '.delete-ingredient-ajax-btn', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const name = $(this).data('name') || 'this ingredient';

                Swal.fire({
                    title: 'Delete Ingredient?',
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
                                Swal.fire('Deleted!', res.message || 'Ingredient has been deleted.', 'success');
                                dataTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to delete ingredient.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
