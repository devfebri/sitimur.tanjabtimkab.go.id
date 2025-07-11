@extends('layouts.master')
@section('content')
<div class="page-content-wrapper ">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h4 class="font-weight-bold">Edit Profil Pengguna</h4>
            </div>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route(auth()->user()->role.'_profileupdate') }}" method="POST" enctype="multipart/form-data">
            @method('POST')
            @csrf
            <div class="row">
                <!-- Upload Foto -->
                <div class="col-md-4 text-center">
                    <div class="card">
                        <div class="card-body">
                            <img id="avatar-preview" src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('storage/avatars/default.png') }}" alt="Avatar" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="form-group">
                                <span class="badge badge-primary  ">{{ $user->username }}</span>
                                
                                <input type="file" name="avatar" id="avatar" class="form-control-file" accept="image/*">
                                <small class="text-muted">Maksimal ukuran: 2MB</small>
                                @error('avatar')
                                <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Data -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name">Nama Lengkap</label>
                                    <input class="form-control" name="name" id="name" type="text" required value="{{ $user->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input class="form-control" name="email" id="email" type="email" required value="{{ $user->email }}">
                                </div>
                               
                                <div class="form-group col-md-6">
                                    <label for="nip">NIP</label>
                                    <input class="form-control" name="nip" id="nip" type="number" required value="{{ $user->nip }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="nik">NIK</label>
                                    <input class="form-control" name="nik" id="nik" type="number" required value="{{ $user->nik }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="jabatan">Jabatan</label>
                                    <input class="form-control" name="jabatan" id="jabatan" type="text" required value="{{ $user->jabatan }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="pangkat">Pangkat</label>
                                    <input class="form-control" name="pangkat" id="pangkat" type="text" required value="{{ $user->pangkat }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="nohp">Nomor HP</label>
                                    <input class="form-control" name="nohp" id="nohp" type="tel" pattern="^\+?[0-9]{9,15}$" maxlength="15" placeholder="Contoh: +628123456789" required value="{{ $user->nohp }}">
                                    <small class="text-muted">Gunakan format angka saja, contoh: +62812xxxxx</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="jk">Jenis Kelamin</label>
                                    <select name="jk" id="jk" class="form-control" required>
                                        <option value="">-Pilih Jenis Kelamin-</option>
                                        <option value="Laki-Laki" {{ $user->jk == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                        <option value="Perempuan" {{ $user->jk == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="password">Password Baru</label>
                                    <input class="form-control" name="password" id="password" type="password" placeholder="Kosongkan jika tidak diubah">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="password_confirmation">Konfirmasi Password</label>
                                    <input class="form-control" name="password_confirmation" id="password_confirmation" type="password" placeholder="Ulangi password">
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block" type="submit">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('javascript')
<script>
    document.getElementById('avatar').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            document.getElementById('avatar-preview').src = URL.createObjectURL(file);
        }
    });
    document.getElementById('nohp').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9+]/g, ''); // hanya angka dan +
    });
    if(Session::has('pesan')){

        alertify.success("{{ Session::get('pesan') }}");
    }


</script>
@endsection



