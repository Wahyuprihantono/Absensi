<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="icon" href="{{asset('assets/img/icon.ico')}}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{asset('assets/js/plugin/webfont/webfont.min.js')}}"></script>
    <script>
        WebFont.load({
        			google: {"families":["Lato:300,400,700,900"]},
        			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: [`{{asset('assets/css/fonts.min.css')}}`]},
        			active: function() {
        				sessionStorage.fonts = true;
        			}
        		});
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/atlantis.css')}}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
    @livewireStyles
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body class="font-sans antialiased">
    <div class="wrapper">
        <div class="main-header">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="blue">

                <a href="{{route('dashboard')}}" class="logo">
                    <img src="{{asset('assets/img/logo.svg')}}" style="height: 30px" alt="navbar brand"
                        class="navbar-brand">
                </a>
                <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
                    data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                        <i class="icon-menu"></i>
                    </span>
                </button>
                <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="icon-menu"></i>
                    </button>
                </div>
            </div>
            <!-- End Logo Header -->

            <!-- Navbar Header -->
            <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">

                <div class="container-fluid">
                    {{-- <div class="collapse" id="search-nav">
                        <form class="navbar-left navbar-form nav-search mr-md-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-search pr-1">
                                        <i class="fa fa-search search-icon"></i>
                                    </button>
                                </div>
                                <input type="text" placeholder="Search ..." class="form-control">
                            </div>
                        </form>
                    </div> --}}
                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        {{-- <li class="nav-item toggle-nav-search hidden-caret">
                            <a class="nav-link" data-toggle="collapse" href="#search-nav" role="button"
                                aria-expanded="false" aria-controls="search-nav">
                                <i class="fa fa-search"></i>
                            </a>
                        </li> --}}

                        <li class="nav-item dropdown hidden-caret">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="nav-link" id="notifDropdown" title="Logout" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                                                                        this.closest('form').submit();">
                                    <i class="fas fa-power-off"></i>
                                </a>
                            </form>
                        </li>


                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>
        <!-- Sidebar -->
        <div class="sidebar sidebar-style-2">

            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <div class="user">
                        <div class="avatar-sm float-left mr-2">
                            <img src="../../assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle">
                        </div>
                        <div class="info">
                            <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                                <span>
                                    {{Auth::user()->karyawan ? Auth::user()->karyawan->nama_karyawan : Auth::user()->email}}
                                    <span class="user-level text-uppercase">{{Auth::user()->level}}</span>
                                </span>
                            </a>
                        </div>
                    </div>
                    <ul class="nav nav-primary">
                        <li class="nav-item {{request()->routeIs('dashboard') ? 'active' : ''}}">
                            <a href="{{route('dashboard')}}">
                                <i class="fas fa-home"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item {{request()->routeIs('data.lokasi') ? 'active' : ''}}">
                            <a href="{{route('data.lokasi')}}">
                                <i class="fas fa-map"></i>
                                <p>Data Lokasi</p>
                            </a>
                        </li>

                        <li class="nav-item {{request()->routeIs('data.karyawan') ? 'active' : ''}}">
                            <a href="{{route('data.karyawan')}}">
                                <i class="fas fa-users"></i>
                                <p>Data Karyawan</p>
                            </a>
                        </li>

                        <li class="nav-item {{request()->routeIs('data.absen') ? 'active' : ''}}">
                            <a href="{{route('data.absen')}}">
                                <i class="fas fa-list"></i>
                                <p>Data Absen</p>
                            </a>
                        </li>

                        <li class="nav-item {{request()->routeIs('log') ? 'active' : ''}}">
                            <a href="{{route('log')}}">
                                <i class="fas fa-clock"></i>
                                <p>Log</p>
                            </a>
                        </li>

                        {{-- <li class="nav-item {{request()->routeIs('crud.generator') ? 'active' : ''}}">
                        <a href="{{route('crud.generator')}}">
                            <i class="fas fa-cogs"></i>
                            <p>Crud Generator</p>
                        </a>
                        </li> --}}


                        {{-- <li class="nav-item {{request()->routeIs('attendance.report') ? 'active' : ''}}">
                        <a href="{{route('attendance.report')}}">
                            <i class="fas fa-cogs"></i>
                            <p>Log Absensi</p>
                        </a>
                        </li> --}}

                    </ul>
                </div>
            </div>
        </div>
        <div class="main-panel">
            <div class="container">
                {{$slot}}
            </div>
            <footer class="footer">
                <div class="container-fluid">

                    <div class="copyright ml-auto">
                        {{date('Y')}}, made with <i class="fa fa-heart heart text-danger"></i> by <a
                            href="http://www.themekita.com">ThemeKita</a>
                    </div>
                </div>
            </footer>
        </div>

    </div>


    <script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery UI -->
    <script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>


    <!-- jQuery Scrollbar -->
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/atlantis.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    @stack('scripts')
    <script>
        document.addEventListener('livewire:load', function(e) {
                    window.livewire.on('showAlert', (data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.msg,
                            timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: false
                        })
                    });
                    
                    window.livewire.on('showAlertError', (data) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg,
                            timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: false
                        })
                    });
                })
    </script>
    @livewireScripts
</body>

</html>