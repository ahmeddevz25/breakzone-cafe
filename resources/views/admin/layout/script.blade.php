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
        var target = ev.currentTarget;
        var urlToRedirect = target.getAttribute('href') || target.dataset.url;
        var title = target.dataset.title || 'Are you sure?';
        var text = target.dataset.text || "You won't be able to revert this!";

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                window.location.href = urlToRedirect;
            }
        });
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
        if ($('#example').length && !$.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable({
                ordering: false
            });
        }

        // Safeguard: Remove any inline onclick="...confirm..." on delete elements so SweetAlert2 handles them cleanly
        $('.action-btn-delete, .btn-delete, [data-confirm-delete]').removeAttr('onclick');

        // Global SweetAlert2 Confirmation for all delete buttons/links across all modules
        $(document).on('click', '.action-btn-delete, .btn-delete, .confirm-delete, [data-confirm-delete]', function(e) {
            // Let dedicated AJAX delete handlers manage their own requests
            if ($(this).hasClass('ajax-delete-btn') || $(this).hasClass('delete-store-ajax-btn') || $(this).hasClass('deletePlotBtn') || $(this).hasClass('deleteBlockBtn') || $(this).hasClass('deletePhaseBtn')) {
                return;
            }

            e.preventDefault();
            var btn = $(this);
            var url = btn.attr('href') || btn.data('url');
            var title = btn.data('title') || 'Are you sure?';
            var text = btn.data('text') || "You won't be able to revert this!";
            var confirmText = btn.data('confirm-text') || 'Yes, delete it!';
            var cancelText = btn.data('cancel-text') || 'Cancel';

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    if (url && url !== '#' && !url.startsWith('javascript:')) {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        window.location.href = url;
                    } else if (btn.closest('form').length) {
                        btn.closest('form').submit();
                    }
                }
            });
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

                        // If linked to an AJAX DataTable, reload table without refreshing entire page!
                        var tableSelector = form.data('ajax-table');
                        if (tableSelector && $(tableSelector).length && $.fn.DataTable.isDataTable(tableSelector)) {
                            $(tableSelector).DataTable().ajax.reload(null, false);
                            restoreSubmitBtn(submitBtn, originalBtnHtml);
                            form[0].reset();
                        } else {
                            // Smoothly refresh after brief feedback delay
                            setTimeout(function() {
                                window.location.reload();
                            }, 500);
                        }
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

<!-- BreakZone SaaS Sidebar Controller -->
<script>
    (function() {
        function initBreakZoneSidebar() {
            var toggleBtn = document.getElementById('saas-sidebar-toggle');
            var toggleIcon = document.getElementById('saas-sidebar-toggle-icon');
            var mobileToggleBtn = document.getElementById('saas-mobile-toggle');
            var html = document.documentElement;
            var body = document.body;

            function syncIconState() {
                if (!toggleIcon) return;
                var isCollapsed = html.classList.contains('layout-menu-collapsed');
                if (window.innerWidth >= 1200) {
                    if (isCollapsed) {
                        toggleIcon.className = 'bx bx-chevron-right bx-sm align-middle';
                        if (toggleBtn) toggleBtn.setAttribute('title', 'Expand Sidebar');
                    } else {
                        toggleIcon.className = 'bx bx-chevron-left bx-sm align-middle';
                        if (toggleBtn) toggleBtn.setAttribute('title', 'Collapse Sidebar');
                    }
                } else {
                    toggleIcon.className = 'bx bx-chevron-left bx-sm align-middle';
                    if (toggleBtn) toggleBtn.setAttribute('title', 'Close Menu');
                }
            }

            function toggleSidebarState() {
                if (window.innerWidth >= 1200) {
                    // Desktop: Toggle collapsed state
                    var willCollapse = !html.classList.contains('layout-menu-collapsed');
                    if (willCollapse) {
                        html.classList.add('layout-menu-collapsed');
                        body.classList.add('layout-menu-collapsed');
                        try { localStorage.setItem('breakzone_sidebar_collapsed', 'true'); } catch(err) {}
                    } else {
                        html.classList.remove('layout-menu-collapsed');
                        body.classList.remove('layout-menu-collapsed');
                        try { localStorage.setItem('breakzone_sidebar_collapsed', 'false'); } catch(err) {}
                    }

                    // Reset menu body scroll position to top
                    var menuInner = document.querySelector('.saas-menu-inner');
                    if (menuInner) {
                        menuInner.scrollTop = 0;
                    }

                    syncIconState();

                    // Re-layout charts and data tables after smooth transition
                    setTimeout(function() {
                        window.dispatchEvent(new Event('resize'));
                    }, 280);
                } else {
                    // Mobile: Toggle drawer
                    html.classList.toggle('layout-menu-expanded');
                    body.classList.toggle('layout-menu-expanded');
                }
            }

            // Sync icon on startup
            syncIconState();

            // Toggle Button Click Handler (Arrow button)
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (window.innerWidth >= 1200) {
                        toggleSidebarState();
                    } else {
                        // Mobile: Close drawer
                        html.classList.remove('layout-menu-expanded');
                        body.classList.remove('layout-menu-expanded');
                    }
                });
            }

            // Hamburger in Header (Works on both desktop and mobile!)
            if (mobileToggleBtn) {
                mobileToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSidebarState();
                });
            }

            // Overlay Click to close mobile drawer
            $(document).on('click', '.layout-overlay', function() {
                html.classList.remove('layout-menu-expanded');
                body.classList.remove('layout-menu-expanded');
            });

            // Window resize watcher
            var resizeTimer = null;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    syncIconState();
                }, 150);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBreakZoneSidebar);
        } else {
            initBreakZoneSidebar();
        }
    })();
</script>

