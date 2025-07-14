<!-- BEGIN: Vendor JS-->
<script src="{{ asset(mix('assets/vendor/libs/jquery/jquery.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/popper/popper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/bootstrap.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/node-waves/node-waves.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>

<!-- jQuery (must be first) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>


<!-- DataTables JS (after jQuery) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<!-- Include jQuery Validation plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- select 2 CDN-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
    integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>

<script>
    $(document).ready(function() {
        $('#loader').fadeOut(250);
    });
    $(document).ajaxSend(function() {
        $("#loader").fadeIn(250);
    });
    $(document).ajaxComplete(function() {
        $("#loader").fadeOut(250);
    });

    const notyf = new Notyf({
        duration: 3000,
        position: {
            x: 'center',
            y: 'top'
        },
        types: [{
                type: 'success',
                background: '#2e7d32',
                icon: {
                    className: 'mdi mdi-check',
                    tagName: 'i',
                    color: 'white'
                }
            },
            {
                type: 'error',
                background: '#c62828',
                icon: {
                    className: 'mdi mdi-close',
                    tagName: 'i',
                    color: 'white'
                }
            }
        ]
    });

    // Usage
    @if (Session::has('success'))
        notyf.success('{{ session('success') }}');
    @endif

    @if (Session::has('error'))
        notyf.error('{{ session('error') }}');
    @endif
</script>
<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->
