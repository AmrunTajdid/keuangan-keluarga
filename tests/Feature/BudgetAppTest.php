<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FamilyMember;
use App\Models\Wallet;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetAppTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_pin_login_page_loads(): void
    {
        $response = $this->get('/pin-login');
        $response->assertStatus(200);
        $response->assertSee('membuka brankas keluarga');
    }

    public function test_unauthenticated_user_redirected_to_pin_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/pin-login');
    }

    public function test_incorrect_pin_fails(): void
    {
        $response = $this->post('/pin-login', [
            'pin' => '999999',
        ]);
        $response->assertSessionHas('error');
    }

    public function test_correct_pin_succeeds(): void
    {
        $response = $this->post('/pin-login', [
            'pin' => '030923',
        ]);
        $response->assertRedirect('/');
        $this->assertTrue(session('pin_authenticated', false));
    }

    public function test_dashboard_page_loads_with_pin(): void
    {
        $response = $this->withSession(['pin_authenticated' => true])->get('/');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Finansial Keluarga');
    }

    public function test_transactions_page_loads(): void
    {
        $response = $this->withSession(['pin_authenticated' => true])->get('/transactions');
        $response->assertStatus(200);
        $response->assertSee('Catatan Transaksi Keuangan');
    }

    public function test_budgets_page_loads(): void
    {
        $response = $this->withSession(['pin_authenticated' => true])->get('/budgets');
        $response->assertStatus(200);
        $response->assertSee('Perencanaan Anggaran Bulanan');
    }

    public function test_wallets_page_loads(): void
    {
        $response = $this->withSession(['pin_authenticated' => true])->get('/wallets');
        $response->assertStatus(200);
        $response->assertSee('Dompet & Rekening Keluarga');
    }

    public function test_categories_page_loads(): void
    {
        $response = $this->withSession(['pin_authenticated' => true])->get('/categories');
        $response->assertStatus(200);
        $response->assertSee('Kategori Keuangan Keluarga');
    }

    public function test_family_members_page_loads(): void
    {
        $response = $this->withSession(['pin_authenticated' => true])->get('/family-members');
        $response->assertStatus(200);
        $response->assertSee('Daftar Anggota Keluarga');
    }

    public function test_savings_goals_page_loads(): void
    {
        $response = $this->withSession(['pin_authenticated' => true])->get('/savings-goals');
        $response->assertStatus(200);
        $response->assertSee('Rencana & Target Tabungan Keluarga');
    }

    public function test_reports_page_loads(): void
    {
        $response = $this->withSession(['pin_authenticated' => true])->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('LAPORAN KEUANGAN KELUARGA');
    }

    public function test_can_create_a_transaction(): void
    {
        $wallet = Wallet::first();
        $category = Category::where('type', 'expense')->first();
        $member = FamilyMember::first();

        $response = $this->withSession(['pin_authenticated' => true])->post('/transactions', [
            'type' => 'expense',
            'amount' => 50000,
            'transaction_date' => date('Y-m-d'),
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'family_member_id' => $member->id,
            'description' => 'Beli camilan sore untuk keluarga',
        ]);

        $response->assertRedirect('/transactions');
        $this->assertDatabaseHas('transactions', [
            'description' => 'Beli camilan sore untuk keluarga',
            'amount' => 50000,
        ]);
    }
}
