<div class="nk-sidebar nk-sidebar-fixed is-dark " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
                    class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex"
                data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
        </div>
        <div class="nk-sidebar-brand">
            <a href="#" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{ asset('assets/images/unsri-ti-light.png') }}" alt="logo">
                <img class="logo-dark logo-img" src="{{ asset('assets/images/unsri-ti-dark.png') }}" alt="logo-dark">
            </a>
        </div>
    </div>
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Dashboard</h6>
                    </li><!-- .nk-menu-heading -->
                    <li class="nk-menu-item has-sub">
                        <a href="{{ route('dashboard') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Artikel</h6>
                    </li><!-- .nk-menu-heading -->
                    <li class="nk-menu-item has-sub">
                        <a href="{{ route('news') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-article"></em></span>
                            <span class="nk-menu-text">Berita</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{ route('announcements') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-bell"></em></span>
                            <span class="nk-menu-text">Pengumuman</span>
                        </a>
                    </li>
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Akademik</h6>
                    </li><!-- .nk-menu-heading -->
                    <li class="nk-menu-item has-sub">
                        <a href="{{ route('lecturers') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                            <span class="nk-menu-text">Dosen</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{ route('facilities') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-building"></em></span>
                            <span class="nk-menu-text">Sarana dan Prasarana</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-award"></em></span>
                            <span class="nk-menu-text">Lulusan</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="{{ route('graduates.main-competencies') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-bullet"></em></span>
                                    <span class="nk-menu-text">Kompetensi Utama</span>
                                </a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{ route('graduates.support-competencies') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-bullet"></em></span>
                                    <span class="nk-menu-text">Kompetensi Pendukung</span>
                                </a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{ route('graduates.graduate-competencies') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-bullet"></em></span>
                                    <span class="nk-menu-text">Kompetensi Lulusan</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-network"></em></span>
                            <span class="nk-menu-text">Struktur Organisasi</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="{{ route('leaders') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-star"></em></span>
                                    <span class="nk-menu-text">Pimpinan</span>
                                </a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{ route('admins') }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-account-setting"></em></span>
                                    <span class="nk-menu-text">Admin</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
