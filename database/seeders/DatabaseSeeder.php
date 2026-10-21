<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\FamilyMember;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Family Members
        $ayah = FamilyMember::create([
            'name' => 'Ayah (Budi)',
            'role' => 'Kepala Keluarga',
            'avatar_color' => '#2563EB',
            'is_active' => true,
        ]);

        $ibu = FamilyMember::create([
            'name' => 'Ibu (Siti)',
            'role' => 'Bendahara Keluarga',
            'avatar_color' => '#EC4899',
            'is_active' => true,
        ]);

        $kakak = FamilyMember::create([
            'name' => 'Kakak (Rian)',
            'role' => 'Anak Pertama',
            'avatar_color' => '#10B981',
            'is_active' => true,
        ]);

        $adik = FamilyMember::create([
            'name' => 'Adik (Zahra)',
            'role' => 'Anak Kedua',
            'avatar_color' => '#F59E0B',
            'is_active' => true,
        ]);

        // 2. Wallets
        $bca = Wallet::create([
            'name' => 'BCA Payroll (Ayah)',
            'type' => 'bank',
            'account_number' => '8271928371',
            'initial_balance' => 12500000,
            'color' => '#1D4ED8',
        ]);

        $mandiri = Wallet::create([
            'name' => 'Mandiri Tabungan Keluarga',
            'type' => 'savings',
            'account_number' => '137001928374',
            'initial_balance' => 28000000,
            'color' => '#047857',
        ]);

        $cash = Wallet::create([
            'name' => 'Dompet Tunai Brankas / Fisik',
            'type' => 'cash',
            'account_number' => null,
            'initial_balance' => 1800000,
            'color' => '#D97706',
        ]);

        $gopay = Wallet::create([
            'name' => 'GoPay & OVO Belanja',
            'type' => 'ewallet',
            'account_number' => '081234567890',
            'initial_balance' => 950000,
            'color' => '#7C3AED',
        ]);

        // 3. Categories (Income)
        $catGaji = Category::create(['name' => 'Gaji Pokok', 'type' => 'income', 'icon' => 'briefcase', 'color' => '#10B981']);
        $catFreelance = Category::create(['name' => 'Usaha Sampingan & Bisnis', 'type' => 'income', 'icon' => 'rocket', 'color' => '#059669']);
        $catInvestasi = Category::create(['name' => 'Dividen & Bunga Investasi', 'type' => 'income', 'icon' => 'trending-up', 'color' => '#34D399']);
        $catBonus = Category::create(['name' => 'Bonus & THR', 'type' => 'income', 'icon' => 'gift', 'color' => '#6EE7B7']);

        // Categories (Expense)
        $catDapur = Category::create(['name' => 'Belanja Dapur & Makanan', 'type' => 'expense', 'icon' => 'shopping-cart', 'color' => '#EF4444']);
        $catTagihan = Category::create(['name' => 'Tagihan Listrik, Air & Wifi', 'type' => 'expense', 'icon' => 'zap', 'color' => '#F97316']);
        $catPendidikan = Category::create(['name' => 'Biaya Sekolah & Les Anak', 'type' => 'expense', 'icon' => 'book-open', 'color' => '#8B5CF6']);
        $catBensin = Category::create(['name' => 'Bensin & Transportasi', 'type' => 'expense', 'icon' => 'truck', 'color' => '#F59E0B']);
        $catKpr = Category::create(['name' => 'Cicilan KPR / Rumah', 'type' => 'expense', 'icon' => 'home', 'color' => '#6366F1']);
        $catKesehatan = Category::create(['name' => 'Kesehatan & Obat', 'type' => 'expense', 'icon' => 'activity', 'color' => '#EC4899']);
        $catSedekah = Category::create(['name' => 'Sedekah, Zakat & Infaq', 'type' => 'expense', 'icon' => 'heart', 'color' => '#14B8A6']);
        $catHiburan = Category::create(['name' => 'Hiburan & Liburan Keluarga', 'type' => 'expense', 'icon' => 'film', 'color' => '#3B82F6']);
        $catPribadi = Category::create(['name' => 'Pakaian & Perlengkapan Pribadi', 'type' => 'expense', 'icon' => 'user', 'color' => '#A855F7']);

        // 4. Budgets for Current Month & Year
        $now = Carbon::now();
        $currMonth = $now->month;
        $currYear = $now->year;

        $budgets = [
            ['cat' => $catDapur->id, 'amount' => 4000000, 'notes' => 'Bahan makanan mingguan & belanja sayur'],
            ['cat' => $catTagihan->id, 'amount' => 1200000, 'notes' => 'PLN Token + PDAM + Indihome'],
            ['cat' => $catPendidikan->id, 'amount' => 2500000, 'notes' => 'SPP Kakak & Adik + Kursus Bahasa Inggris'],
            ['cat' => $catBensin->id, 'amount' => 1500000, 'notes' => 'Bensin motor + mobil kerja Ayah'],
            ['cat' => $catKpr->id, 'amount' => 3500000, 'notes' => 'Cicilan tetap bulanan'],
            ['cat' => $catKesehatan->id, 'amount' => 800000, 'notes' => 'Vitamin dan cek dokter rutin'],
            ['cat' => $catSedekah->id, 'amount' => 1000000, 'notes' => 'Sedekah subuh & infaq Jumat'],
            ['cat' => $catHiburan->id, 'amount' => 1200000, 'notes' => 'Makan luar weekend & nonton'],
            ['cat' => $catPribadi->id, 'amount' => 800000, 'notes' => 'Perawatan & baju'],
        ];

        foreach ($budgets as $b) {
            Budget::create([
                'category_id' => $b['cat'],
                'month' => $currMonth,
                'year' => $currYear,
                'amount' => $b['amount'],
                'notes' => $b['notes'],
            ]);
        }

        // 5. Savings Goals
        SavingsGoal::create([
            'name' => 'Dana Darurat Keluarga (6 Bulan)',
            'target_amount' => 60000000,
            'current_amount' => 38500000,
            'target_date' => Carbon::now()->addMonths(10),
            'color' => '#2563EB',
            'notes' => 'Target 6x pengeluaran bulanan disimpan di reksadana pasar uang & deposito',
            'is_achieved' => false,
        ]);

        SavingsGoal::create([
            'name' => 'Tabungan Qurban Idul Adha',
            'target_amount' => 7500000,
            'current_amount' => 5000000,
            'target_date' => Carbon::now()->addMonths(6),
            'color' => '#10B981',
            'notes' => '1 ekor kambing / patungan sapi',
            'is_achieved' => false,
        ]);

        SavingsGoal::create([
            'name' => 'Liburan Akhir Tahun ke Jogja',
            'target_amount' => 10000000,
            'current_amount' => 8200000,
            'target_date' => Carbon::now()->addMonths(2),
            'color' => '#F59E0B',
            'notes' => 'Tiket kereta api + hotel 4 hari 3 malam',
            'is_achieved' => false,
        ]);

        SavingsGoal::create([
            'name' => 'Renovasi Kamar Tidur & Ruang Belajar',
            'target_amount' => 15000000,
            'current_amount' => 6500000,
            'target_date' => Carbon::now()->addMonths(8),
            'color' => '#8B5CF6',
            'notes' => 'Cat dinding, meja belajar bertingkat untuk anak',
            'is_achieved' => false,
        ]);

        // 6. Transactions (Realistic current month)
        // Income
        Transaction::create([
            'type' => 'income',
            'family_member_id' => $ayah->id,
            'category_id' => $catGaji->id,
            'wallet_id' => $bca->id,
            'amount' => 15000000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(1),
            'description' => 'Gaji Bulanan PT Maju Bersama (Ayah)',
        ]);

        Transaction::create([
            'type' => 'income',
            'family_member_id' => $ibu->id,
            'category_id' => $catFreelance->id,
            'wallet_id' => $mandiri->id,
            'amount' => 3800000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(5),
            'description' => 'Penjualan Katering & Kue Kering (Ibu)',
        ]);

        Transaction::create([
            'type' => 'income',
            'family_member_id' => $ayah->id,
            'category_id' => $catInvestasi->id,
            'wallet_id' => $mandiri->id,
            'amount' => 750000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(10),
            'description' => 'Dividen Reksadana Saham',
        ]);

        // Expense Transactions
        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ibu->id,
            'category_id' => $catDapur->id,
            'wallet_id' => $cash->id,
            'amount' => 850000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(2),
            'description' => 'Belanja mingguan pasar tradisional (Sayur, daging, buah)',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ibu->id,
            'category_id' => $catDapur->id,
            'wallet_id' => $gopay->id,
            'amount' => 1250000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(8),
            'description' => 'Belanja Supermarket bulanan (Beras, minyak, sabun, susu)',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ayah->id,
            'category_id' => $catTagihan->id,
            'wallet_id' => $bca->id,
            'amount' => 650000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(3),
            'description' => 'Token Listrik PLN 2200 VA',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ayah->id,
            'category_id' => $catTagihan->id,
            'wallet_id' => $bca->id,
            'amount' => 385000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(4),
            'description' => 'Paket Internet Wifi Indihome 50Mbps',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ibu->id,
            'category_id' => $catPendidikan->id,
            'wallet_id' => $bca->id,
            'amount' => 1400000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(5),
            'description' => 'SPP Sekolah Kakak & Adik bulan ini',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ibu->id,
            'category_id' => $catPendidikan->id,
            'wallet_id' => $cash->id,
            'amount' => 450000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(9),
            'description' => 'Buku pelajaran & alat tulis tambahan',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ayah->id,
            'category_id' => $catKpr->id,
            'wallet_id' => $bca->id,
            'amount' => 3500000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(6),
            'description' => 'Autodebet Cicilan KPR Rumah',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ayah->id,
            'category_id' => $catBensin->id,
            'wallet_id' => $gopay->id,
            'amount' => 400000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(7),
            'description' => 'Isi Pertamax Mobil & Tol',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $kakak->id,
            'category_id' => $catBensin->id,
            'wallet_id' => $cash->id,
            'amount' => 150000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(11),
            'description' => 'Bensin motor ke kampus & parkir',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ibu->id,
            'category_id' => $catSedekah->id,
            'wallet_id' => $cash->id,
            'amount' => 300000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(7),
            'description' => 'Sedekah santunan yatim dhuafa',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $ayah->id,
            'category_id' => $catHiburan->id,
            'wallet_id' => $gopay->id,
            'amount' => 450000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(12),
            'description' => 'Makan malam keluarga akhir pekan di Resto Padang',
        ]);

        Transaction::create([
            'type' => 'expense',
            'family_member_id' => $adik->id,
            'category_id' => $catKesehatan->id,
            'wallet_id' => $cash->id,
            'amount' => 180000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(14),
            'description' => 'Beli vitamin anak & obat flu di apotek',
        ]);

        // Transfer Antar Rekening
        Transaction::create([
            'type' => 'transfer',
            'family_member_id' => $ayah->id,
            'wallet_id' => $bca->id,
            'to_wallet_id' => $gopay->id,
            'amount' => 1000000,
            'transaction_date' => Carbon::now()->startOfMonth()->addDays(6),
            'description' => 'Top Up GoPay operasional keluarga dari BCA',
        ]);
    }
}
