<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Role;
use App\Models\Penyedia;
use App\Models\Sekolah;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Untuk transaction

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Coba autentikasi
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Buat token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Load relasi role untuk dikirim ke frontend
        $user->load('role');

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user, // Kirim data user termasuk rolenya
        ], 200);
    }

    /**
     * Handle user registration for 'Penyedia'.
     */
    public function registerPenyedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Data User
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            
            // Data Profil Penyedia
            'nama_perusahaan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil role_id 'penyedia'
        $role = Role::where('nama_role', 'penyedia')->firstOrFail();

        // Gunakan DB Transaction untuk memastikan data konsisten
        try {
            DB::beginTransaction();

            // 1. Buat User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $role->id,
            ]);

            // 2. Buat Profil Penyedia
            Penyedia::create([
                'user_id' => $user->id,
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
            ]);

            DB::commit();

            return response()->json(['message' => 'Registrasi penyedia berhasil'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registrasi gagal, terjadi kesalahan.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle user registration for 'Sekolah'.
     */
    public function registerSekolah(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Data User
            'name' => 'required|string|max:255', // Ini akan sama dengan nama sekolah
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            
            // Data Profil Sekolah
            'kelurahan_id' => 'required|exists:kelurahans,id',
            'npsn' => 'required|string|unique:sekolahs',
            'alamat' => 'required|string',
            'jenis_sekolah' => 'required|string|max:50', // Misal: 'SD'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::where('nama_role', 'sekolah')->firstOrFail();

        try {
            DB::beginTransaction();

            // 1. Buat User
            $user = User::create([
                'name' => $request->name, // Nama user = nama sekolah
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $role->id,
            ]);

            // 2. Buat Profil Sekolah
            Sekolah::create([
                'user_id' => $user->id,
                'kelurahan_id' => $request->kelurahan_id,
                'npsn' => $request->npsn,
                'nama_sekolah' => $request->name, // Nama sekolah dari request 'name'
                'alamat' => $request->alamat,
                'jenis_sekolah' => $request->jenis_sekolah,
            ]);

            DB::commit();

            return response()->json(['message' => 'Registrasi sekolah berhasil'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registrasi gagal, terjadi kesalahan.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('role');

        return response()->json($user, 200);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        // Hapus token saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}