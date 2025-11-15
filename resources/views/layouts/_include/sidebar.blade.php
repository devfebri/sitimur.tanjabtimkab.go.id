<!-- LOGO -->
<div class="topbar-left">
    <div class="text-center">
        {{-- <a href="index.html" class="logo"><i class="mdi mdi-assistant"></i> Annex</a> --}}
        <a href="{{ route(auth()->user()->role.'_dashboard') }}" class="logo">
            <img src="{{ asset('img/logo.webp') }}" width="200" height="40" alt="logo">
        </a>
        <h5></h5>
    </div>
</div>

<div class="sidebar-inner slimscrollleft" style="font-family:revert-layer;font-size:14px;">

    <div id="sidebar-menu">
        <ul>
            @if( auth()->user()->role == 'ppk')
                <li class="menu-title">PPK Menu</li>
                <li>
                    <a href="{{ route('ppk_pengajuan_create') }}" class="waves-effect">
                        <i class="mdi mdi-plus"></i>
                        <span> Buat Pengajuan </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ppk_dashboard') }}" class="waves-effect">
                        <i class="mdi mdi-file"></i>
                        <span> Data Pengajuan </span>
                    </a>
                </li>

            @elseif(auth()->user()->role == 'admin')
                <li class="menu-title">Admin Menu</li>
                <li>
                    <a href="{{ route(auth()->user()->role.'_user') }}" class="waves-effect">
                        <i class="fa fa-users"></i>
                        <span> Kelola User </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route(auth()->user()->role.'_persyaratan') }}" class="waves-effect">
                        <i class="mdi mdi-clipboard"></i>
                        <span> Kelola Persyaratan </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route(auth()->user()->role.'_dashboard') }}" class="waves-effect">
                        <i class="fa fa-user"></i>
                        <span> Data Pengajuan </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route(auth()->user()->role.'_laporan') }}" class="waves-effect">
                        <i class="mdi mdi-chart-line"></i>
                        <span> Laporan </span>
                    </a>
                </li>
            @elseif(auth()->user()->role == 'verifikator')
            <li class="menu-title">Verifikator Menu</li>
            <li>
                <a href="{{ route('verifikator_dashboard') }}" class="waves-effect">
                    <i class="mdi mdi-check"></i>
                    <span> Dashboard </span>
                </a>
            </li>


            @elseif(auth()->user()->role == 'kepalaukpbj')
            <li class="menu-title">Kepala UKPBJ Menu</li>
            <li>
                <a href="{{ route('kepalaukpbj_dashboard') }}" class="waves-effect">
                    <i class="fa fa-user"></i>
                    <span> Dashboard </span>
                </a>
            </li>

            @elseif(auth()->user()->role == 'pokjapemilihan')
            <li class="menu-title">Pokja Pemilihan Menu</li>
            <li>
                <a href="{{ route('pokjapemilihan_dashboard') }}" class="waves-effect">
                    <i class="mdi mdi-gavel"></i>
                    <span> Dashboard </span>
                </a>
            </li>
            {{-- <li>
                <a href="{{ route('pokjapemilihan_chats') }}" class="waves-effect">
                    <i class="mdi mdi-forum"></i>
                    <span> Chat </span>
                    @if(isset($unreadChatCount) && $unreadChatCount > 0)
                        <span class="badge badge-pill badge-danger float-right chat-badge">{{ $unreadChatCount }}</span>
                    @else
                        <span class="badge badge-pill badge-danger float-right chat-badge" style="display: none;"></span>
                    @endif
                </a>
            </li> --}}

           @endif



        </ul>
    </div>
    <div class="clearfix"></div>

    <!-- Custom CSS for Chat Badge -->
    <style>
    .badge-danger {
        background-color: #dc3545 !important;
        color: white !important;
        font-size: 0.6rem !important;
        padding: 2px 6px !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
        min-width: 18px !important;
        text-align: center !important;
        line-height: 1.2 !important;
        margin-left: 8px !important;
        animation: pulse-badge 2s infinite;
    }

    @keyframes pulse-badge {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    /* Sidebar link with badge styling */
    #sidebar-menu ul li a {
        position: relative;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    #sidebar-menu ul li a span:first-of-type {
        flex: 1;
    }

    /* Responsive badge */
    @media (max-width: 768px) {
        .badge-danger {
            font-size: 0.55rem !important;
            padding: 1px 4px !important;
            min-width: 16px !important;
        }
    }
    </style>
</div> <!-- end sidebarinner -->
