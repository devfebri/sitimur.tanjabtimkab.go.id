<!-- LOGO -->
<div class="topbar-left">
    <div class="text-center">
        {{-- <a href="index.html" class="logo"><i class="mdi mdi-assistant"></i> Annex</a> --}}
        <a href="{{ route(auth()->user()->role.'_dashboard') }}" class="logo"><img src="{{ asset('image/logo-png.png') }}" height="80" alt="logo"></a>
        <h5></h5>
    </div>
</div>

<div class="sidebar-inner slimscrollleft" style="font-family:revert-layer;font-size:14px;">

    <div id="sidebar-menu">
        <ul>
           
            <li>
                <a href="{{ route(auth()->user()->role.'_user') }}" class="waves-effect">
                    <i class="mdi mdi-worker"></i>
                    <span> User </span>
                </a>
            </li>


        </ul>
    </div>
    <div class="clearfix"></div>
</div> <!-- end sidebarinner -->
