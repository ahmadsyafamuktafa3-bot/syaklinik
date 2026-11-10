<?php

namespace Database\Seeders;

use App\Models\Tindakan;
use Illuminate\Database\Seeder;

class TindakanSeeder extends Seeder
{
    public function run(): void
    {
        $tindakan = [
            ['Konsultasi dokter', 50000],
            ['Mencabut gigi', 100000],
            ['Penambalan gigi', 150000],
            ['Pembersihan karang gigi', 200000],
            ['Perawatan gigi (scaling)', 250000],
            ['Pencabutan gigi impacted', 750000],
            ['Pemasangan kawat gigi', 650000],
            ['Pemeriksaan tekanan darah', 25000],
            ['Pemeriksaan gula darah', 35000],
            ['Rawat luka simple', 75000],
            ['Jahitan luka', 200000],
            ['Perawatan luka bakar', 180000],
            ['Suntik intramuskular', 30000],
            ['Suntik intravenosus', 45000],
            ['Infus vitamin', 150000],
            ['Nebulisasi / inhalasi', 120000],
            ['Fisioterapi anggota tubuh', 125000],
            ['Pijat terapi', 90000],
            ['Rontgen', 250000],
            ['USG perut', 350000],
            ['EKG (rekam jantung)', 300000],
            ['Tes urine', 40000],
            ['Imunisasi / vaksinasi', 35000],
            ['Konsultasi gizi', 100000],
            ['Konsultasi psikolog', 200000],
        ];

        foreach ($tindakan as [$nama, $tarif]) {
            Tindakan::updateOrCreate(
                ['nama_tindakan' => $nama],
                ['tarif' => $tarif]
            );
        }
    }
}
