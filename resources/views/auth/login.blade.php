<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | Villa Merah Information System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        *{
            font-family:'Poppins',sans-serif;
        }

        body{
            background:#f8fafc;
        }

        .login-card{
            display:block;
            border:1px solid #111;
            border-radius:0;
            overflow:hidden;
            box-shadow:0 18px 45px rgba(0,0,0,.12);
            width:100%;
            max-width:640px;
        }

        .left-panel{
            display:none;
        }

        .right-panel{
            background:#fff;
            box-shadow:none;
            text-align:center;
        }

        .right-panel form, .right-panel .flex.justify-between { text-align:left; }

        .animate-card{
            animation:fade .7s ease;
        }

        @keyframes fade{

            from{

                opacity:0;

                transform:translateY(25px);

            }

            to{

                opacity:1;

                transform:translateY(0);

            }

        }

        input{

            transition:.25s;

        }

        input:focus{

            transform:scale(1.01);

        }

    </style>

</head>

<body class="min-h-screen bg-white flex justify-center items-center p-4 sm:p-8">

<div id="page-loader" aria-live="polite" aria-label="Memuat halaman">
    <div class="page-loader-inner"><span class="page-loader-spinner" aria-hidden="true"></span><span>Memuat halaman...</span></div>
</div>
<div id="route-progress" aria-hidden="true"></div>

<div class="w-full max-w-xl animate-card">

<div class="login-card w-full">

    <!-- LEFT -->
    <div class="left-panel text-white flex-col justify-center items-center p-14">        <img
            src="{{ asset('images/logo-vm-mh.png') }}"
            class="w-60 h-60 object-contain mb-8">

        <h1 class="text-5xl font-bold">
            VILMER
        </h1>

        <p class="mt-4 text-xl text-white text-center">
            Villa Merah Information System
        </p>

        <p class="mt-2 text-white/80 text-center">
            Sistem Informasi SDM VMI
        </p>

    </div>

    <!-- RIGHT -->
        <div class="right-panel p-6 sm:p-12">
        <div class="flex justify-center mb-8">

            <img
            src="{{ asset('images/logo-vm-mh.png') }}"
                class="w-44 h-44 sm:w-52 sm:h-52 object-contain">

        </div>

        <h2 class="text-3xl font-bold text-gray-800">
            Selamat Datang
        </h2>

        <p class="text-gray-500 mt-2">
            Silakan login menggunakan akun Anda.
        </p>

        @if ($errors->any())

        <div
        class="bg-red-100 border border-red-300 text-red-700 rounded-lg p-3 mt-6">

            {{ $errors->first() }}

        </div>

        @endif

        <form
        action="{{ route('login.store') }}"
        method="POST"
        class="mt-8 space-y-5">

            @csrf

            <!-- EMAIL OR NIP -->

            <div>

                <label
                class="font-semibold text-gray-700">

                    Email atau NIP

                </label>

                <input
                    type="text"
                    name="login"
                    value="{{ old('login') }}"
                    required
                    autofocus

                    class="mt-2 w-full rounded-xl border-2 border-gray-200 px-5 py-3
                    focus:border-black
                    focus:ring-4
                    focus:ring-gray-300
                    outline-none transition">

            </div>

            <!-- PASSWORD -->

            <div>

                <label
                class="font-semibold text-gray-700">

                    Password

                </label>

                <div class="relative mt-2">

                    <input

                        id="password"

                        type="password"

                        name="password"

                        required

                        class="w-full rounded-xl border-2 border-gray-200 px-5 py-3 pr-14
                        focus:border-black
                        focus:ring-4
                        focus:ring-gray-300
                        outline-none transition">

                    <button

                    type="button"

                    onclick="togglePassword()"

                    class="absolute right-4 top-3 text-gray-500 hover:text-black">

                        👁

                    </button>

                </div>

            </div>

            <div class="flex justify-between items-center">

                <label class="flex items-center gap-2">

                    <input
                    type="checkbox"
                    name="remember">

                    <span class="text-gray-600">

                        Ingat Saya

                    </span>

                </label>

            </div>

            <button

            class="w-full py-3 rounded-xl
            bg-black
            hover:bg-gray-800
            text-white
            font-semibold
            transition
            duration-300
            shadow-lg
            hover:shadow-2xl
            hover:scale-[1.02]">

                Login

            </button>

        </form>

        <div class="mt-10 text-center text-sm text-gray-400">

            © {{ date('Y') }} Villa Merah Information System

        </div>

    </div>

</div>

</div>

<script>

function togglePassword(){

let x=document.getElementById("password");

if(x.type==="password"){

x.type="text";

}else{

x.type="password";

}

}

</script>

<script src="{{ asset('js/app.js') }}" defer></script>

</body>
</html>
