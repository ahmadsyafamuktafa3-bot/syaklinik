<?php

namespace Database\Seeders;

use App\Models\Antrean;
use App\Models\DetailPembayaran;
use App\Models\DetailResep;
use App\Models\Obat;
use App\Models\Pembayaran;
use App\Models\Pemeriksaan;
use App\Models\Pendaftaran;
use App\Models\Pengguna;
use App\Models\RekamMedis;
use App\Models\Resep;
use App\Models\StokHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Cache::get('demo_data_seeded')) {
            $this->command->info('Data demo sudah pernah diisi. Hapus kunci cache demo_data_seeded untuk mengisi ulang.');
            return;
        }

        $pasienIds = [1, 2, 4, 5];
        $dokterIds = [1, 2, 3];
        $jadwalIds = [1, 2, 5, 6, 7];
        $tindakanId = 1;
        $tindakanTarif = 100000;
        $adminId = optional(Pengguna::where('role', 'admin')->first())->id;

        $keluhanPool = [
            'Demam sejak 2 hari', 'Batuk berdahak', 'Sakit kepala berdenyut',
            'Nyeri tenggorokan saat menelan', 'Perut kembung dan mual', 'Pilek dan bersin-bersin',
            'Nyeri gigi di rahang bawah', 'Pusing dan mudah lelah', 'Tubuh terasa pegal seluruhnya',
            'Mata merah dan berair', 'Ruam gatal di lengan', 'Sering haus dan buang air kecil',
            'Nyeri ulu hati setelah makan', 'Kram perut bagian kiri', 'Pegal di punggung bawah',
            'Sariawan di bibir', 'Gusi berdarah saat sikat gigi', 'Telinga berdenging',
            'Nafsu makan menurun', 'Tenggorokan terasa gatal',
        ];

        $diagnosisPool = [
            'Demam tifoid', 'ISPA / Infeksi Saluran Pernapasan Atas', 'Migrain',
            'Faringitis akut', 'Dispepsia', 'Rhinitis alergi', 'Pulpitis / gigi berlubang',
            'Hipertensi derajat 1', 'Diabetes melitus tipe 2', 'Dermatitis',
            'Konjungtivitis', 'Urtikaria', 'Gastritis', 'Infeksi saluran kemih',
            'Mialgia / nyeri otot', 'Gingivitis', 'Tinnitus', 'Anemia defisiensi besi',
            'Vertigo', 'Konstipasi fungsional',
        ];

        $fisikPool = [
            'TD 120/80 mmHg, N 88x/menit, S 37.2 C',
            'TD 130/85 mmHg, N 92x/menit, S 37.8 C',
            'TD 110/70 mmHg, N 80x/menit, S 36.9 C',
            'TD 140/90 mmHg, N 100x/menit, S 37.5 C',
        ];

        $aturanPool = [
            '3x sehari 1 tablet', '2x sehari 1 kapsul', '3x sehari 1 sendok takar',
            '1x sehari 1 tablet setelah makan', 'Sesuai kebutuhan',
        ];

        $obatSeeds = [
            ['Paracetamol 500 mg', 'Tablet', 'strip', 5000, 0],
            ['Amoxicillin 500 mg', 'Kapsul', 'strip', 12000, 0],
            ['Ibuprofen 400 mg', 'Tablet', 'strip', 7000, 0],
            ['Cetirizine 10 mg', 'Tablet', 'strip', 8000, 0],
            ['Loratadine 10 mg', 'Tablet', 'strip', 9000, 0],
            ['Vitamin C 50 mg', 'Tablet', 'strip', 4000, 0],
            ['Vitamin B Complex', 'Tablet', 'strip', 5000, 0],
            ['Zinc Sulfate 30 mg', 'Tablet', 'strip', 6000, 0],
            ['Ranitidine 150 mg', 'Tablet', 'strip', 3000, 0],
            ['Omeprazole 20 mg', 'Kapsul', 'strip', 15000, 0],
            ['Domperidone 10 mg', 'Tablet', 'strip', 10000, 0],
            ['Metformin 500 mg', 'Tablet', 'strip', 9000, 0],
            ['Amlodipine 10 mg', 'Tablet', 'strip', 11000, 0],
            ['Captopril 25 mg', 'Tablet', 'strip', 8000, 0],
            ['Dexamethasone 0.5 mg', 'Tablet', 'strip', 6000, 0],
            ['Prednison 5 mg', 'Tablet', 'strip', 5000, 0],
            ['Salbutamol 2 mg', 'Tablet', 'strip', 4000, 0],
            ['Ambroxol 30 mg', 'Tablet', 'strip', 6000, 0],
            ['Carbocisteine 250 mg', 'Kapsul', 'strip', 9000, 0],
            ['CTM 4 mg', 'Tablet', 'strip', 2000, 0],
            ['Paracetamol Sirup 120 ml', 'Sirup', 'botol', 15000, 0],
            ['Amoxicillin Sirup 60 ml', 'Sirup', 'botol', 18000, 0],
            ['Antasida DOEN', 'Tablet', 'strip', 3000, 0],
            ['Ondansetron 8 mg', 'Tablet', 'strip', 12000, 0],
            ['Meloxicam 7.5 mg', 'Tablet', 'strip', 10000, 0],
            ['Asam Mefenamat 500 mg', 'Tablet', 'strip', 8000, 0],
            ['Betadine Solution 60 ml', 'Cairan', 'botol', 25000, 0],
            ['Alcohol Swab', 'Peralatan', 'box', 10000, 0],
            ['Kassa Steril', 'Peralatan', 'box', 8000, 0],
            ['Plester Medis', 'Peralatan', 'roll', 6000, 0],
            ['Obat Tetes Mata Cendo', 'Tetes', 'botol', 15000, 0],
            ['Gentamicin Salep', 'Salep', 'tube', 12000, 0],
            ['Miconazole Krim', 'Krim', 'tube', 18000, 0],
            ['Hidrokortison Krim', 'Krim', 'tube', 15000, 0],
            ['Betametason Krim', 'Krim', 'tube', 16000, 0],
            ['Curcuma Sirup', 'Sirup', 'botol', 20000, 0],
            ['Suplemen Kalsium', 'Tablet', 'strip', 12000, 0],
            ['Asam Folat 400 mcg', 'Tablet', 'strip', 5000, 0],
            ['Ferrous Sulfate 300 mg', 'Tablet', 'strip', 4000, 0],
            ['Vitamin D3 1000 IU', 'Kapsul', 'strip', 20000, 0],
            ['Glimepiride 2 mg', 'Tablet', 'strip', 14000, 0],
            ['Atorvastatin 20 mg', 'Tablet', 'strip', 25000, 0],
            ['Furosemide 40 mg', 'Tablet', 'strip', 6000, 0],
            ['Bisoprolol 5 mg', 'Tablet', 'strip', 15000, 0],
            ['Ciprofloxacin 500 mg', 'Tablet', 'strip', 18000, 0],
            ['Azithromycin 500 mg', 'Tablet', 'strip', 20000, 0],
            ['Ivermectin 12 mg', 'Tablet', 'strip', 8000, 0],
            ['Albendazol 400 mg', 'Tablet', 'strip', 10000, 0],
            ['Mebendazol 500 mg', 'Tablet', 'strip', 9000, 0],
            ['Chloramphenicol Tetes Mata', 'Tetes', 'botol', 12000, 0],
        ];

        $obatModels = [];

        foreach ($obatSeeds as $i => [$nama, $jenis, $satuan, $harga]) {
            $kode = 'OBR-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            $habis = in_array($i, [9, 39]);
            $kedaluwarsa = in_array($i, [6, 28]);

            $stok = $habis ? 0 : 150 + (($i * 13) % 300);

            $obat = Obat::updateOrCreate(
                ['kode_obat' => $kode],
                [
                    'nama_obat' => $nama,
                    'jenis_obat' => $jenis,
                    'satuan' => $satuan,
                    'harga' => $harga,
                    'stok' => $stok,
                    'tanggal_kedaluwarsa' => $kedaluwarsa ? '2026-05-01' : null,
                    'status' => $kedaluwarsa ? 'kedaluwarsa' : ($habis ? 'habis' : 'tersedia'),
                ]
            );

            $obatModels[] = $obat;
        }

        $usable = [];
        foreach ($obatModels as $i => $obat) {
            if (in_array($i, [6, 28, 9, 39])) {
                continue;
            }
            $usable[] = $obat;
        }

        $start = now()->startOfDay()->subDays(54);
        $usage = [];
        $perDayBayar = [];

        DB::transaction(function () use (
            $pasienIds, $dokterIds, $jadwalIds, $tindakanId, $tindakanTarif, $adminId,
            $keluhanPool, $diagnosisPool, $fisikPool, $aturanPool, $usable, $start,
            &$usage, &$perDayBayar
        ) {
            $perDayDaftar = [];
            $perDayAntrean = [];

            for ($i = 0; $i < 50; $i++) {
                $pasienId = $pasienIds[$i % 4];
                $dokterId = $dokterIds[$i % 3];
                $jadwalId = $jadwalIds[$i % 5];
                $tanggal = $start->copy()->addDays($i)->toDateString();

                if (!isset($perDayDaftar[$tanggal])) {
                    $perDayDaftar[$tanggal] = (int) substr(Pendaftaran::where('nomor_pendaftaran', 'like', 'DAFTAR-' . str_replace('-', '', $tanggal) . '-%')->max('nomor_pendaftaran') ?? '', -4);
                }

                $pendaftaran = Pendaftaran::create([
                    'nomor_pendaftaran' => 'DAFTAR-' . str_replace('-', '', $tanggal) . '-' . str_pad(($perDayDaftar[$tanggal] = ($perDayDaftar[$tanggal] ?? 0) + 1), 4, '0', STR_PAD_LEFT),
                    'pasien_id' => $pasienId,
                    'dokter_id' => $dokterId,
                    'jadwal_id' => $jadwalId,
                    'tanggal_kunjungan' => $tanggal,
                    'keluhan' => $keluhanPool[$i % 20],
                    'status' => 'terdaftar',
                ]);

                if (!isset($perDayAntrean[$tanggal])) {
                    $perDayAntrean[$tanggal] = Antrean::where('tanggal', $tanggal)->count();
                }

                Antrean::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'nomor_antrean' => ($perDayAntrean[$tanggal] = ($perDayAntrean[$tanggal] ?? 0) + 1),
                    'tanggal' => $tanggal,
                    'status' => 'diperiksa',
                ]);

                $pemeriksaan = Pemeriksaan::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'pasien_id' => $pasienId,
                    'dokter_id' => $dokterId,
                    'tanggal_pemeriksaan' => $tanggal,
                    'keluhan' => $keluhanPool[$i % 20],
                    'pemeriksaan_fisik' => $fisikPool[$i % 4],
                    'diagnosis' => $diagnosisPool[$i % 20],
                    'catatan' => 'Perawatan lanjutan direncanakan bila keluhan berlanjut.',
                ]);

                $pakaiTindakan = $i % 2 === 0;
                if ($pakaiTindakan) {
                    $pemeriksaan->tindakans()->sync([$tindakanId => ['biaya' => $tindakanTarif]]);
                }

                RekamMedis::create([
                    'pasien_id' => $pasienId,
                    'pemeriksaan_id' => $pemeriksaan->id,
                    'tanggal' => $tanggal,
                    'keluhan' => $keluhanPool[$i % 20],
                    'diagnosis' => $diagnosisPool[$i % 20],
                    'tindakan' => $pakaiTindakan ? 'Konsultasi dokter' : null,
                    'catatan' => 'Perawatan lanjutan direncanakan bila keluhan berlanjut.',
                ]);

                $ditebus = $i % 3 === 0;

                $resep = Resep::create([
                    'pemeriksaan_id' => $pemeriksaan->id,
                    'pasien_id' => $pasienId,
                    'dokter_id' => $dokterId,
                    'tanggal' => $tanggal,
                    'catatan_resep' => 'Diserahkan beserta obat sesuai aturan pakai.',
                    'status' => $ditebus ? 'ditebus' : 'belum_ditebus',
                ]);

                $idxA = $i % count($usable);
                $idxB = ($i + 7) % count($usable);
                $pilih = [$idxA];

                if ($i % 3 !== 1) {
                    while ($idxB === $idxA) {
                        $idxB = ($idxB + 1) % count($usable);
                    }
                    $pilih[] = $idxB;
                }

                if ($i % 5 === 0) {
                    $idxC = ($i + 13) % count($usable);
                    while (in_array($idxC, $pilih)) {
                        $idxC = ($idxC + 1) % count($usable);
                    }
                    $pilih[] = $idxC;
                }

                $totalObat = 0;
                $items = [];

                foreach ($pilih as $p) {
                    $obat = $usable[$p];
                    $jumlah = ($i % 3) + 1;
                    $subtotal = (int) $obat->harga * $jumlah;

                    DetailResep::create([
                        'resep_id' => $resep->id,
                        'obat_id' => $obat->id,
                        'jumlah' => $jumlah,
                        'aturan_pakai' => $aturanPool[$i % 5],
                        'harga' => $obat->harga,
                        'subtotal' => $subtotal,
                    ]);

                    $usage[$obat->id] = ($usage[$obat->id] ?? 0) + $jumlah;
                    $totalObat += $subtotal;
                    $items[] = [
                        'jenis_pelayanan' => 'Obat',
                        'nama_pelayanan' => $obat->nama_obat,
                        'jumlah' => $jumlah,
                        'harga' => (int) $obat->harga,
                        'subtotal' => $subtotal,
                    ];
                }

                if ($ditebus) {
                    $total = $totalObat + ($pakaiTindakan ? $tindakanTarif : 0);
                    $lunas = $i % 6 === 0;

                    if (!isset($perDayBayar[$tanggal])) {
                        $perDayBayar[$tanggal] = (int) substr(Pembayaran::where('nomor_pembayaran', 'like', 'BYR-' . str_replace('-', '', $tanggal) . '-%')->max('nomor_pembayaran') ?? '', -3);
                    }

                    $pembayaran = Pembayaran::create([
                        'nomor_pembayaran' => 'BYR-' . str_replace('-', '', $tanggal) . '-' . str_pad(($perDayBayar[$tanggal] = ($perDayBayar[$tanggal] ?? 0) + 1), 3, '0', STR_PAD_LEFT),
                        'pasien_id' => $pasienId,
                        'pendaftaran_id' => $pendaftaran->id,
                        'pemeriksaan_id' => $pemeriksaan->id,
                        'tanggal' => $tanggal,
                        'total' => $total,
                        'bayar' => $lunas ? $total : 0,
                        'kembalian' => $lunas ? 0 : 0,
                        'status' => $lunas ? 'lunas' : 'belum_lunas',
                    ]);

                    if ($pakaiTindakan) {
                        DetailPembayaran::create([
                            'pembayaran_id' => $pembayaran->id,
                            'jenis_pelayanan' => 'Tindakan',
                            'nama_pelayanan' => 'Konsultasi dokter',
                            'jumlah' => 1,
                            'harga' => $tindakanTarif,
                            'subtotal' => $tindakanTarif,
                        ]);
                    }

                    foreach ($items as $item) {
                        DetailPembayaran::create(array_merge(['pembayaran_id' => $pembayaran->id], $item));
                    }
                }
            }
        });

        foreach ($usage as $obatId => $jumlah) {
            $obat = Obat::find($obatId);
            if (!$obat) {
                continue;
            }
            $obat->decrement('stok', $jumlah);
            StokHistory::create([
                'obat_id' => $obat->id,
                'jenis' => 'keluar',
                'jumlah' => $jumlah,
                'keterangan' => 'Pemakaian data demo (resep)',
                'tanggal' => now()->toDateString(),
                'pengguna_id' => $adminId,
            ]);
            $this->perbaruiStatusObat($obat);
        }

        Cache::forever('demo_data_seeded', true);

        $this->command->info('Data demo berhasil ditambahkan: 50 obat, 50 pendaftaran, 50 antrean, 50 pemeriksaan, 50 rekam medis, 50 resep, dan tagihan otomatis untuk resep yang ditebus.');
    }

    private function perbaruiStatusObat(Obat $obat): void
    {
        if ($obat->status === 'kedaluwarsa') {
            return;
        }
        $obat->update(['status' => $obat->stok <= 0 ? 'habis' : 'tersedia']);
    }
}