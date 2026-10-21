<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Brankas - KeuanganKeluarga</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-tr from-sky-100 via-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4 text-slate-800">

    <div class="max-w-sm w-full bg-white rounded-3xl shadow-xl shadow-blue-500/10 border border-slate-200/80 p-6 sm:p-8 flex flex-col items-center text-center relative overflow-hidden">

        <!-- Decorative Glow -->
        <div class="absolute -top-12 -left-12 w-32 h-32 bg-blue-400/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-indigo-400/10 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Lock Icon Badge -->
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/30 text-2xl mb-4">
            🔐
        </div>

        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Keuangan<span class="text-blue-600">Keluarga</span></h2>
        <p class="text-xs text-slate-500 font-medium mt-1 mb-6">Masukkan 6 digit PIN untuk membuka brankas keluarga</p>

        @if(session('error'))
            <div class="w-full mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center justify-center space-x-2 animate-bounce">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="w-full mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center justify-center space-x-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Form PIN -->
        <form method="POST" action="{{ route('pin.verify') }}" class="w-full space-y-4">
            @csrf

            <!-- PIN Display Input -->
            <div class="relative">
                <input
                    type="password"
                    name="pin"
                    id="pinInput"
                    maxlength="6"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    autocomplete="off"
                    required
                    autofocus
                    placeholder="• • • • • •"
                    class="w-full text-center tracking-[0.5em] text-2xl font-black py-3 px-4 rounded-2xl border-2 border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all placeholder:tracking-widest placeholder:text-slate-300"
                >
            </div>

            <!-- Virtual Keypad (Touch friendly) -->
            <div class="grid grid-cols-3 gap-2 pt-2">
                @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $num)
                    <button type="button" onclick="appendPin('{{ $num }}')" class="py-3 text-lg font-black text-slate-700 bg-slate-50 hover:bg-slate-100 active:bg-blue-50 active:text-blue-600 rounded-xl transition-colors border border-slate-200/60 shadow-xs">
                        {{ $num }}
                    </button>
                @endforeach
                <button type="button" onclick="clearPin()" class="py-3 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors border border-slate-200/60">
                    Hapus
                </button>
                <button type="button" onclick="appendPin('0')" class="py-3 text-lg font-black text-slate-700 bg-slate-50 hover:bg-slate-100 active:bg-blue-50 active:text-blue-600 rounded-xl transition-colors border border-slate-200/60 shadow-xs">
                    0
                </button>
                <button type="button" onclick="deletePin()" class="py-3 text-sm font-bold text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors border border-slate-200/60">
                    ⌫
                </button>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full mt-2 py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:scale-[0.99] text-white font-bold text-sm rounded-2xl shadow-md shadow-blue-500/25 transition-all">
                Buka Brankas 🔓
            </button>
        </form>

        <p class="text-[11px] text-slate-400 mt-6">
            AmarDella &bull; KeuanganKeluarga
        </p>

    </div>

    <script>
        const pinInput = document.getElementById('pinInput');

        function appendPin(val) {
            if (pinInput.value.length < 6) {
                pinInput.value += val;
                if (pinInput.value.length === 6) {
                    // Auto submit when 6 digits entered
                    pinInput.form.submit();
                }
            }
        }

        function deletePin() {
            pinInput.value = pinInput.value.slice(0, -1);
        }

        function clearPin() {
            pinInput.value = '';
        }
    </script>
</body>
</html>
