<div class="topbar">

    <nav class="navbar-custom">


        <ul class="list-inline float-right mb-0">
            <!-- language-->
            <span style="color: white"><b><i id="time"></i></b></span>
            

            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="ti-bell noti-icon"></i>
                    @if(auth()->user()->unreadNotifications->count())
                    <span class="badge badge-danger noti-icon-badge badgenotif">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg p-0">
                    <div class="dropdown-item noti-title d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                        <span class="mb-0 font-weight-bold">Notifikasi</span>
                        <a href="{{ route('notif.baca.semua') }}" class="text-primary small">Tandai semua dibaca</a>
                    </div>
                    <div class="px-2" style="max-height: 350px; overflow-y: auto;">
                        @forelse(auth()->user()->unreadNotifications->take(5) as $notif)
                        <a class="dropdown-item notify-item d-flex align-items-start py-2" href="{{ route('notif.read', $notif->id) }}">
                            <div class="flex-grow-1 pl-2">
                                <div class="font-weight-bold text-dark">{{ $notif->data['judul'] }}</div>
                                <div class="small text-muted">{{ $notif->data['pesan'] }}</div>
                                <div class="small text-secondary">
                                    <i class="mdi mdi-clock-outline"></i>
                                    {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                        @empty
                        <span class="dropdown-item text-muted text-center py-3">Tidak ada notifikasi</span>
                        @endforelse
                    </div>
                    {{-- <a href="cuti/viewall" class="dropdown-item notify-item text-center">
                            View All
                        </a> --}}
                </div>
            </li>


            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ auth()->user()->getAvatar() }}" alt="user" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5>{{ auth()->user()->name }}</h5>
                    </div>

                    <a class="dropdown-item" href="{{ route(auth()->user()->role.'_profile') }}"><i class="mdi mdi-account-circle m-r-5 text-muted"></i>
                        Profile</a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{route('logout')}}"><i class="mdi mdi-logout m-r-5 text-muted"></i>
                        Logout</a>
                </div>
            </li>

        </ul>
        <ul class="list-inline menu-left mb-0">
            <li class="float-left">
                <button class="button-menu-mobile open-left waves-light waves-effect">
                    <i class="mdi mdi-menu"></i>
                </button>
            </li>
            {{-- <li class="hide-phone app-search">
                <form role="search" class="">
                    <input type="text" placeholder="Search..." class="form-control">
                    <a href=""><i class="fa fa-search"></i></a>
                </form>
            </li> --}}
        </ul>



        <div class="clearfix">
        </div>

    </nav>

</div>

