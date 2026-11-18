<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Tool;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class InventraTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Admin Sarpras SMKN 5 Surakarta',
            'nis'      => 'admin05',
            'email'    => 'admin@inventra.test',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        $this->siswa = User::create([
            'name'     => 'Budi Pratama',
            'nis'      => '2024001',
            'email'    => 'siswa@inventra.test',
            'password' => Hash::make('siswa123'),
            'role'     => 'siswa',
        ]);

        $this->category = Category::create([
            'nama_kategori' => 'Fotografi & Video'
        ]);

        $this->tool = Tool::create([
            'category_id' => $this->category->id,
            'kode_alat'   => 'DSLR-01',
            'nama_alat'   => 'Kamera DSLR Canon EOS 800D',
            'deskripsi'   => 'Lensa Kit 18-55mm IS STM',
            'stok'        => 3,
            'status'      => 'tersedia'
        ]);
    }

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('INVENTRA');
        $response->assertSee('SMK Negeri 5 Surakarta');
    }

    public function test_katalog_page_shows_tools(): void
    {
        $response = $this->get('/katalog');
        $response->assertStatus(200);
        $response->assertSee('Kamera DSLR Canon EOS 800D');
        $response->assertSee('Fotografi & Video');
    }

    public function test_admin_can_login_with_username_admin05(): void
    {
        $response = $this->post('/login', [
            'role'     => 'admin',
            'username' => 'admin05',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_siswa_can_login_with_nis(): void
    {
        $response = $this->post('/login', [
            'role'     => 'siswa',
            'username' => '2024001',
            'password' => 'siswa123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($this->siswa);
    }

    public function test_login_rejects_mismatching_role(): void
    {
        // Mencoba login dengan akun admin tapi memilih role siswa
        $response = $this->post('/login', [
            'role'     => 'siswa',
            'username' => 'admin05',
            'password' => 'admin123',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_student_can_register(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Ahmad Zaki',
            'nis'                   => '2024099',
            'email'                 => 'ahmad@inventra.test',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'nis'  => '2024099',
            'role' => 'siswa',
        ]);
    }

    public function test_student_can_submit_borrowing_request_locked_to_one_unit_and_three_days(): void
    {
        $chosenDate = Carbon::now()->addDays(5)->format('Y-m-d');
        $expectedReturnDate = Carbon::parse($chosenDate)->addDays(3)->format('Y-m-d');

        $response = $this->actingAs($this->siswa)->post('/ajukan', [
            'tool_id'       => $this->tool->id,
            'nama_peminjam' => 'Budi Pratama',
            'kelas_nis'     => 'XII RPL 1 / 2024001',
            'tgl_pinjam'    => $chosenDate,
            'alasan_pinjam' => 'Praktik fotografi studio sekolah',
        ]);

        $response->assertRedirect(route('riwayat.saya'));
        
        $borrowing = Borrowing::where('nama_peminjam', 'Budi Pratama')->first();
        $this->assertNotNull($borrowing);
        $this->assertEquals(1, $borrowing->jumlah_unit);
        $this->assertEquals($chosenDate, $borrowing->tgl_pinjam);
        $this->assertEquals($expectedReturnDate, $borrowing->tgl_kembali);
        $this->assertEquals('pending', $borrowing->status);
    }

    public function test_admin_can_verify_and_approve_borrowing(): void
    {
        $borrowing = Borrowing::create([
            'user_id'       => $this->siswa->id,
            'tool_id'       => $this->tool->id,
            'nama_peminjam' => 'Budi Pratama',
            'kelas_nis'     => 'XII RPL 1 / 2024001',
            'jumlah_unit'   => 1,
            'tgl_pinjam'    => now()->format('Y-m-d'),
            'tgl_kembali'   => now()->addDays(3)->format('Y-m-d'),
            'alasan_pinjam' => 'Praktik fotografi',
            'status'        => 'pending',
        ]);

        $initialStock = $this->tool->stok;

        $response = $this->actingAs($this->admin)->post("/borrowing/verify/{$borrowing->id}", [
            'action' => 'approve'
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('disetujui', $borrowing->fresh()->status);
        $this->assertEquals($initialStock - 1, $this->tool->fresh()->stok);
    }

    public function test_admin_can_reject_borrowing_with_rejection_reason(): void
    {
        $borrowing = Borrowing::create([
            'user_id'       => $this->siswa->id,
            'tool_id'       => $this->tool->id,
            'nama_peminjam' => 'Budi Pratama',
            'kelas_nis'     => 'XII RPL 1 / 2024001',
            'jumlah_unit'   => 1,
            'tgl_pinjam'    => now()->format('Y-m-d'),
            'tgl_kembali'   => now()->addDays(3)->format('Y-m-d'),
            'alasan_pinjam' => 'Praktik fotografi',
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/borrowing/verify/{$borrowing->id}", [
            'action'           => 'reject',
            'alasan_penolakan' => 'Jadwal peminjaman bentrok dengan ujian UKK'
        ]);

        $response->assertSessionHas('info');
        $this->assertEquals('ditolak', $borrowing->fresh()->status);
        $this->assertEquals('Jadwal peminjaman bentrok dengan ujian UKK', $borrowing->fresh()->rejection_reason);
        $this->assertEquals('Jadwal peminjaman bentrok dengan ujian UKK', $borrowing->fresh()->alasan_penolakan);
    }

    public function test_admin_can_confirm_tool_return(): void
    {
        $this->tool->update(['stok' => 2]);

        $borrowing = Borrowing::create([
            'user_id'       => $this->siswa->id,
            'tool_id'       => $this->tool->id,
            'nama_peminjam' => 'Budi Pratama',
            'kelas_nis'     => 'XII RPL 1 / 2024001',
            'jumlah_unit'   => 1,
            'tgl_pinjam'    => now()->format('Y-m-d'),
            'tgl_kembali'   => now()->addDays(3)->format('Y-m-d'),
            'alasan_pinjam' => 'Praktik fotografi',
            'status'        => 'disetujui',
        ]);

        $response = $this->actingAs($this->admin)->post("/borrowing/return/{$borrowing->id}", [
            'kondisi_kembali' => 'Lengkap dan baik'
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('dikembalikan', $borrowing->fresh()->status);
        $this->assertEquals(3, $this->tool->fresh()->stok);
    }

    public function test_admin_can_delete_borrowing(): void
    {
        $borrowing = Borrowing::create([
            'user_id'       => $this->siswa->id,
            'tool_id'       => $this->tool->id,
            'nama_peminjam' => 'Budi Pratama',
            'kelas_nis'     => 'XII RPL 1 / 2024001',
            'jumlah_unit'   => 1,
            'tgl_pinjam'    => now()->format('Y-m-d'),
            'tgl_kembali'   => now()->addDays(3)->format('Y-m-d'),
            'alasan_pinjam' => 'Praktik fotografi',
            'status'        => 'dikembalikan',
        ]);

        $response = $this->actingAs($this->admin)->delete("/borrowing/{$borrowing->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('borrowings', [
            'id' => $borrowing->id,
        ]);
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->siswa)->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_crud_tools(): void
    {
        // 1. Create tool
        $createResponse = $this->actingAs($this->admin)->post('/admin/barang', [
            'kode_alat'   => 'LPT-02',
            'nama_alat'   => 'Laptop Lenovo ThinkPad',
            'category_id' => $this->category->id,
            'deskripsi'   => 'RAM 16GB Core i7',
            'stok'        => 5,
        ]);
        $createResponse->assertRedirect('/admin/barang');
        $this->assertDatabaseHas('tools', ['kode_alat' => 'LPT-02']);

        $newTool = Tool::where('kode_alat', 'LPT-02')->first();

        // 2. Update tool
        $updateResponse = $this->actingAs($this->admin)->put("/admin/barang/{$newTool->id}", [
            'kode_alat'   => 'LPT-02',
            'nama_alat'   => 'Laptop Lenovo ThinkPad Updated',
            'category_id' => $this->category->id,
            'deskripsi'   => 'RAM 32GB Core i7',
            'stok'        => 8,
        ]);
        $updateResponse->assertRedirect('/admin/barang');
        $this->assertEquals('Laptop Lenovo ThinkPad Updated', $newTool->fresh()->nama_alat);
        $this->assertEquals(8, $newTool->fresh()->stok);

        // 3. Delete tool
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/barang/{$newTool->id}");
        $deleteResponse->assertRedirect('/admin/barang');
        $this->assertDatabaseMissing('tools', ['id' => $newTool->id]);
    }

    public function test_admin_can_crud_users(): void
    {
        // 1. Create user with role petugas
        $createResponse = $this->actingAs($this->admin)->post('/admin/users', [
            'name'     => 'Petugas Sarpras Rudi',
            'nis'      => 'PTG01',
            'email'    => 'rudi@inventra.test',
            'password' => 'secret123',
            'role'     => 'petugas',
        ]);
        $createResponse->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => 'rudi@inventra.test', 'role' => 'petugas']);

        $user = User::where('email', 'rudi@inventra.test')->first();

        // 2. Update user
        $updateResponse = $this->actingAs($this->admin)->put("/admin/users/{$user->id}", [
            'name'  => 'Petugas Sarpras Rudi Updated',
            'nis'   => 'PTG01',
            'email' => 'rudi@inventra.test',
            'role'  => 'admin',
        ]);
        $updateResponse->assertRedirect('/admin/users');
        $this->assertEquals('Petugas Sarpras Rudi Updated', $user->fresh()->name);
        $this->assertEquals('admin', $user->fresh()->role);

        // 3. Delete user
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/users/{$user->id}");
        $deleteResponse->assertRedirect('/admin/users');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_can_access_rekap_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/rekap');
        $response->assertStatus(200);
        $response->assertSee('REKAP PEMINJAMAN ALAT INVENTARIS');
    }

    public function test_admin_can_export_rekap_csv(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/rekap/export');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
