<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-2 px-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark fs-6" href="javascript:;">ADMIN</a></li>
            <li class="breadcrumb-item text-sm text-dark active text-capitalize fs-6" aria-current="page">{{ str_replace('-', ' ', Request::path()) }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize fs-5">{{ str_replace('-', ' ', Request::path()) }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar"> 
            <ul class="navbar-nav justify-content-end">
            <li class="nav-item d-flex align-items-center">
                <span class="nav-link text-body font-weight-bold px-0">
                    <i class="fa fa-user me-sm-1 fs-5"></i>
                    <span class="d-sm-inline d-none fs-6">{{ Auth::user()->name }}</span>
                </span>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line"></i>
                    <i class="sidenav-toggler-line"></i>
                    <i class="sidenav-toggler-line"></i>
                </div>
                </a>
            </li>
            <li class="nav-item px-4 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0 icon-link" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer fs-5"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end settings-dropdown" aria-labelledby="settingsDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ url('/profile') }}">
                            <i class="fa fa-user me-2"></i> Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="{{ url('/logout')}}">
                            <i class="fa fa-sign-out me-2"></i> Sign Out
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
                <a href="{{ url('/admin/requests') }}" class="nav-link text-body p-0 icon-link">
                    <i class="fa fa-bell cursor-pointer fs-5"></i>
                </a>
            </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar-nav .nav-item {
    margin: 0 5px;
}

.dropdown-menu {
    min-width: 160px;
}

.dropdown-item {
    font-size: 1rem;
    padding: 8px 15px;
}

.nav-link i {
    font-size: 1.2rem;
}

.breadcrumb-item {
    font-size: 1rem !important;
}

.navbar .container-fluid {
    padding: 0.75rem 1.5rem;
}

.settings-dropdown {
    min-width: 200px;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    background-color: #fff;
    border: 0;
    border-radius: 0.75rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
}

.settings-dropdown .dropdown-item {
    padding: 0.7rem 1.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    color: #344767;
    transition: all 0.15s ease-in;
    display: flex;
    align-items: center;
}

.settings-dropdown .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #821131;
}

.settings-dropdown .dropdown-item.text-danger {
    color: #dc3545;
}

.settings-dropdown .dropdown-item.text-danger:hover {
    background-color: #fff5f5;
}

.settings-dropdown .dropdown-divider {
    margin: 0.5rem 0;
    border-top: 1px solid #e9ecef;
}

.settings-dropdown i {
    font-size: 1rem;
    margin-right: 0.5rem;
}

.icon-link {
    transition: all 0.3s ease;
}

.icon-link:hover i {
    color: #821131 !important;
    transform: scale(1.1);
}

.icon-link i {
    transition: all 0.3s ease;
}

.nav-link.icon-link:hover {
    opacity: 1;
}
</style>
<!-- End Navbar -->