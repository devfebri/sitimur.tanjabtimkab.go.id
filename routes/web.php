<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengajuanOpenController;
use App\Http\Controllers\PersyaratanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatRevisiController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\KepalaukpbjMiddleware;
use App\Http\Middleware\PokjaPemilihanMiddleware;
use App\Http\Middleware\PpkMiddleware;
use App\Http\Middleware\VerifikatorMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('maintenance');
// });

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/proses_login', [AuthController::class, 'proses_login'])->name('proses_login');
Route::post('/proses_register', [AuthController::class, 'proses_register'])->name('proses_register');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('check-username', [UserController::class, 'checkUsername'])->name('usercheckUsername');

// Global User routes (accessible by all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/users/data', [UserController::class, 'getUserData'])->name('users.data');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::delete('/user/{id}', [UserController::class, 'delete'])->name('user.delete');
});

// Test Chat Route (temporary for testing)
Route::get('/test-chat', function () {
    return view('test-chat');
})->name('test.chat');


Route::get('/notif/baca-semua', [NotificationController::class, 'bacaSemua'])->name('notif.baca.semua');
Route::get('/notif/read/{id}', function ($id) {
    $notif = auth()->user()->notifications()->find($id);
    if (!$notif) {
        return response()->json(['error' => true, 'pesan' => 'Data sudah dihapus pembuat.']);
    }
    $notif->markAsRead();
    // Redirect ke url tujuan yang disimpan di data notifikasi
    return redirect($notif->data['url'] ?? '/');
})->name('notif.read');


// ================= ADMIN =================
Route::prefix('admin')->middleware(AdminMiddleware::class)->name('admin_')->group(function () {
    Route::get('/dashboard', [PengajuanController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/data', [PengajuanController::class, 'getData'])->name('pengajuandata');
    Route::get('/pengajuan/{id}/open', [PengajuanOpenController::class, 'open'])->name('pengajuanopen');
    Route::get('pengajuan/{id}/files', [PengajuanOpenController::class, 'getFiles'])->name('pengajuan_files');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profileupdate');
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/users/data', [UserController::class,'getUserData'])->name('userdata');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/create', [UserController::class, 'create'])->name('usercreate');
    Route::delete('/user/{id}', [UserController::class, 'delete'])->name('userdelete');

    Route::get('/persyaratan', [PersyaratanController::class, 'index'])->name('persyaratan');
    Route::post('/persyaratan/store', [PersyaratanController::class, 'store'])->name('persyaratancreate');
    Route::get('/persyaratan/{id}/edit', [PersyaratanController::class, 'edit'])->name('persyaratanedit');
    Route::delete('/persyaratan/{id}', [PersyaratanController::class, 'destroy'])->name('persyaratandelete');
    Route::get('/persyaratan/{id}/open', [PersyaratanController::class, 'open'])->name('persyaratanopen');
    Route::post('/persyaratan/berkas/store', [PersyaratanController::class, 'berkasStore'])->name('persyaratan_berkas_store');
    Route::get('/persyaratan/berkas/{id}/edit', [PersyaratanController::class, 'berkasEdit'])->name('persyaratan_berkas_edit');
    Route::delete('/persyaratan/berkas/{id}', [PersyaratanController::class, 'berkasDestroy'])->name('persyaratan_berkas_delete');
    Route::get('riwayat_revisi', [RiwayatRevisiController::class, 'index'])->name('riwayat_revisi');
    Route::get('download_revision/{id}', [RiwayatRevisiController::class, 'downloadRevision'])->name('download_revision');
});

// =============== VERIFIKATOR ===============
Route::prefix('verifikator')->middleware(VerifikatorMiddleware::class)->name('verifikator_')->group(function () {
    Route::get('/dashboard', [PengajuanController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/data', [PengajuanController::class, 'getData'])->name('pengajuandata');
    Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->name('pengajuanedit');
    Route::post('/pengajuan/create', [PengajuanController::class, 'kirim_pengajuan'])->name('kirim_pengajuan');
    Route::post('/pengajuan/{id}/update', [PengajuanController::class, 'update_pengajuan'])->name('update_pengajuan');
    Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->name('pengajuandelete');
    Route::get('/pengajuan/{id}/open', [PengajuanOpenController::class, 'open'])->name('pengajuanopen');
    Route::get('pengajuan/{id}/files', [PengajuanOpenController::class, 'getFiles'])->name('pengajuan_files');
    Route::post('pengajuan/{id}/files/approval',[PengajuanOpenController::class,'filesApproval'])->name('pengajuan_files_approval');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update',[ProfileController::class,'update'])->name('profileupdate');
    Route::get('riwayat_revisi', [RiwayatRevisiController::class, 'index'])->name('riwayat_revisi');
    Route::get('download_revision/{id}', [RiwayatRevisiController::class, 'downloadRevision'])->name('download_revision');
    Route::post('/pengajuan/{id}/tolak', [PengajuanOpenController::class, 'tolakPengajuan'])->name('pengajuan_tolak');
});

// =============== KEPALA UKPBJ ===============
Route::prefix('kepalaukpbj')->middleware(KepalaukpbjMiddleware::class)->name('kepalaukpbj_')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profileupdate');
    Route::get('/dashboard', [PengajuanController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/data', [PengajuanController::class, 'getData'])->name('pengajuandata');
    Route::get('/pengajuan/{id}/open', [PengajuanOpenController::class, 'open'])->name('pengajuanopen');
    Route::get('pengajuan/{id}/files', [PengajuanOpenController::class, 'getFiles'])->name('pengajuan_files');
    Route::post('/pengajuan/{id}/disposisi', [PengajuanOpenController::class, 'disposisiPokja'])->name('pengajuan_disposisi');
    Route::post('/pengajuan/{id}/tolak', [PengajuanOpenController::class, 'tolakPengajuan'])->name('pengajuan_tolak');

    Route::get('/getPokja', [PengajuanOpenController::class, 'getPokja'])->name('getPokja');
    Route::post('/kirim_pokja', [PengajuanOpenController::class, 'kirimPokja'])->name('kirimPokja');
    Route::get('riwayat_revisi', [RiwayatRevisiController::class, 'index'])->name('riwayat_revisi');
    Route::get('download_revision/{id}', [RiwayatRevisiController::class, 'downloadRevision'])->name('download_revision');
});

// =============== PPK ===============
Route::prefix('ppk')->middleware(['auth', PpkMiddleware::class])->name('ppk_')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profileupdate');
    Route::post('/pengajuan/simpanstep1', [PengajuanController::class, 'simpanStep1'])->name('simpan_step1');
    Route::post('/pengajuan/simpanstep2', [PengajuanController::class, 'simpanStep2'])->name('simpan_step2');

    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/data', [PengajuanController::class,'getData'])->name('pengajuandata');
    Route::get('/pengajuan/create', [PengajuanController::class,'create'])->name('pengajuan_create');
    Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->name('pengajuanedit');
    Route::post('/pengajuan/create', [PengajuanController::class, 'kirim_pengajuan'])->name('kirim_pengajuan');
    Route::post('/pengajuan/{id}/update', [PengajuanController::class, 'update_pengajuan'])->name('update_pengajuan');
    Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->name('pengajuandelete');
    Route::get('/pengajuan/{id}/open', [PengajuanOpenController::class, 'open'])->name('pengajuanopen');
    Route::get('/metode_pengadaan_berkas/{id}',[PengajuanController::class, 'metodePengadaanBerkas'])->name('metode_pengadaan_berkas');
    Route::get('pengajuan/{id}/files', [PengajuanOpenController::class, 'getFiles'])->name('pengajuan_files');
    Route::post('/pengajuan/upload-berkas', [PengajuanController::class, 'uploadBerkasAjax'])->name('upload_berkas_ajax');
    Route::post('/pengajuan/cek-upload-berkas', [PengajuanController::class, 'cekUploadBerkas'])->name('cek_upload_berkas');
    
    Route::get('pengajuan/{id}/open/edit', [PengajuanOpenController::class, 'pengajuan_open_edit'])->name('pengajuan_open_edit');
    Route::post('pengajuan/{id}/open/update', [PengajuanOpenController::class, 'pengajuan_open_update'])->name('pengajuan_open_update');

    // Chat route for PPK
    Route::get('/chats',[ChatsController::class, 'index'])->name('chats');
    
    // API for chat users
    Route::get('/api/chat-users', [ChatsController::class, 'getChatUsers'])->name('api.chat.users');
    Route::get('/api/unread-count', [ChatsController::class, 'getUnreadCount'])->name('api.unread.count');

    Route::get('riwayat_revisi',[RiwayatRevisiController::class,'index'])->name('riwayat_revisi');
    Route::get('download_revision/{id}', [RiwayatRevisiController::class, 'downloadRevision'])->name('download_revision');
});

// =============== POKJA PEMILIHAN ===============
Route::prefix('pokjapemilihan')->middleware(PokjaPemilihanMiddleware::class)->name('pokjapemilihan_')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profileupdate');
    Route::get('/dashboard', [PengajuanController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/data', [PengajuanController::class, 'getData'])->name('pengajuandata');
    Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->name('pengajuanedit');
    Route::post('/pengajuan/create', [PengajuanController::class, 'kirim_pengajuan'])->name('kirim_pengajuan');
    Route::post('/pengajuan/{id}/update', [PengajuanController::class, 'update_pengajuan'])->name('update_pengajuan');
    Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->name('pengajuandelete');

    Route::get('/pengajuan/{id}/open', [PengajuanOpenController::class, 'open'])->name('pengajuanopen');
    Route::get('/metode_pengadaan_berkas/{id}', [PengajuanController::class, 'metodePengadaanBerkas'])->name('metode_pengadaan_berkas');
    Route::get('/pengajuan/{id}/files', [PengajuanOpenController::class, 'getFiles'])->name('pengajuan_files');
    Route::post('pengajuan/{id}/files/approval', [PengajuanOpenController::class, 'filesApproval'])->name('pengajuan_files_approval');
    Route::post('/selesai-reviu/{id}', [PengajuanOpenController::class, 'selesaiReviu'])->name('selesai_reviu');
    Route::post('/pengajuan/kirim-pokja', [PengajuanOpenController::class, 'kirimPokja'])->name('kirimPokja');

    Route::post('/selesai-reviu/{id}', [PengajuanOpenController::class, 'selesaiReviu'])->name('selesai_reviu');
    
    // Chat route for Pokja Pemilihan
    Route::get('/chats', [ChatsController::class, 'index'])->name('chats');
    
    // API for chat users
    Route::get('/api/chat-users', [ChatsController::class, 'getChatUsers'])->name('api.chat.users');
    Route::get('/api/unread-count', [ChatsController::class, 'getUnreadCount'])->name('api.unread.count');
    Route::get('riwayat_revisi', [RiwayatRevisiController::class, 'index'])->name('riwayat_revisi');
    Route::get('download_revision/{id}', [RiwayatRevisiController::class, 'downloadRevision'])->name('download_revision');

});

// Test routes for debugging
Route::get('/test-chat', function () {
    return view('test-chat');
})->name('test.chat');

Route::get('/test-chat-debug', function () {
    return view('test-chat-debug');
})->name('test.chat.debug');

Route::post('/test-send-message', function (\Illuminate\Http\Request $request) {
    try {
        // Get or create test conversation
        $conversation = \App\Models\ChatConversation::firstOrCreate([
            'id' => $request->conversation_id
        ], [
            'user1_id' => \Illuminate\Support\Facades\Auth::id(),
            'user2_id' => \Illuminate\Support\Facades\Auth::id() + 1, // For testing
            'pengajuan_id' => null,
            'last_message_at' => now()
        ]);

        // Create test message
        $message = \App\Models\ChatMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'message' => $request->message,
            'type' => 'text'
        ]);

        // Fire broadcasting event
        event(new \App\Events\MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => 'Test message sent and broadcasted',
            'data' => $message->load('user')
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
})->name('test.send.message');
