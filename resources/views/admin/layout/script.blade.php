<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('admin') }}/assets/vendor/libs/jquery/jquery.js"></script>
<script src="{{ asset('admin') }}/assets/vendor/libs/popper/popper.js"></script>
<script src="{{ asset('admin') }}/assets/vendor/js/bootstrap.js"></script>
<script src="{{ asset('admin') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

<script src="{{ asset('admin') }}/assets/vendor/js/menu.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('admin') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<!-- Add in your layout head section if not already included -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Main JS -->
<script src="{{ asset('admin') }}/assets/js/main.js"></script>

<!-- Page JS -->
<script src="{{ asset('admin') }}/assets/js/dashboards-analytics.js"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script>
    function confirmation(ev) {
        ev.preventDefault();
        var urlToRedirect = ev.currentTarget.getAttribute('href');
        console.log(urlToRedirect);
        // Fallback or legacy swal check
        if (typeof swal === 'function') {
            swal({
                    title: "Are you sure to cancel this product",
                    text: "You will not be able to revert this!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willCancel) => {
                    if (willCancel) {



                        window.location.href = urlToRedirect;

                    }


                });


        }
    }
</script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<!-- JS (before </body>) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Toastr Flash Messages -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error(@json($error));
            @endforeach
        @endif

        @if (session('error'))
            toastr.error(@json(session('error')));
        @endif

        @if (session('success'))
            toastr.success(@json(session('success')));
        @endif
    });
</script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            ordering: false
        });

        // Global AJAX Delete Handler
        $(document).on('click', '.ajax-delete-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var url = btn.data('url');
            var row = btn.closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        headers: {
                            'Accept': 'application/json'
                        },
                        beforeSend: function() {
                            btn.prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                // Remove row with fade out
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message ||
                                    'Failed to delete record.',
                                    'error'
                                );
                                btn.prop('disabled', false);
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                            btn.prop('disabled', false);
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });

        // Global AJAX Form Submission Handler with Button Loading Spinner
        $(document).on('submit', '.modal form, form.ajax-form', function(e) {
            var form = $(this);

            // Skip if explicitly opted out
            if (form.data('no-ajax') === true || form.hasClass('no-ajax')) {
                return true;
            }

            e.preventDefault();

            var submitBtn = form.find('button[type="submit"], input[type="submit"]');
            var originalBtnHtml = submitBtn.is('button') ? submitBtn.html() : submitBtn.val();

            // Set loading state on submit button
            submitBtn.prop('disabled', true);
            if (submitBtn.is('button')) {
                submitBtn.data('original-html', originalBtnHtml);
                submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Please wait...');
            } else {
                submitBtn.val('Please wait...');
            }

            // Remove existing validation highlights
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();

            var formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method') || 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status || response.success) {
                        toastr.success(response.message || 'Operation completed successfully.');

                        // Close modal if inside one
                        var modalEl = form.closest('.modal');
                        if (modalEl.length) {
                            var bsModal = bootstrap.Modal.getInstance(modalEl[0]);
                            if (bsModal) {
                                bsModal.hide();
                            } else {
                                modalEl.modal('hide');
                            }
                        }

                        // Smoothly refresh after brief feedback delay
                        setTimeout(function() {
                            window.location.reload();
                        }, 500);
                    } else {
                        restoreSubmitBtn(submitBtn, originalBtnHtml);
                        toastr.error(response.message || 'An error occurred.');
                    }
                },
                error: function(xhr) {
                    restoreSubmitBtn(submitBtn, originalBtnHtml);

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            var input = form.find('[name="' + field + '"]');
                            if (!input.length) {
                                input = form.find('[name="' + field + '[]"]');
                            }
                            if (input.length) {
                                input.addClass('is-invalid');
                            }
                            $.each(messages, function(i, msg) {
                                toastr.error(msg);
                            });
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                }
            });
        });

        function restoreSubmitBtn(btn, originalHtml) {
            btn.prop('disabled', false);
            if (btn.is('button')) {
                btn.html(originalHtml);
            } else {
                btn.val(originalHtml);
            }
        }


    });
</script>
