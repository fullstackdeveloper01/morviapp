<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        @yield('title')
        <title>{{ config('app.name', 'NDN') }}</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <!-- Icons -->
        <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
        <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
        <!-- Argon CSS -->
        <link type="text/css" href="{{ asset('argon') }}/css/argon.css?v=1.0.0" rel="stylesheet">
        <!-- Argon CSS -->
        <link type="text/css" href="{{ asset('custom') }}/css/custom.css" rel="stylesheet">
        <!-- Select2 -->
        <link type="text/css" href="{{ asset('custom') }}/css/select2.min.css" rel="stylesheet">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="{{ asset('vendor') }}/jasny/css/jasny-bootstrap.min.css">
        
        <!-- Datatable -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.0/css/buttons.bootstrap4.min.css">
        <!--//-->

        <!-- Flatpickr datepicker -->
        <link rel="stylesheet" href="{{ asset('vendor') }}/flatpickr/flatpickr.min.css">

         <!-- Font Awesome Icons -->
        <link href="{{ asset('argonfront') }}/css/font-awesome.css" rel="stylesheet" />

        <!-- Range datepicker -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        @yield('head')
        @laravelPWA

        <!-- Custom CSS defined by admin -->
        <link type="text/css" href="{{ asset('byadmin') }}/back.css" rel="stylesheet">
        <style>
            #DataTables_Table_0_wrapper{
                padding: 1.5rem !important;
            }

            .text-area{
                /*max-width: 400px;*/
                height: 100px;
                overflow-x: hidden;
                overflow-y: scroll;
                white-space: normal;
            }
        </style>

    </head>
    <body class="{{ $class ?? '' }}">
        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @if(\Request::route()->getName() != "order.success")
                @include('layouts.navbars.sidebar')
            @endif
        @endauth

        <div class="main-content">
            @include('layouts.navbars.navbar')
            @yield('content')
        </div>

        @guest()
            @include('layouts.footers.guest')
        @endguest

        <!-- Commented because navtabs includes same script -->
        <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>

        <script src="{{ asset('argonfront') }}/js/core/popper.min.js" type="text/javascript"></script>
        <script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

        @yield('topjs')

        <!-- Navtabs -->
        <script src="{{ asset('argonfront') }}/js/core/jquery.min.js" type="text/javascript"></script>

        <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <!-- Nouslider -->
        <script src="{{ asset('argon') }}/vendor/nouislider/distribute/nouislider.min.js" type="text/javascript"></script>

        <!-- Argon JS -->
        <script src="{{ asset('argon') }}/js/argon.js?v=1.0.0"></script>

        <!-- Latest compiled and minified JavaScript -->
        <script src="{{ asset('vendor') }}/jasny/js/jasny-bootstrap.min.js"></script>
        <!-- Custom js -->
        <script src="{{ asset('custom') }}/js/orders.js"></script>
         <!-- Custom js -->
        <script src="{{ asset('custom') }}/js/mresto.js"></script>
        <!-- AJAX -->

        <!-- SELECT2 -->
        <script src="{{ asset('custom') }}/js/select2.js"></script>
        <script src="{{ asset('vendor') }}/select2/select2.min.js"></script>

        <!-- DATE RANGE PICKER -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <!-- All in one -->
        <script src="{{ asset('custom') }}/js/js.js?id={{ config('config.version')}}"></script>

        <!-- Import Vue -->
        <script src="{{ asset('vendor') }}/vue/vue.js"></script>

        <!-- Import AXIOS --->
        <script src="{{ asset('vendor') }}/axios/axios.min.js"></script>

        <!-- Flatpickr datepicker -->
        <script src="{{ asset('vendor') }}/flatpickr/flatpickr.js"></script>

        <!-- Notify JS -->
        <script src="{{ asset('custom') }}/js/notify.min.js"></script>

        <script>
            var ONESIGNAL_APP_ID = "{{ config('settings.onesignal_app_id') }}";
            var USER_ID = '{{  auth()->user()&&auth()->user()?auth()->user()->id:"" }}';
            var PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
            var PUSHER_APP_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
        </script>

        <script src="{{ asset('ckeditor') }}/ckeditor.js"></script>
        <script>
            "use strict";
            CKEDITOR.replace('ndneditor', {
                removePlugins: 'sourcearea',
                filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
                filebrowserUploadMethod: 'form',
                //allowedContent: 'p h1{text-align}; a[!href]; strong em; p(tip)'
            });

            CKEDITOR.replace('ndneditor2', {
                removePlugins: 'sourcearea',
                filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
                filebrowserUploadMethod: 'form',
                //allowedContent: 'p h1{text-align}; a[!href]; strong em; p(tip)'
            });            
        </script>

        <script>
            $(function() {
              $('.ndndatepicker').daterangepicker({
                singleDatePicker: true,
                minDate: 0,
                dateFormat: 'dd-mm-yy'
              });
            });

            function getSubCategory(eid)
            {
                if(eid)
                {
                    $('.sub_category_id').html('<p>Loading...</p>');
                    $.ajax({

                        url: '/getSubCategory/' + eid,

                        type: 'get',

                        dataType: 'html',

                        success: function(response) {

                            if (response) {   

                                $('.sub_category_id').html(response);

                            } else {
                                $('.sub_category_id').html('<option value="">Select Sub Category</option>');
                            }

                        }

                    });
                }
                else{
                    $('.sub_category_id').html('<option value="">Select Sub Category</option>');
                }
            }

            function sendEmailToUser(uemail){
                $('#emailsend_to').val(uemail);
            }
        </script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

        <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

        <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>

        <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>

        <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
        <script>
            $('.ndntable').DataTable({
                "order": [[0, 'desc']],
            });
        </script>

        <!-- OneSignal -->
        @if(strlen( config('settings.onesignal_app_id'))>4)
            <script src="{{ asset('vendor') }}/OneSignalSDK/OneSignalSDK.js" async=""></script>
            <script src="{{ asset('custom') }}/js/onesignal.js"></script>
        @endif

        @stack('js')
        @yield('js')

        <script src="{{ asset('custom') }}/js/rmap.js"></script>

         <!-- Pusher -->
         @if(strlen( config('broadcasting.connections.pusher.app_id'))>2)
            <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
            <script src="{{ asset('custom') }}/js/pusher.js"></script>
        @endif

        <!-- Custom JS defined by admin -->
        <?php echo file_get_contents(base_path('public/byadmin/back.js')) ?>
        <script src="{{ asset('js/custom.js') }}"></script>
    </body>
</html>
