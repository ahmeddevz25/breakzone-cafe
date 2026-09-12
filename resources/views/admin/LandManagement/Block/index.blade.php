@extends('admin.layouts')
@section('content')
    @include('sweetalert::alert')

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">

                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <!-- Header -->
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Block Management</h5>
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" id="addBlockBtn">
                            <i class="bx bx-plus icon-sm"></i>
                            <span class="d-none d-sm-inline-block">Add Block</span>
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>Sr. No</th>
                                    <th>Phase</th>
                                    <th>Block Name</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($blocks as $key => $block)
                                    <tr id="blockRow{{ $block->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $block->phase->phase_name ?? 'N/A' }}</td>
                                        <td class="fw-semibold">{{ $block->block_name }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary editBlockBtn"
                                                    data-id="{{ $block->id }}">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger deleteBlockBtn"
                                                    data-id="{{ $block->id }}">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noDataRow">
                                        <td colspan="5" class="text-center text-muted">No blocks found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div> <!-- card -->
            </div> <!-- layout-page -->
        </div> <!-- layout-container -->
    </div> <!-- wrapper -->

    <!-- Block Modal -->
    <div class="modal fade" id="blockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="blockForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" id="blockId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Block</h5>
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
                                <label class="form-label">Block Name</label>
                                <input type="text" name="block_name" id="block_name" class="form-control" required>
                                <div class="invalid-feedback" id="error_block_name"></div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Block Description</label>
                                <textarea name="block_description" id="block_description" class="form-control" rows="4"></textarea>
                                <div class="invalid-feedback" id="error_block_description"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Block</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Setup CSRF token for AJAX
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                const blockModal = new bootstrap.Modal(document.getElementById('blockModal'));
                const blockForm = document.getElementById('blockForm');

                // Open Modal for Add
                $('#addBlockBtn').click(function() {
                    $('#modalTitle').text('Add New Block');
                    $('#saveBtn').text('Save Block');
                    $('#formMethod').val('POST');
                    $('#blockId').val('');
                    blockForm.reset();
                    $('.is-invalid').removeClass('is-invalid');
                    blockModal.show();
                });

                // Handle Form Submission
                $('#blockForm').submit(function(e) {
                    e.preventDefault();
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    let id = $('#blockId').val();
                    let url = id ? `/blocks/${id}` : "/blocks";
                    let formData = $(this).serialize();

                    $.ajax({
                        url: url,
                        type: "POST",
                        data: formData,
                        beforeSend: function() {
                            $('#saveBtn').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                            );
                        },
                        success: function(response) {
                            blockModal.hide();
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
                        error: function(xhr) {
                            $('#saveBtn').prop('disabled', false).text(id ? 'Update Block' :
                                'Save Block');
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
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
                $(document).on('click', '.editBlockBtn', function() {
                    let id = $(this).data('id');
                    $('.is-invalid').removeClass('is-invalid');

                    $.get(`/blocks/${id}`, function(data) {
                        $('#modalTitle').text('Edit Block');
                        $('#saveBtn').text('Update Block');
                        $('#formMethod').val('PUT');
                        $('#blockId').val(data.id);
                        $('#phase_id').val(data.phase_id);
                        $('#block_name').val(data.block_name);
                        $('#block_description').val(data.block_description);
                        blockModal.show();
                    });
                });

                // Handle Delete
                $(document).on('click', '.deleteBlockBtn', function() {
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
                                url: `/blocks/${id}`,
                                type: 'DELETE',
                                success: function(response) {
                                    $(`#blockRow${id}`).remove();
                                    Swal.fire('Deleted!', response.message, 'success');
                                    if ($('#blockTable tbody tr').length === 0) {
                                        $('#blockTable tbody').append(
                                            '<tr id="noDataRow"><td colspan="5" class="text-center text-muted">No blocks found.</td></tr>'
                                        );
                                    }
                                },
                                error: function() {
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
