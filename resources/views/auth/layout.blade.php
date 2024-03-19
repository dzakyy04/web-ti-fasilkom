<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Page Title  -->
    <title>{{ config('app.name') }} | {{ $title }}</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href=" {{ asset('/assets/css/dashlite.css') }}">
    @stack('css')
</head>

<body class="nk-body bg-white npc-general pg-auth">
    <div class="nk-app-root">
        <div class="nk-main ">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="#" class="logo-link">
                                <img class="logo-light logo-img logo-img-lg"
                                    src="{{ asset('assets/images/unsri-ti-light.png') }}" alt="logo">
                                <img class="logo-dark logo-img logo-img-lg"
                                    src="{{ asset('assets/images/unsri-ti-dark.png') }}" alt="logo-dark">
                            </a>
                        </div>
                        <div class="card card-bordered">
                            <div class="card-inner card-inner-lg">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                    <div class="nk-footer nk-auth-footer-full">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <p class="text-soft">&copy; 2022 Dashlite. All Rights Reserved.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="{{ asset('assets/js/bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/example-toastr.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#btnSubmit").on("click", function() {
                var $btn = $(this);
                var $form = $btn.closest("form");
                if ($form[0].checkValidity()) {
                    $btn.addClass("disabled");

                    var spinner = $("<span/>", {
                        "class": "spinner-border spinner-border-sm",
                        "role": "status",
                        "aria-hidden": "true"
                    });

                    $btn.prepend(spinner);
                }
            });
        });

        @if (session()->has('success'))
            let successMessage = @json(session('success'));
            NioApp.Toast(`<h5>Berhasil</h5><p>${successMessage}</p>`, 'success', {
                position: 'top-right',
            });
        @endif

        @if (session()->has('error'))
            let errorMessage = @json(session('error'));
            NioApp.Toast(`<h5>Error</h5><p>${errorMessage}</p>`, 'error', {
                position: 'top-right',
            });
        @endif
    </script>

</html>
