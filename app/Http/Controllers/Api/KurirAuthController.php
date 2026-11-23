<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kurir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class KurirAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:20',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data login tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $kurir = Kurir::where('kode', trim($data['kode']))->first();

        if (!$kurir || !Hash::check($data['password'], $kurir->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode kurir atau password salah.',
            ], 401);
        }

        if (!$kurir->status) {
            return response()->json([
                'success' => false,
                'message' => 'Akun kurir tidak aktif.',
            ], 403);
        }

        $kedaluwarsa = now()->addDays(30);
        $token = $kurir->createToken('PaketIN Android', ['*'], $kedaluwarsa);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $kedaluwarsa->toIso8601String(),
                'wajib_ganti_password' => (bool) $kurir->gantipassword,
                'kurir' => $this->dataKurir($kurir),
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Profil kurir berhasil diambil.',
            'data' => [
                'kurir' => $this->dataKurir($request->user()),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    public function gantiPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:8|confirmed|different:password_lama',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data password tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $kurir = $request->user();
        $data = $validator->validated();

        if (!Hash::check($data['password_lama'], $kurir->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama salah.',
            ], 422);
        }

        $kurir->password = Hash::make($data['password_baru']);
        $kurir->gantipassword = false;
        $kurir->save();

        $tokenAktif = $kurir->currentAccessToken();

        if ($tokenAktif instanceof PersonalAccessToken) {
            $kurir->tokens()
                ->where('id', '!=', $tokenAktif->getKey())
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diganti.',
            'data' => [
                'wajib_ganti_password' => false,
                'kurir' => $this->dataKurir($kurir),
            ],
        ]);
    }

    private function dataKurir(Kurir $kurir): array
    {
        return [
            'id' => $kurir->id,
            'kode' => $kurir->kode,
            'nama' => $kurir->nama,
            'telepon' => $kurir->telepon,
        ];
    }
}
