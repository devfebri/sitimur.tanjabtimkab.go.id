<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Registrasi Pengajuan Tender</title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="Mannatthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('image/logo-png.png') }}">

    <link href="{{ asset('template/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('template/assets/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('template/assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('template/assets/plugins/alertify/css/alertify.css') }}" rel="stylesheet" type="text/css">
</head>
<body class="fixed-left">
    <div class="accountbg" id="particles-js"></div>
    <div class="wrapper-page">
        <div class="card" style="font-family:revert-layer;">
            <div class="card-body">
                <h3 class="text-center mt-0 m-b-15">
                    <a href="#" class="logo logo-admin"><img src="{{ asset('image/logo1.png') }}" class="rounded-circle" height="100" alt="logo"></a>
                </h3>
                <h3 style="font-family:Cursive;font-size:14pt;color:#fff;margin-bottom:-25px;" class="text-center">Registrasi Pengajuan Tender</h3>
                <div class="p-3">
                    <form class="form-horizontal m-t-20" action="{{ route('proses_register') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group ">
                                    <label for="name">Nama Lengkap</label>
                                    <input class="form-control " name="name" id="name" type="text" required value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label for="email">Email</label>
                                    <input class="form-control " name="email" id="email" type="email" required value="{{ old('email') }}">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="username">Username</label>
                                    <input class="form-control " name="username" id="username" type="text" required value="{{ old('username') }}">
                                    @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input class="form-control " name="password" id="password" type="password" required>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Password Confirmation</label>
                                    <input class="form-control " name="password_confirmation" id="password_confirmation" type="password" required>
                                    {{-- No error needed for confirmation --}}
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group ">
                                    <label for="nip">NIP</label>
                                    <input class="form-control " name="nip" id="nip" type="number" required value="{{ old('nip') }}">
                                    @error('nip')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label for="nik">NIK</label>
                                    <input class="form-control " name="nik" id="nik" type="number" required value="{{ old('nik') }}">
                                    @error('nik')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="jabatan">Jabatan</label>
                                    <input class="form-control " name="jabatan" id="jabatan" type="text" required value="{{ old('jabatan') }}">
                                    @error('jabatan')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="jabatan">Pangkat</label>
                                    <input class="form-control " name="pangkat" id="pangkat" type="text" required value="{{ old('pangkat') }}">
                                    @error('pangkat')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="nohp">NO HP</label>
                                    <input class="form-control " name="nohp" id="nohp" type="number" required value="{{ old('nohp') }}">
                                    @error('nohp')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="jk">Jenis Kelamin</label>
                                    <select name="jk" id="jk" class="form-control" required>
                                        <option value="">-Pilih Jenis Kelamin-</option>
                                        <option value="Laki-Laki" {{ old('jk') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                        <option value="Perempuan" {{ old('jk') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jk')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-center row m-t-20">
                            <div class="col-12">
                                <button class="btn btn-primary btn-block waves-effect waves-light" type="submit">Registrasi</button>
                                <a href="{{ route('login') }}" class="btn btn-secondary btn-block waves-effect waves-light">Kembali ke Login</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('template/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/assets/plugins/alertify/js/alertify.js') }}"></script>
    <script>
        @if(Session::has('berhasil'))
            alertify.success("{{ Session::get('berhasil') }}");
        @elseif(Session::has('gagal'))
            alertify.error("{{ Session::get('gagal') }}");
        @endif
    </script>
</body>
</html>