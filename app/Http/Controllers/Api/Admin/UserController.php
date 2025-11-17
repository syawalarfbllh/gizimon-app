<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Menampilkan semua user
     */
    public function index()
    {
        // Tampilkan semua user beserta relasi role dan profil (jika ada)
        $users = User::with(['role', 'penyedia', 'sekolah'])->latest()->get();
        return response()->json($users, 200);
    }

    /**
     * Menyimpan user baru (dibuat oleh Admin, misal: Supervisor)
     */
    public function store(Request $request)
    {
        // 1. Validasi data User
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::find($request->role_id);
        if (!$role) {
            return response()->json(['errors' => ['role_id' => 'Role tidak valid.']], 422);
        }
        $namaRole = $role->nama_role;

        // --- 2. VALIDASI DATA PROFIL (JIKA ADA) ---
        $profileValidator = null;
        if ($namaRole === 'sekolah') {
            
            // --- INI ADALAH PERBAIKANNYA ---
            $profileValidator = Validator::make($request->all(), [
                'npsn' => 'required|string|max:10|unique:sekolahs',
                'nama_sekolah' => 'required|string|max:255',
                'alamat' => 'required|string',
                // Sesuaikan aturan 'in:' agar 100% cocok dengan ENUM baru Anda
                'jenis_sekolah' => 'required|string|in:SD,MI,SMP,MTS,SMA,SMK,MA,SLB',
                'kelurahan_id' => 'required|exists:kelurahans,id',
            ]);
            // --- AKHIR PERBAIKAN ---

        } elseif ($namaRole === 'penyedia') {
            $profileValidator = Validator::make($request->all(), [
                'nama_perusahaan' => 'required|string|max:255|unique:penyedias',
                'alamat' => 'required|string',
                'no_telp' => 'required|string|max:15',
            ]);
        }

        if ($profileValidator && $profileValidator->fails()) {
            return response()->json(['errors' => $validator->errors()->merge($profileValidator->errors())], 422);
        }
        
        // --- 3. GUNAKAN TRANSAKSI DATABASE (PENTING) ---
        try {
            DB::beginTransaction();

            // Buat User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            // Buat Profil
            if ($namaRole === 'sekolah') {
                $user->sekolah()->create([
                    'npsn' => $request->npsn,
                    'nama_sekolah' => $request->nama_sekolah,
                    'alamat' => $request->alamat,
                    'jenis_sekolah' => $request->jenis_sekolah,
                    'kelurahan_id' => $request->kelurahan_id,
                ]);
            } elseif ($namaRole === 'penyedia') {
                $user->penyedia()->create([
                    'nama_perusahaan' => $request->nama_perusahaan,
                    'alamat' => $request->alamat,
                    'no_telp' => $request->no_telp,
                ]);
            }
            
            DB::commit();

            $user->load(['role', 'penyedia', 'sekolah']);
            return response()->json(['message' => 'User dan Profil berhasil dibuat', 'user' => $user], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat user.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan satu user spesifik
     */
    public function show(User $user)
    {
        // $user sudah otomatis diambil via Route Model Binding
        $user->load(['role', 'penyedia', 'sekolah']);
        return response()->json($user, 200);
    }

    /**
     * Update data user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Abaikan email user ini sendiri
            ],
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed', // Password opsional
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update data dasar
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        // Jika ada password baru, hash dan update
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        $user->load('role'); // Muat relasi role untuk respon

        return response()->json(['message' => 'User berhasil diupdate', 'user' => $user], 200);
    }

    /**
     * Menghapus user
     */
    public function destroy(User $user)
    {
        // TODO: Tambahkan logic untuk cek relasi
        // Misalnya, jangan hapus user jika dia masih punya data profil
        // (Untuk saat ini, kita langsung hapus)

        try {
            // Hapus profil terkait terlebih dahulu
            $user->penyedia()->delete();
            $user->sekolah()->delete();
            // Hapus relasi laporan, dll
            
            $user->delete();
            return response()->json(['message' => 'User berhasil dihapus'], 200);

        } catch (\Exception $e) {
            // Tangkap error jika ada foreign key constraint
            return response()->json(['message' => 'Gagal menghapus user, mungkin masih memiliki data terkait.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getRoles()
    {
        // Kita tidak perlu mengirim 'admin' karena admin tidak dibuat
        // oleh admin lain. (Kecuali Anda mau, ganti '!= "admin"')
        $roles = Role::where('nama_role', '!=', 'admin')
                    //  ->where('nama_role', '!=', 'penyedia') // Penyedia mendaftar sendiri
                    //  ->where('nama_role', '!=', 'sekolah') // Sekolah mendaftar sendiri
                     ->get();

        // Jika Anda ingin admin bisa membuat SEMUA role:
        // $roles = Role::all(); 

        return response()->json($roles);
    }
}