@extends('admin.layouts')
@section('content')
    @include('sweetalert::alert')

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">

                <div class="card mt-5 shadow-sm rounded" style="margin: 31px;">
                    <!-- Header -->
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <h5 class="card-title mb-0 text-md-start text-center">Phase Management</h5>
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" id="addPhaseBtn">
                            <i class="bx bx-plus icon-sm"></i>
                            <span class="d-none d-sm-inline-block">Add Phase</span>
                        </button>
                    </div>

                    <!-- AI Assistant Box -->
                    <div class="card-body bg-light border-bottom mb-3" style="background-color: #f8f9fa !important; margin: 15px; border-radius: 8px;">
                        <div class="d-flex align-items-center mb-2">
                            <i class='bx bx-bot text-primary me-2' style="font-size: 1.5rem;"></i>
                            <h6 class="mb-0 fw-bold text-primary">AI Phase Generator</h6>
                        </div>
                        <p class="small text-muted mb-2">Describe what you want to create (e.g. <i>"Create New City Phase 1 with A-Block containing 60 plots of 5 Marla"</i>) and AI will do the data entry for you.</p>
                        <div class="input-group">
                            <input type="text" id="aiPrompt" class="form-control" placeholder="Type your prompt here...">
                            <button class="btn btn-primary" type="button" id="aiGenerateBtn">
                                <i class="bx bx-magic-wand me-1"></i> Generate
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped border-top" id="example">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th>Sr. No</th>
                                    <th>Phase Name</th>
                                    <th>Land (Acre)</th>
                                    <th>Land (Marla)</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($phases as $key => $phase)
                                    <tr id="phaseRow{{ $phase->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-semibold">{{ $phase->phase_name }}</td>
                                        <td>{{ $phase->land_in_acer + 0 }}</td>
                                        <td>{{ $phase->land_in_marla + 0 }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary editPhaseBtn"
                                                    data-id="{{ $phase->id }}">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger deletePhaseBtn"
                                                    data-id="{{ $phase->id }}">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noDataRow">
                                        <td colspan="6" class="text-center text-muted">No phases found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div> <!-- card -->
            </div> <!-- layout-page -->
        </div> <!-- layout-container -->
    </div> <!-- wrapper -->

    <!-- Phase Modal -->
    <div class="modal fade" id="phaseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="phaseForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" id="phaseId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Phase</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phase Name</label>
                                <input type="text" name="phase_name" id="phase_name" class="form-control" required>
                                <div class="invalid-feedback" id="error_phase_name"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Land (Acre)</label>
                                <input type="number" step="0.01" name="land_in_acer" id="land_in_acer"
                                    class="form-control">
                                <div class="invalid-feedback" id="error_land_in_acer"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Land (Marla)</label>
                                <input type="number" step="0.01" name="land_in_marla" id="land_in_marla"
                                    class="form-control">
                                <div class="invalid-feedback" id="error_land_in_marla"></div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                                <div class="invalid-feedback" id="error_description"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Phase</button>
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

                const phaseModal = new bootstrap.Modal(document.getElementById('phaseModal'));
                const phaseForm = document.getElementById('phaseForm');

                // Open Modal for Add
                $('#addPhaseBtn').click(function() {
                    $('#modalTitle').text('Add New Phase');
                    $('#saveBtn').text('Save Phase');
                    $('#formMethod').val('POST');
                    $('#phaseId').val('');
                    phaseForm.reset();
                    $('.is-invalid').removeClass('is-invalid');
                    phaseModal.show();
                });

                // AI Generate Phase
                $('#aiGenerateBtn').click(function() {
                    let prompt = $('#aiPrompt').val();
                    if (!prompt) {
                        Swal.fire('Warning', 'Please enter a description first.', 'warning');
                        return;
                    }

                    let btn = $(this);
                    let originalHtml = btn.html();

                    $.ajax({
                        url: '/ai/generate-phase',
                        type: 'POST',
                        data: { prompt: prompt },
                        beforeSend: function() {
                            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                        },
                        success: function(response) {
                            btn.prop('disabled', false).html(originalHtml);
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html(originalHtml);
                            let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong. Is Ollama running locally?';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });

                // Handle Form Submission
                $('#phaseForm').submit(function(e) {
                    e.preventDefault();
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    let id = $('#phaseId').val();
                    let url = id ? `/phases/${id}` : "/phases";
                    let formData = $(this).serialize();

                    $.ajax({
                        url: url,
                        type: "POST", // Method spoofing via _method input
                        data: formData,
                        beforeSend: function() {
                            $('#saveBtn').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                            );
                        },
                        success: function(response) {
                            phaseModal.hide();
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
                            $('#saveBtn').prop('disabled', false).text(id ? 'Update Phase' :
                                'Save Phase');
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
                $(document).on('click', '.editPhaseBtn', function() {
                    let id = $(this).data('id');
                    $('.is-invalid').removeClass('is-invalid');

                    $.get(`/phases/${id}`, function(data) {
                        $('#modalTitle').text('Edit Phase');
                        $('#saveBtn').text('Update Phase');
                        $('#formMethod').val('PUT');
                        $('#phaseId').val(data.id);
                        $('#phase_name').val(data.phase_name);
                        $('#land_in_acer').val(data.land_in_acer ? parseFloat(data.land_in_acer) : '');
                        $('#land_in_marla').val(data.land_in_marla ? parseFloat(data.land_in_marla) :
                            '');
                        $('#description').val(data.description);
                        phaseModal.show();
                    });
                });

                // Handle Delete
                $(document).on('click', '.deletePhaseBtn', function() {
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
                                url: `/phases/${id}`,
                                type: 'DELETE',
                                success: function(response) {
                                    $(`#phaseRow${id}`).remove();
                                    Swal.fire('Deleted!', response.message, 'success');
                                    if ($('#phaseTable tbody tr').length === 0) {
                                        $('#phaseTable tbody').append(
                                            '<tr id="noDataRow"><td colspan="6" class="text-center text-muted">No phases found.</td></tr>'
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
