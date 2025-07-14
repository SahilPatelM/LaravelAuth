<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset(mix('assets/vendor/fonts/materialdesignicons.css')) }}" />
<link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/node-waves/node-waves.css')) }}" />
<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/core.css')) }}" />
<link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/theme-default.css')) }}" />
<link rel="stylesheet" href="{{ asset(mix('assets/css/demo.css')) }}" />
<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')) }}" />

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

<!-- Select2 CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
    integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .notyf__toast {
        border-radius: 30px !important;
    }

    .dataTables_processing {
        display: none !important;
    }

    div.dataTables_wrapper div.dataTables_info {
        padding-left: 1.85rem;
        padding-bottom: 1rem;
        padding-top: 1.2rem;
    }

    .dataTables_paginate {
        padding-top: .85rem;
        padding-right: 1.85rem;
        padding-bottom: 1rem;
    }

    .table {
        border-radius: 16px;
        overflow: hidden;
        /* Ensures the table content respects the border radius */
    }

    .card-body.tablecard {
        padding-bottom: 0px;
        padding-top: 0px;
    }

    .loader {
        display: block;
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: #08000077 url(https://emiclub-file-prod.s3.ap-south-1.amazonaws.com/images/icons/34338d26023e5515f6cc8969aa027bca_w200.gif) no-repeat center center;
        text-align: center;
        color: #999;
        background-size: 70px;
    }

    .swal2-high-z-index {
        z-index: 9999 !important;
    }

    .swal2-backdrop-show {
        z-index: 9998 !important;
    }
</style>

<!-- Vendor Styles -->
@yield('vendor-style')


<!-- Page Styles -->
@yield('page-style')
