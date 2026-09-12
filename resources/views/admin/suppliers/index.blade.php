@extends('admin.layouts')
@section('title', 'Suppliers Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Suppliers Management</h5>
                        @can('supplier add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#supplierModal" onclick="resetSupplierForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add New Supplier</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Suppliers Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suppliers as $key => $supplier)
                                    <tr class="text-dark">
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-semibold text-dark">{{ $supplier->name }}</td>
                                        <td class="text-dark">{{ $supplier->company }}</td>
                                        <td class="text-dark">{{ $supplier->mobile }}</td>
                                        <td>
                                            @if ($supplier->status == 'A' || $supplier->status == 'active' || $supplier->status === '1' || $supplier->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" title="View Details"
                                                    class="btn btn-link text-info fs-5 p-0 view-supplier-btn"
                                                    data-name="{{ $supplier->name }}" 
                                                    data-company="{{ $supplier->company }}"
                                                    data-address="{{ $supplier->address }}"
                                                    data-mobile="{{ $supplier->mobile }}"
                                                    data-ntn="{{ $supplier->ntn_no ?? $supplier->ntn }}"
                                                    data-email="{{ $supplier->email }}"
                                                    data-status="{{ $supplier->status }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewSupplierModal">
                                                    <i class='bx bx-show'></i>
                                                </button>

                                                @can('supplier edit')
                                                    <button type="button" title="Edit"
                                                        class="btn btn-link text-primary fs-5 p-0 edit-supplier-btn"
                                                        data-id="{{ $supplier->id }}" 
                                                        data-name="{{ $supplier->name }}" 
                                                        data-company="{{ $supplier->company }}"
                                                        data-address="{{ $supplier->address }}"
                                                        data-mobile="{{ $supplier->mobile }}"
                                                        data-ntn="{{ $supplier->ntn_no ?? $supplier->ntn }}"
                                                        data-email="{{ $supplier->email }}"
                                                        data-status="{{ $supplier->status }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#supplierModal">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                @can('supplier delete')
                                                    <a href="{{ route('suppliers.delete', $supplier->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this supplier?')"
                                                        class="text-danger fs-5">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No suppliers available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Supplier Modal -->
                    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form id="supplierForm" action="{{ route('suppliers.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="supplierModalLabel">Add New Supplier</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row g-3">
                                            {{-- Name --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="supplierName" class="form-control" required>
                                            </div>

                                            {{-- Company --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Company <span class="text-danger">*</span></label>
                                                <input type="text" name="company" id="supplierCompany" class="form-control" required>
                                            </div>

                                            {{-- Mobile --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                                <input type="text" name="mobile" id="supplierMobile" class="form-control" required>
                                            </div>

                                            {{-- NTN --}}
                                            <div class="col-md-6">
                                                <label class="form-label">NTN #</label>
                                                <input type="text" name="ntn_no" id="supplierNtn" class="form-control">
                                            </div>

                                            {{-- Email Address --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" name="email" id="supplierEmail" class="form-control">
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                                <select name="status" id="supplierStatus" class="form-select" required>
                                                    <option value="A">Active</option>
                                                    <option value="I">Inactive</option>
                                                </select>
                                            </div>

                                            {{-- Address --}}
                                            <div class="col-12">
                                                <label class="form-label">Address</label>
                                                <input type="text" name="address" id="supplierAddress" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Clear</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary bg-dark border-dark">Save Supplier</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- View Supplier Modal -->
                    <div class="modal fade" id="viewSupplierModal" tabindex="-1" aria-labelledby="viewSupplierModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header border-bottom">
                                    <h5 class="modal-title" id="viewSupplierModalLabel">Supplier Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                            <tr>
                                                <th style="width: 35%;">Name</th>
                                                <td id="v_name" class="text-dark fw-semibold"></td>
                                            </tr>
                                            <tr>
                                                <th>Company</th>
                                                <td id="v_company" class="text-dark"></td>
                                            </tr>
                                            <tr>
                                                <th>Mobile</th>
                                                <td id="v_mobile" class="text-dark"></td>
                                            </tr>
                                            <tr>
                                                <th>Email Address</th>
                                                <td id="v_email" class="text-dark"></td>
                                            </tr>
                                            <tr>
                                                <th>NTN #</th>
                                                <td id="v_ntn" class="text-dark"></td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td id="v_status"></td>
                                            </tr>
                                            <tr>
                                                <th>Address</th>
                                                <td id="v_address" class="text-dark"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer border-top">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            const supplierForm = document.getElementById('supplierForm');
            const modalTitle = document.getElementById('supplierModalLabel');
            const submitBtn = document.getElementById('submitBtn');
            
            const supplierName = document.getElementById('supplierName');
            const supplierCompany = document.getElementById('supplierCompany');
            const supplierAddress = document.getElementById('supplierAddress');
            const supplierMobile = document.getElementById('supplierMobile');
            const supplierNtn = document.getElementById('supplierNtn');
            const supplierEmail = document.getElementById('supplierEmail');
            const supplierStatus = document.getElementById('supplierStatus');
            
            const supplierRoute = "{{ route('suppliers.store') }}";

            window.resetSupplierForm = function() {
                supplierForm.action = supplierRoute;
                modalTitle.textContent = 'Add New Supplier';
                submitBtn.textContent = 'Save Supplier';
                supplierForm.reset();
                supplierStatus.value = "A"; // Default to Active (A)
            };

            document.querySelectorAll('.edit-supplier-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const company = this.dataset.company;
                    const address = this.dataset.address;
                    const mobile = this.dataset.mobile;
                    const ntn = this.dataset.ntn;
                    const email = this.dataset.email;
                    let status = this.dataset.status;
                    if (status === "active" || status === "1" || status === "A") {
                        status = "A";
                    } else {
                        status = "I";
                    }

                    // Update form action for editing
                    supplierForm.action = `/suppliers/${id}/update`;
                    modalTitle.textContent = 'Edit Supplier';
                    submitBtn.textContent = 'Update Supplier';
                    
                    supplierName.value = name;
                    supplierCompany.value = company;
                    supplierAddress.value = address;
                    supplierMobile.value = mobile;
                    supplierNtn.value = ntn;
                    supplierEmail.value = email;
                    supplierStatus.value = status;
                });
            });

            document.querySelectorAll('.view-supplier-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('v_name').textContent = this.dataset.name || 'N/A';
                    document.getElementById('v_company').textContent = this.dataset.company || 'N/A';
                    document.getElementById('v_mobile').textContent = this.dataset.mobile || 'N/A';
                    document.getElementById('v_email').textContent = this.dataset.email || 'N/A';
                    document.getElementById('v_ntn').textContent = this.dataset.ntn || 'N/A';
                    document.getElementById('v_address').textContent = this.dataset.address || 'N/A';
                    
                    const statusVal = this.dataset.status;
                    const statusBadge = (statusVal === 'A' || statusVal === 'active' || statusVal === '1') 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-danger">Inactive</span>';
                    document.getElementById('v_status').innerHTML = statusBadge;
                });
            });
        });
    </script>
@endsection
