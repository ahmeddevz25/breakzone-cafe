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


    });
</script>
