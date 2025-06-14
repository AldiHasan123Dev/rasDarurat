<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css?'.date('his')) }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}">
    @yield('style')
    @livewireStyles
    <style>
        .bg-content{
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;  
            background-blend-mode: multiply;
        }
    </style>
    <title>APP</title>
</head>
<body>
    <main class="main" id="top">
        <div class="container-fluid px-0" data-layout="container">
            <x-navbar/>
            <div class="content" style="{{ str_contains(url('/'), 'amelia.id') ? " background: url('".asset('background.jpg')."') rgb(131 114 124 / 60%); " : '' }}">
                @if (session('success'))
                    <div class="container">
                        <div class="my-3">
                            <div class="alert alert-success alert-dismissible fade show text-white py-2" role="alert">
                                <strong>Success!</strong>  {!! session('success') !!}
                                <button type="button" class="btn-close pt-2" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('danger'))
                    <div class="container">
                        <div class="my-3">
                            <div class="alert alert-danger alert-dismissible fade show text-white py-2" role="alert">
                                <strong>Warning!</strong> {{ session('danger') }}
                                <button type="button" class="btn-close pt-2" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="container">
                    <div class="my-3">
                        @if ($errors->any())
                            <div class="alert alert-danger py-1">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li class="text-white">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                    </div>
                </div>
                @yield('content')
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="{{ asset('assets/vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('assets/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('assets/js/phoenix.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/topbar.js') }}"></script>
    @yield('script')
    @stack('scripts')
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        $(document).ready( function () {
            var table = $('#table').DataTable();
            // $('#table tbody').on( 'click', 'tr', function () {
            //     console.log( table.row( this ).data() );
            // });
        });

        $('.rupiah').keyup(function (e) {
            var val = $(this).val(rupiahFormat($(this).val()));
        });

        function rupiahFormat(angka, prefix)
        {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split    = number_string.split(','),
                sisa     = split[0].length % 3,
                rupiah     = split[0].substr(0, sisa),
                ribuan     = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

        function resetToDefaults() {
            topbar.config({
              autoRun      : true,
              barThickness : 7,
              barColors    : {
                '0'      : 'rgba(26,  188, 156, .9)',
                '.25'    : 'rgba(52,  152, 219, .9)',
                '.50'    : 'rgba(241, 196, 15,  .9)',
                '.75'    : 'rgba(230, 126, 34,  .9)',
                '1.0'    : 'rgba(211, 84,  0,   .9)'
              },
              shadowBlur   : 10,
              shadowColor  : 'rgba(0,   0,   0,   .6)'
            })
        }

        resetToDefaults()
        topbar.hide();
        $(document).ajaxStart(function() {
            window.ajax_loading = true;
            topbar.show();
        });
        $(document).ajaxStop(function() {
            window.ajax_loading = false;
            topbar.hide();
        });
        $('form').submit(function (e) {
            window.ajax_loading = true;
            topbar.show();
        });
    </script>
    @livewireScripts
</body>
</html>
