@extends('admin.layouts')
@section('title', 'Units Management')
@section('content')

    @include('sweetalert::alert')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Units Management</h5>
                        @can('unit add')
                            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                data-bs-target="#unitModal" onclick="resetUnitForm()">
                                <i class="bx bx-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Add New Unit</span>
                            </button>
                        @endcan
                    </div>

                    <!-- Units Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>#</th>
                                    <th>Unit Name</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th class="text-center">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($units as $key => $unit)
                                    <tr class="text-dark">
                                        <td class="text-dark fw-medium">{{ $key + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ $unit->unit ?? $unit->name }}</td>
                                        <td class="text-dark fw-medium">{{ $unit->quantity ?? 0 }}</td>
                                        <td>
                                            @if ($unit->status == 'A' || $unit->status == 'active' || $unit->status === '1' || $unit->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="table-actions">
                                                {{-- Edit --}}
                                                @can('unit edit')
                                                    <button type="button" title="Edit"
                                                        class="action-btn action-btn-edit edit-unit-btn"
                                                        data-id="{{ $unit->id }}" 
                                                        data-unit="{{ $unit->unit ?? $unit->name }}" 
                                                        data-quantity="{{ $unit->quantity ?? 0 }}"
                                                        data-position="{{ $unit->position ?? 0 }}"
                                                        data-status="{{ $unit->status }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#unitModal">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete --}}
                                                @can('unit delete')
                                                    <a href="{{ route('units.delete', $unit->id) }}" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this unit?')"
                                                        class="action-btn action-btn-delete">
                                                        <i class='bx bx-trash'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No units available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Unit Modal (Add / Edit) -->
                    <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="unitForm" action="{{ route('units.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title fw-bold" id="unitModalLabel">Add New Unit</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- Unit Name --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                                            <input type="text" name="unit" id="unitName" class="form-control" placeholder="e.g. Kg, Pcs, Box, Liter" required>
                                        </div>

                                        {{-- Quantity --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Quantity / Value</label>
                                            <input type="number" step="any" name="quantity" id="unitQuantity" class="form-control" value="0">
                                        </div>

                                        {{-- Position --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Position / Order</label>
                                            <input type="number" name="position" id="unitPosition" class="form-control" value="0">
                                        </div>

                                        {{-- Status --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="unitStatus" class="form-select" required>
                                                <option value="A">Active</option>
                                                <option value="I">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary px-4">Save Unit</button>
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
            const unitForm = document.getElementById('unitForm');
            const modalTitle = document.getElementById('unitModalLabel');
            const submitBtn = document.getElementById('submitBtn');
            
            const unitName = document.getElementById('unitName');
            const unitQuantity = document.getElementById('unitQuantity');
            const unitPosition = document.getElementById('unitPosition');
            const unitStatus = document.getElementById('unitStatus');
            
            const unitRoute = "{{ route('units.store') }}";

            window.resetUnitForm = function() {
                unitForm.action = unitRoute;
                modalTitle.textContent = 'Add New Unit';
                submitBtn.textContent = 'Save Unit';
                unitForm.reset();
                unitQuantity.value = "0";
                unitPosition.value = "0";
                unitStatus.value = "A";
            };

            // Edit Unit
            document.querySelectorAll('.edit-unit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const unit = this.dataset.unit;
                    const quantity = this.dataset.quantity;
                    const position = this.dataset.position;
                    let status = this.dataset.status;
                    if (status === "active" || status === "1" || status === "A") {
                        status = "A";
                    } else {
                        status = "I";
                    }

                    unitForm.action = `/units/${id}/update`;
                    modalTitle.textContent = 'Edit Unit';
                    submitBtn.textContent = 'Update Unit';
                    
                    unitName.value = unit || '';
                    unitQuantity.value = quantity || '0';
                    unitPosition.value = position || '0';
                    unitStatus.value = status;
                });
            });


        });
    </script>
@endsection
