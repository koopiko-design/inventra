<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Tool;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Default Admin & Contoh Siswa
        $admin = User::create([
            'name'     => 'Admin Sarpras SMKN 5 Surakarta',
            'nis'      => 'admin05',
            'email'    => 'admin@inventra.test',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        $siswa1 = User::create([
            'name'     => 'Budi Pratama',
            'nis'      => '2024001',
            'email'    => 'siswa@inventra.test',
            'password' => Hash::make('siswa123'),
            'role'     => 'siswa',
        ]);

        $siswa2 = User::create([
            'name'     => 'Siti Rahmawati',
            'nis'      => '2024002',
            'email'    => 'siti@inventra.test',
            'password' => Hash::make('siswa123'),
            'role'     => 'siswa',
        ]);

        // 2. Kategori Barang
        $fotografi = Category::create(['nama_kategori' => 'Fotografi & Video']);
        $komputer  = Category::create(['nama_kategori' => 'Komputer & Jaringan']);
        $elektronik = Category::create(['nama_kategori' => 'Elektronik']);

        // 3. Data Barang Inventaris (Tools)
        $toolDslr = Tool::create([
            'category_id' => $fotografi->id,
            'kode_alat'   => 'DSLR-01',
            'nama_alat'   => 'Kamera DSLR Canon EOS 800D',
            'deskripsi'   => 'Lensa Kit 18-55mm IS STM, baterai 2 pcs, SD Card 64GB, dan tas kamera.',
            'stok'        => 3,
            'status'      => 'tersedia'
        ]);

        $toolTripod = Tool::create([
            'category_id' => $fotografi->id,
            'kode_alat'   => 'TRP-01',
            'nama_alat'   => 'Tripod Takara Rover 66',
            'deskripsi'   => 'Bahan aluminium kokoh, kapasitas max 4kg, fluid head, termasuk holder HP & tas.',
            'stok'        => 5,
            'status'      => 'tersedia'
        ]);

        $toolLaptop = Tool::create([
            'category_id' => $komputer->id,
            'kode_alat'   => 'LPT-01',
            'nama_alat'   => 'Laptop ASUS Vivobook Core i5',
            'deskripsi'   => 'RAM 16GB, SSD 512GB, terinstall software Adobe CC & VS Code untuk praktikum multimedia.',
            'stok'        => 0,
            'status'      => 'habis'
        ]);

        $toolMikrotik = Tool::create([
            'category_id' => $komputer->id,
            'kode_alat'   => 'MKT-01',
            'nama_alat'   => 'Mikrotik RouterBOARD RB951Ui-2HnD',
            'deskripsi'   => '5 port Ethernet, High Power Wireless AP, lengkap dengan adaptor 24V dan kabel LAN.',
            'stok'        => 8,
            'status'      => 'tersedia'
        ]);

        $toolCrimping = Tool::create([
            'category_id' => $komputer->id,
            'kode_alat'   => 'CRM-01',
            'nama_alat'   => 'Crimping Tool & Cable Tester RJ45 Kit',
            'deskripsi'   => 'Tang crimping RJ45/RJ11 multifungsi, LAN tester digital dengan baterai 9V.',
            'stok'        => 12,
            'status'      => 'tersedia'
        ]);

        $toolSolder = Tool::create([
            'category_id' => $elektronik->id,
            'kode_alat'   => 'ELK-01',
            'nama_alat'   => 'Digital Solder Station Quick 936A',
            'deskripsi'   => 'Temperature adjustable 200°C - 480°C, stand solder dan sponge pembersih mata solder.',
            'stok'        => 6,
            'status'      => 'tersedia'
        ]);

        $toolProjector = Tool::create([
            'category_id' => $elektronik->id,
            'kode_alat'   => 'PRJ-01',
            'nama_alat'   => 'Projector Epson EB-X500 HDMI 3600 Lumens',
            'deskripsi'   => 'Input HDMI & VGA, resolusi XGA tajam, remote kontrol, dan tas jinjing.',
            'stok'        => 2,
            'status'      => 'tersedia'
        ]);

        // 4. Contoh Transaksi Peminjaman (Borrowings)
        Borrowing::create([
            'user_id'       => $siswa1->id,
            'tool_id'       => $toolDslr->id,
            'nama_peminjam' => 'Budi Pratama',
            'kelas_nis'     => 'XII RPL 1 / 2024001',
            'jumlah_unit'   => 1,
            'tgl_pinjam'    => now()->format('Y-m-d'),
            'tgl_kembali'   => now()->addDays(2)->format('Y-m-d'),
            'alasan_pinjam' => 'Praktikum Ujian Kompetensi Keahlian (UKK) Pembuatan Video Profil Sekolah.',
            'status'        => 'pending',
        ]);

        Borrowing::create([
            'user_id'       => $siswa2->id,
            'tool_id'       => $toolTripod->id,
            'nama_peminjam' => 'Siti Rahmawati',
            'kelas_nis'     => 'XI TKJ 2 / 2024002',
            'jumlah_unit'   => 1,
            'tgl_pinjam'    => now()->subDays(1)->format('Y-m-d'),
            'tgl_kembali'   => now()->addDays(1)->format('Y-m-d'),
            'alasan_pinjam' => 'Dokumentasi kegiatan workshop jaringan sekolah.',
            'status'        => 'disetujui',
        ]);

        Borrowing::create([
            'user_id'          => $siswa1->id,
            'tool_id'          => $toolMikrotik->id,
            'nama_peminjam'    => 'Budi Pratama',
            'kelas_nis'        => 'XII RPL 1 / 2024001',
            'jumlah_unit'      => 2,
            'tgl_pinjam'       => now()->subDays(5)->format('Y-m-d'),
            'tgl_kembali'      => now()->subDays(2)->format('Y-m-d'),
            'tgl_dikembalikan' => now()->subDays(2)->format('Y-m-d'),
            'alasan_pinjam'    => 'Konfigurasi routing dinamis OSPF lab komputer 3.',
            'status'           => 'dikembalikan',
            'kondisi_kembali'  => 'Baik dan lengkap beserta adaptor.',
        ]);
    }
}
