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

    <div id="sidebar-menu">        <ul>           @if( auth()->user()->role == 'ppk')
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
             <li>
                 <a href="{{ route('ppk_chats') }}" class="waves-effect">
                     <i class="mdi mdi-forum"></i>
                     <span> Chat </span>
                 </a>
             </li>@elseif(auth()->user()->role == 'admin')
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
                    <i class="fa fa-dashboard"></i>
                    <span> Data Pengajuan </span>
                </a>
            </li>            @elseif(auth()->user()->role == 'verifikator')
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
            <li>
                <a href="{{ route('pokjapemilihan_chats') }}" class="waves-effect">
                    <i class="mdi mdi-forum"></i>
                    <span> Chat </span>
                </a>
            </li>

           @endif
            


        </ul>
    </div>
    <div class="clearfix"></div>
</div> <!-- end sidebarinner -->
