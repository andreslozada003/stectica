<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · Yeral Stetic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root { --pink:#dc4f87; --rose:#fff1f6; --ink:#332d35; --muted:#817780; --line:#f0e6ea; }
        * { box-sizing:border-box; }
        body { min-height:100vh; margin:0; display:grid; place-items:center; padding:24px; color:var(--ink); font-family:'DM Sans',sans-serif; background:radial-gradient(circle at 0% 0%,#ffe9f1 0,transparent 33%),radial-gradient(circle at 100% 100%,#fbe1eb 0,transparent 30%),#fcfafb; }
        .login { width:min(100%, 440px); background:#fff; border:1px solid var(--line); border-radius:20px; padding:42px; box-shadow:0 20px 55px #8b49631a; }
        .brand { display:flex; align-items:center; gap:11px; margin-bottom:35px; font-family:'Playfair Display',serif; font-size:23px; font-weight:700; }
        .brand-mark { width:38px; height:38px; display:grid; place-items:center; border-radius:12px; color:#fff; background:linear-gradient(145deg,#ef8ab1,#c73071); font-size:27px; }
        .eyebrow { color:var(--pink); font-size:10px; font-weight:700; letter-spacing:1.3px; text-transform:uppercase; }
        h1 { margin:7px 0 8px; font-family:'Playfair Display',serif; font-size:30px; }
        .subtitle { margin:0 0 28px; color:var(--muted); font-size:14px; line-height:1.55; }
        label { display:block; margin:18px 0 7px; font-size:13px; font-weight:600; }
        input[type="email"], input[type="password"] { width:100%; padding:12px 13px; color:var(--ink); border:1px solid #e7dce1; border-radius:9px; outline:0; font:14px 'DM Sans',sans-serif; transition:border .2s,box-shadow .2s; }
        input:focus { border-color:#df75a0; box-shadow:0 0 0 3px #fce2ed; }
        .options { display:flex; align-items:center; justify-content:space-between; gap:12px; margin:20px 0 24px; color:var(--muted); font-size:12px; }
        .remember { display:flex; align-items:center; gap:7px; cursor:pointer; } input[type="checkbox"] { accent-color:var(--pink); }
        button { width:100%; border:0; border-radius:9px; padding:13px; background:var(--pink); color:#fff; box-shadow:0 7px 16px #dc4f8733; cursor:pointer; font:600 14px 'DM Sans',sans-serif; }
        button:hover { background:#c93e75; }
        .alert { padding:11px 13px; border-radius:9px; font-size:13px; margin-bottom:18px; }.error { color:#a02d56; background:#fff0f4; border:1px solid #f8d4e0; }.success { color:#17745a; background:#e8f8f0; border:1px solid #cef0df; }
        .field-error { color:#b83261; font-size:12px; margin:6px 0 0; }
        @media(max-width:480px) { body { padding:14px; }.login { padding:30px 24px; } }
    </style>
</head>
<body>
    <main class="login">
        <div class="brand"><span class="brand-mark">Y</span><span>Yeral Stetic</span></div>
        <div class="eyebrow">Acceso al sistema</div>
        <h1>Bienvenida de nuevo</h1>
        <p class="subtitle">Ingresa tus datos para administrar la información de tu clínica.</p>

        @if (session('status'))
            <div class="alert success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert error">Revisa tus datos e inténtalo nuevamente.</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" autofocus required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror

            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror

            <div class="options">
                <label class="remember" for="remember"><input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))> Recordarme</label>
                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit">Iniciar sesión</button>
        </form>
    </main>
</body>
</html>
