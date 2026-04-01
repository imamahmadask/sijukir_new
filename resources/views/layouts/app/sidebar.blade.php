<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand text-primary fs-2 fw-bold">
                <!-- ========   Change your logo from here   ============ -->
                {{-- <img src="../assets/images/logo-dark.svg" class="img-fluid logo-lg" alt="logo"> --}}
                SIJUKIR
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Data</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item {{ request()->routeIs('notification.index') ? 'active' : '' }}">
                    <a href="{{ route('notification.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-bell"></i></span>
                        <span class="pc-mtext">Notifikasi</span>
                    </a>
                </li>                
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-chart-line"></i></span><span class="pc-mtext">
                        Analisa</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="{{ route('analisa.jukir.index') }}">Analisa Jukir</a></li>
                        <li class="pc-item"><a class="pc-link" href="{{ route('analisa.korlap.index') }}">Analisa Bulanan</a></li>
                        <li class="pc-item"><a class="pc-link" href="#!">Analisa Tahunan</a></li>
                        <li class="pc-item"><a class="pc-link" href="#!">Analisa Potensi</a></li>
                        <li class="pc-item"><a class="pc-link" href="#!">Analisa Titik</a></li>                                                                                        
                    </ul>
                </li>

                <li class="pc-item pc-caption">
                    <label>Parkir</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item {{ request()->routeIs('lokasi.*') ? 'active' : '' }}">
                    <a href="{{ route('lokasi.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-map-pin"></i></span>
                        <span class="pc-mtext">Titik Parkir</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('jukir.*') ? 'active' : '' }}">
                    <a href="{{ route('jukir.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-user"></i></span>
                        <span class="pc-mtext">Juru Parkir</span>
                    </a>
                </li>                

                <li class="pc-item pc-caption">
                    <label>Transaksi</label>
                    <i class="ti ti-news"></i>
                </li>
                <li class="pc-item {{ request()->routeIs('transaksi.tunai.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.tunai.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-cash"></i></span>
                        <span class="pc-mtext">Tunai</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('transaksi.non-tunai.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.non-tunai.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-wallet"></i></span>
                        <span class="pc-mtext">Non Tunai</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('berlangganan.*') ? 'active' : '' }}">
                    <a href="{{ route('berlangganan.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-ticket"></i></span>
                        <span class="pc-mtext">Berlangganan</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('insidentil.*') ? 'active' : '' }}">
                    <a href="{{ route('insidentil.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-box"></i></span>
                        <span class="pc-mtext">Insidentil</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Other</label>
                    <i class="ti ti-brand-chrome"></i>
                </li>
                <li class="pc-item {{ request()->routeIs('merchant.*') ? 'active' : '' }}">
                    <a href="{{ route('merchant.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-shopping-cart"></i></span>
                        <span class="pc-mtext">Merchant</span>
                    </a>
                </li>
                @if(in_array(auth()->user()->role, ['user', 'admin']))
                <li class="pc-item {{ request()->routeIs('korlap.index') ? 'active' : '' }}">
                    <a href="{{ route('korlap.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                        <span class="pc-mtext">Korlap</span>
                    </a>
                </li>
                @endif
                <li class="pc-item">
                    <a href="#" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-urgent"></i></span>
                        <span class="pc-mtext">Pengaduan</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="#" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-notes"></i></span>
                        <span class="pc-mtext">Peringatan</span>
                    </a>
                </li>
                @can('manageAll')
                <li class="pc-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-user-plus"></i></span>
                        <span class="pc-mtext">Users</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
