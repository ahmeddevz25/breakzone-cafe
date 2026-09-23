@extends('admin.layouts')
@section('title', 'Store Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Store Management</h5>
                        @can('store add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#storeModal" onclick="resetStoreForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add Store</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Stores Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>#</th>
                                    <th>Store</th>
                                    <th>Printer IP</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stores as $key => $store)
                                    <tr class="text-dark">
                                        <td class="text-dark fw-medium">{{ $key + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ $store->store ?? $store->name }}</td>
                                        <td class="text-dark">
                                            @if($store->printer_ip_address)
                                                <span class="font-monospace fw-medium text-dark">{{ $store->printer_ip_address }}{{ $store->printer_port ? ':' . $store->printer_port : '' }}</span>
                                            @elseif($store->printer_ip)
                                                <span class="font-monospace fw-medium text-dark">{{ $store->printer_ip }}{{ $store->printer_port ? ':' . $store->printer_port : '' }}</span>
                                            @else
                                                <span class="text-secondary small fw-medium">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($store->status == 'A' || $store->status == 'active' || $store->status === '1' || $store->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="table-actions">
                                                {{-- Edit --}}
                                                @can('store edit')
                                                    <button type="button" title="Edit"
                                                        class="action-btn action-btn-edit edit-store-btn"
                                                        data-id="{{ $store->id }}" 
                                                        data-store="{{ $store->store ?? $store->name }}" 
                                                        data-ip="{{ $store->printer_ip_address ?? $store->printer_ip }}"
                                                        data-port="{{ $store->printer_port }}"
                                                        data-balance="{{ $store->opening_balance ?? 0 }}"
                                                        data-position="{{ $store->position ?? 0 }}"
                                                        data-status="{{ $store->status }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#storeModal">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete --}}
                                                @can('store delete')
                                                    <a href="{{ route('stores.delete', $store->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this store?')"
                                                        class="action-btn action-btn-delete">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No stores available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Store Modal (Add / Edit) -->
                    <div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="storeForm" action="{{ route('stores.store') }}" method="POST">
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const storeForm = document.getElementById('storeForm');
            const modalTitle = document.getElementById('storeModalLabel');
            const submitBtn = document.getElementById('submitBtn');
            
            const storeName = document.getElementById('storeName');
            const printerIp = document.getElementById('printerIp');
            const printerPort = document.getElementById('printerPort');
            const openingBalance = document.getElementById('openingBalance');
            const storePosition = document.getElementById('storePosition');
            const storeStatus = document.getElementById('storeStatus');
            
            const storeRoute = "{{ route('stores.store') }}";

            window.resetStoreForm = function() {
                storeForm.action = storeRoute;
                modalTitle.textContent = 'Add New Store';
                submitBtn.textContent = 'Save Store';
                storeForm.reset();
                printerPort.value = "9100";
                openingBalance.value = "0";
                storePosition.value = "0";
                storeStatus.value = "A";
            };

            // Edit Store
            document.querySelectorAll('.edit-store-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const store = this.dataset.store;
                    const ip = this.dataset.ip;
                    const port = this.dataset.port;
                    const balance = this.dataset.balance;
                    const position = this.dataset.position;
                    let status = this.dataset.status;
                    if (status === "active" || status === "1" || status === "A") {
                        status = "A";
                    } else {
                        status = "I";
                    }

                    storeForm.action = `/stores/${id}/update`;
                    modalTitle.textContent = 'Edit Store';
                    submitBtn.textContent = 'Update Store';
                    
                    storeName.value = store || '';
                    printerIp.value = ip || '';
                    printerPort.value = port || '9100';
                    openingBalance.value = balance || '0';
                    storePosition.value = position || '0';
                    storeStatus.value = status;
                });
            });


        });
    </script>
@endsection
