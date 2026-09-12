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
                                    <th>Opening Balance</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stores as $key => $store)
                                    <tr class="text-dark">
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-semibold text-dark">{{ $store->store ?? $store->name }}</td>
                                        <td class="text-dark">
                                            @if($store->printer_ip_address)
                                                {{ $store->printer_ip_address }}{{ $store->printer_port ? ':' . $store->printer_port : '' }}
                                            @elseif($store->printer_ip)
                                                {{ $store->printer_ip }}{{ $store->printer_port ? ':' . $store->printer_port : '' }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($store->opening_balance ?? 0) }}</td>
                                        <td>
                                            @if ($store->status == 'A' || $store->status == 'active' || $store->status === '1' || $store->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('store edit')
                                                    <button type="button" title="Edit"
                                                        class="btn btn-link text-primary fs-5 p-0 edit-store-btn"
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

                                                @can('store delete')
                                                    <a href="{{ route('stores.delete', $store->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this store?')"
                                                        class="text-danger fs-5">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No stores available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Store Modal -->
                    <div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="storeForm" action="{{ route('stores.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="storeModalLabel">Add New Store</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        {{-- Store Name --}}
                                        <div class="mb-3">
                                            <label class="form-label">Store <span class="text-danger">*</span></label>
                                            <input type="text" name="store" id="storeName" class="form-control" required>
                                        </div>

                                        {{-- Printer IP --}}
                                        <div class="mb-3">
                                            <label class="form-label">Printer IP Address</label>
                                            <input type="text" name="printer_ip_address" id="printerIp" class="form-control" placeholder="192.168.1.100">
                                        </div>

                                        {{-- Printer Port --}}
                                        <div class="mb-3">
                                            <label class="form-label">Printer Port</label>
                                            <input type="text" name="printer_port" id="printerPort" class="form-control" value="9100">
                                        </div>

                                        {{-- Opening Balance --}}
                                        <div class="mb-3">
                                            <label class="form-label">Opening Balance</label>
                                            <input type="number" name="opening_balance" id="openingBalance" class="form-control" value="0">
                                        </div>

                                        {{-- Position / Order --}}
                                        <div class="mb-3">
                                            <label class="form-label">Position</label>
                                            <input type="number" name="position" id="storePosition" class="form-control" value="0">
                                        </div>

                                        {{-- Status --}}
                                        <div class="mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="storeStatus" class="form-select" required>
                                                <option value="A">Active</option>
                                                <option value="I">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Clear / Cancel</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary bg-dark border-dark">Save Store</button>
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
                storeStatus.value = "A"; // Default to Active (A)
            };

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

                    // Update form action for editing
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
