<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\RiwayatStatus;
use App\Services\PaketService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KurirPaketController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->query(), [
            // Endpoint operasional kurir hanya boleh memuat paket Dikirim.
            'status' => ['nullable', 'string', Rule::in(['Dikirim'])],
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Filter paket tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $filter = $validator->validated();
        $daftarPaket = Paket::with('setoran')
            ->where('id_kurir', $request->user()->id)
            ->where('status', 'Dikirim')
            ->orderByDesc('id')
            ->paginate((int) ($filter['per_page'] ?? 20));

        return response()->json([
            'success' => true,
            'message' => 'Daftar paket berhasil diambil.',
            'data' => $daftarPaket->getCollection()
                ->map(fn (Paket $paket) => $this->dataPaket($paket))
                ->values(),
            'pagination' => [
                'current_page' => $daftarPaket->currentPage(),
                'last_page' => $daftarPaket->lastPage(),
                'per_page' => $daftarPaket->perPage(),
                'total' => $daftarPaket->total(),
            ],
        ]);
    }

    public function show(Request $request, int $id)
    {
        $paket = Paket::with(['setoran', 'daftarRiwayat'])
            ->where('id_kurir', $request->user()->id)
            ->where('status', 'Dikirim')
            ->find($id);

        if (!$paket) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail paket berhasil diambil.',
            'data' => $this->dataPaket($paket, true),
        ]);
    }

    public function updateStatus(Request $request, int $id, PaketService $paketService)
    {
        $validator = Validator::make($request->all(), [
            'status' => [
                'required',
                'string',
                Rule::in(['Terkirim', 'Gagal Kirim']),
            ],
            'catatan' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data perubahan status tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $kurir = $request->user();
        $paket = Paket::where('id_kurir', $kurir->id)->find($id);

        if (!$paket) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak ditemukan.',
            ], 404);
        }

        $data = $validator->validated();

        try {
            $paketService->laporkanHasilAntar(
                $paket,
                $kurir,
                $data['status'],
                $data['catatan'] ?? null
            );
        } catch (AuthorizationException $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 403);
        } catch (AturanBisnisException $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 422);
        } catch (ModelNotFoundException $error) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak ditemukan.',
            ], 404);
        }

        $paket->refresh()->load('setoran');

        return response()->json([
            'success' => true,
            'message' => 'Status paket berhasil diperbarui.',
            'data' => $this->dataPaket($paket),
        ]);
    }

    public function codBelumDisetor(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Filter COD tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dasar = Paket::query()
            ->where('id_kurir', $request->user()->id)
            ->where('status_pembayaran', true)
            ->where('status', 'Terkirim')
            ->where('nilai_cod', '>', 0);

        $belum = (clone $dasar)->whereDoesntHave('setoran');
        $jumlahBelum = (clone $belum)->count();
        $totalBelum = (int) (clone $belum)->sum('nilai_cod');
        $totalSudah = (int) (clone $dasar)->whereHas('setoran')->sum('nilai_cod');

        $daftarCOD = (clone $dasar)->with('setoran')
            ->orderByDesc('id')
            ->paginate((int) ($validator->validated()['per_page'] ?? 20));

        return response()->json([
            'success' => true,
            'message' => 'Daftar COD berhasil diambil.',
            'summary' => [
                'jumlah_paket' => $jumlahBelum,
                'total_belum_disetor' => $totalBelum,
                'total_harus_disetor' => $totalBelum,
                'total_sudah_disetor' => $totalSudah,
            ],
            'data' => $daftarCOD->getCollection()
                ->map(fn (Paket $paket) => [
                    'id' => $paket->id,
                    'no_resi' => $paket->no_resi,
                    'nama_penerima' => $paket->nama_penerima,
                    'kota_tujuan' => $paket->kota_tujuan,
                    'nilai_cod' => (int) $paket->nilai_cod,
                    'sudah_disetor' => $paket->setoran !== null,
                    'tanggal_masuk' => $paket->tanggal_masuk?->toDateString(),
                ])
                ->values(),
            'pagination' => [
                'current_page' => $daftarCOD->currentPage(),
                'last_page' => $daftarCOD->lastPage(),
                'per_page' => $daftarCOD->perPage(),
                'total' => $daftarCOD->total(),
            ],
        ]);
    }

    private function dataPaket(Paket $paket, bool $denganRiwayat = false): array
    {
        $isCod = (bool) $paket->status_pembayaran;

        if (!$isCod) {
            $statusSetoran = 'bukan_cod';
        } elseif ($paket->setoran) {
            $statusSetoran = 'sudah_disetor';
        } elseif ($paket->status === 'Terkirim') {
            $statusSetoran = 'belum_disetor';
        } else {
            $statusSetoran = 'menunggu_pengiriman';
        }

        $data = [
            'id' => $paket->id,
            'no_resi' => $paket->no_resi,
            'status' => $paket->status,
            'penerima' => [
                'nama' => $paket->nama_penerima,
                'telepon' => $paket->telepon_penerima,
                'alamat' => $paket->alamat_penerima,
                'kota' => $paket->kota_tujuan,
            ],
            'zona' => $paket->zona,
            'status_zona' => (bool) $paket->status_zona,
            'layanan' => $paket->layanan,
            'berat_asli' => $paket->berat_asli,
            'berat_bulat' => $paket->berat_bulat,
            'ongkir' => (int) ($paket->ongkir ?? 0),
            'perlu_verifikasi' => (bool) $paket->perlu_verifikasi,
            'cod' => [
                'aktif' => $isCod,
                'nilai_cod' => $isCod ? (int) ($paket->nilai_cod ?? 0) : null,
                'biaya_cod' => $isCod ? (int) $paket->biaya_cod : 0,
                'status_setoran' => $statusSetoran,
                'tanggal_disetor' => $paket->setoran?->disetor_pada?->toIso8601String(),
            ],
            'jumlah_gagal' => (int) $paket->jumlah_gagal,
            'tanggal_masuk' => $paket->tanggal_masuk?->toDateString(),
        ];

        if ($denganRiwayat) {
            $data['riwayat'] = $paket->daftarRiwayat
                ->map(fn (RiwayatStatus $riwayat) => [
                    'status_dari' => $riwayat->status_dari,
                    'status_ke' => $riwayat->status_ke,
                    'catatan' => $riwayat->catatan,
                    'pelaku' => $riwayat->tipe_pelaku,
                    'tanggal' => $riwayat->dibuat_pada?->toIso8601String(),
                ])
                ->values();
        }

        return $data;
    }
}
