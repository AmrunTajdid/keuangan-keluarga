<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSetupController extends Controller
{
    public function index()
    {
        $connection = config('database.default');
        $isVercel = isset($_SERVER['VERCEL']) || getenv('VERCEL') || isset($_ENV['VERCEL']);
        $isSqlite = $connection === 'sqlite';

        $dbStatus = 'ok';
        $dbError = null;
        $tablesExist = false;
        $tableCounts = [];

        try {
            DB::connection()->getPdo();
            $tablesExist = Schema::hasTable('transactions') && Schema::hasTable('budgets');

            if ($tablesExist) {
                $tableCounts = [
                    'transactions' => DB::table('transactions')->count(),
                    'budgets' => DB::table('budgets')->count(),
                    'wallets' => DB::table('wallets')->count(),
                    'categories' => DB::table('categories')->count(),
                    'family_members' => DB::table('family_members')->count(),
                    'savings_goals' => DB::table('savings_goals')->count(),
                ];
            }
        } catch (\Throwable $e) {
            $dbStatus = 'error';
            $dbError = $e->getMessage();
        }

        return view('system.database', compact(
            'connection',
            'isVercel',
            'isSqlite',
            'dbStatus',
            'dbError',
            'tablesExist',
            'tableCounts'
        ));
    }

    public function migrate(Request $request)
    {
        $pin = $request->input('pin', $request->query('pin'));
        $expectedPin = config('app.family_pin', env('FAMILY_PIN', '030923'));

        if (!session('pin_authenticated', false) && $pin !== $expectedPin) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'PIN salah atau belum terautentikasi.',
            ], 403);
        }

        try {
            // Neon PostgreSQL: PgBouncer pooler (-pooler) causes transaction aborts on DDL migrations.
            // If the URL contains '-pooler.', automatically switch to direct connection.
            $currentUrl = config('database.connections.pgsql.url') ?: (getenv('DATABASE_URL') ?: getenv('DB_URL'));
            if ($currentUrl && strpos($currentUrl, '-pooler.') !== false) {
                $directUrl = str_replace('-pooler.', '.', $currentUrl);
                config(['database.connections.pgsql.url' => $directUrl]);
                DB::purge('pgsql');
            }

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();

            $seedOutput = '';
            if ($request->input('seed', '1') === '1' || $request->query('seed') === '1') {
                Artisan::call('db:seed', ['--force' => true]);
                $seedOutput = Artisan::output();
            }

            $isPublicUrl = $request->is('system/migrate*');

            if ($request->expectsJson() || $request->ajax() || $isPublicUrl) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Migrasi dan inisialisasi database berhasil!',
                    'migrate_output' => $migrateOutput,
                    'seed_output' => $seedOutput,
                ]);
            }

            return redirect()->route('system.database')->with('success', 'Migrasi dan inisialisasi data default database berhasil dijalankan!');
        } catch (\Throwable $e) {
            $isPublicUrl = $request->is('system/migrate*');
            if ($request->expectsJson() || $request->ajax() || $isPublicUrl) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menjalankan migrasi: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('system.database')->with('error', 'Gagal menjalankan migrasi: ' . $e->getMessage());
        }
    }
}
