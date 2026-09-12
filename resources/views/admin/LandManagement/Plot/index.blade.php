@extends('admin.layouts')
@section('content')
    @include('sweetalert::alert')

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">

                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <!-- Header -->
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Plot Management</h5>
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" id="addPlotBtn">
                            <i class="bx bx-plus icon-sm"></i>
                            <span class="d-none d-sm-inline-block">Add Plot</span>
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>Sr. No</th>
                                    <th>Phase / Block</th>
                                    <th>Plot No</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Size (Marla)</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($plots as $key => $plot)
                                    <tr id="plotRow{{ $plot->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $plot->phase->phase_name ?? 'N/A' }}</span><br>
                                            <small class="text-muted">{{ $plot->block->block_name ?? 'N/A' }}</small>
                                        </td>
                                        <td class="fw-semibold">{{ $plot->plot_no }}</td>
                                        <td><span class="badge bg-label-info">{{ $plot->plot_type }}</span></td>
                                        <td>{{ $plot->plot_category }}</td>
                                        <td>{{ $plot->plot_size }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary editPlotBtn"
                                                    data-id="{{ $plot->id }}">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger deletePlotBtn"
                                                    data-id="{{ $plot->id }}">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noDataRow">
                                        <td colspan="7" class="text-center text-muted">No plots found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div> <!-- card -->
            </div> <!-- layout-page -->
        </div> <!-- layout-container -->
    </div> <!-- wrapper -->

    <!-- Plot Modal -->
    <div class="modal fade" id="plotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="plotForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" id="plotId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Plot</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Select Phase</label>
                                <select name="phase_id" id="phase_id" class="form-select" required>
                                    <option value="">-- Select Phase --</option>
                                    @foreach ($phases as $phase)
                                        <option value="{{ $phase->id }}">{{ $phase->phase_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error_phase_id"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Select Block</label>
                                <select name="block_id" id="block_id" class="form-select" required disabled>
                                    <option value="">-- Select Block --</option>
                                </select>
                                <div class="invalid-feedback" id="error_block_id"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Plot Type</label>
                                <select name="plot_type" id="plot_type" class="form-select" required>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Industrial">Industrial</option>
                                </select>
                                <div class="invalid-feedback" id="error_plot_type"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Plot Category</label>
                                <select name="plot_category" id="plot_category" class="form-select" required>
                                    <option value="">Select Plot Category</option>
                                    <option value="general">General</option>
                                    <option value="park_face">Park Face</option>
                                    <option value="corner">Corner</option>
                                    <option value="main_bulevard">Boulevard</option>
                                </select>
                                <div class="invalid-feedback" id="error_plot_category"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Plot No</label>
                                <input type="text" name="plot_no" id="plot_no" class="form-control" placeholder="e.g. A-123"
                                    required>
                                <div class="invalid-feedback" id="error_plot_no"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Plot Size</label>
                                <input type="text" name="plot_size" id="plot_size" class="form-control"
                                    placeholder="e.g. 5 Marla" required>
                                <div class="invalid-feedback" id="error_plot_size"></div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                                <div class="invalid-feedback" id="error_description"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Plot</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                const plotModal = new bootstrap.Modal(document.getElementById('plotModal'));
                const plotForm = document.getElementById('plotForm');

                // Dependent Dropdown Logic
                $('#phase_id').change(function () {
                    let phaseId = $(this).val();
                    let blockSelect = $('#block_id');

                    blockSelect.empty().append('<option value="">-- Select Block --</option>');

                    if (phaseId) {
                        $.get(`/get-blocks-by-phase/${phaseId}`, function (data) {
                            blockSelect.prop('disabled', false);
                            $.each(data, function (index, block) {
                                blockSelect.append(
                                    `<option value="${block.id}">${block.block_name}</option>`
                                );
                            });

                            // If editing, select the correct block
                            let currentBlockId = $('#plotId').data('current-block');
                            if (currentBlockId) {
                                blockSelect.val(currentBlockId);
                                $('#plotId').data('current-block', ''); // Clear it
                            }
                        });
                    } else {
                        blockSelect.prop('disabled', true);
                    }
                });

                // Open Modal for Add
                $('#addPlotBtn').click(function () {
                    $('#modalTitle').text('Add New Plot');
                    $('#saveBtn').text('Save Plot');
                    $('#formMethod').val('POST');
                    $('#plotId').val('');
                    plotForm.reset();
                    $('#block_id').prop('disabled', true).empty().append(
                        '<option value="">-- Select Block --</option>');
                    $('.is-invalid').removeClass('is-invalid');
                    plotModal.show();
                });

                // Handle Form Submission
                $('#plotForm').submit(function (e) {
                    e.preventDefault();
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    let id = $('#plotId').val();
                    let url = id ? `/plots/${id}` : "/plots";
                    let formData = $(this).serialize();

                    $.ajax({
                        url: url,
                        type: "POST",
                        data: formData,
                        beforeSend: function () {
                            $('#saveBtn').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                            );
                        },
                        success: function (response) {
                            plotModal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            $('#saveBtn').prop('disabled', false).text(id ? 'Update Plot' :
                                'Save Plot');
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#error_${key}`).text(value[0]);
                                });
                            } else {
                                Swal.fire('Error', 'Something went wrong!', 'error');
                            }
                        }
                    });
                });

                // Open Modal for Edit
                $(document).on('click', '.editPlotBtn', function () {
                    let id = $(this).data('id');
                    $('.is-invalid').removeClass('is-invalid');

                    $.get(`/plots/${id}`, function (data) {
                        $('#modalTitle').text('Edit Plot');
                        $('#saveBtn').text('Update Plot');
                        $('#formMethod').val('PUT');
                        $('#plotId').val(data.id);
                        $('#plotId').data('current-block', data.block_id); // Storage for dropdown logic

                        $('#phase_id').val(data.phase_id).trigger('change');
                        $('#plot_type').val(data.plot_type);
                        $('#plot_category').val(data.plot_category);
                        $('#plot_no').val(data.plot_no);
                        $('#plot_size').val(data.plot_size);
                        $('#description').val(data.description);

                        plotModal.show();
                    });
                });

                // Handle Delete
                $(document).on('click', '.deletePlotBtn', function () {
                    let id = $(this).data('id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/plots/${id}`,
                                type: 'DELETE',
                                success: function (response) {
                                    $(`#plotRow${id}`).remove();
                                    Swal.fire('Deleted!', response.message, 'success');
                                    if ($('#example tbody tr').length === 0) {
                                        $('#example tbody').append(
                                            '<tr id="noDataRow"><td colspan="7" class="text-center text-muted">No plots found.</td></tr>'
                                        );
                                    }
                                },
                                error: function () {
                                    Swal.fire('Error', 'Could not delete the record.',
                                        'error');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection