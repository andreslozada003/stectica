<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeral Stetic · Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root { --pink:#dc4f87; --rose:#fff1f6; --ink:#332d35; --muted:#817780; --line:#f0e6ea; --card:#fff; --green:#198b69; --amber:#c98421; }
        * { box-sizing:border-box } body { margin:0; background:#fcfafb; color:var(--ink); font-family:'DM Sans',sans-serif; }
        .app { display:grid; grid-template-columns:254px 1fr; min-height:100vh; }
        aside { background:#fff; border-right:1px solid var(--line); padding:30px 18px; display:flex; flex-direction:column; }
        .brand { display:flex; align-items:center; gap:11px; padding:0 10px 32px; font-family:'Playfair Display',serif; font-size:22px; font-weight:700; }
        .brand-mark { width:34px; height:34px; display:grid; place-items:center; border-radius:11px; color:#fff; background:linear-gradient(145deg,#ef8ab1,#c73071); font-family:serif; font-size:25px; }
        .section-label { font-size:10px; font-weight:700; letter-spacing:1.4px; color:#b3a8ae; padding:12px 12px 8px; }
        nav a { color:#716871; text-decoration:none; padding:11px 12px; display:flex; align-items:center; gap:12px; border-radius:10px; font-size:14px; margin:2px 0; }
        nav a.active, nav a:hover { color:#b82664; background:var(--rose); font-weight:600; } .nav-icon { font-size:17px; width:18px; text-align:center; }
        .profile { margin-top:auto; padding:14px 10px 0; border-top:1px solid var(--line); display:flex; gap:10px; align-items:center; }.profile-info { flex:1; min-width:0; }.logout { width:auto; padding:5px 7px; color:#9b5871; background:#fff; border:1px solid var(--line); font-size:11px; box-shadow:none; }
        .avatar { background:#f6b8d0; color:#92234f; border-radius:50%; width:36px; height:36px; display:grid; place-items:center; font-weight:700; font-size:13px; }
        .profile b,.profile small { display:block; font-size:12px; } .profile small { color:var(--muted); margin-top:2px; }
        main { padding:32px 42px; max-width:1550px; width:100%; margin:0 auto; }
        .topbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:31px; } h1 { font-family:'Playfair Display',serif; font-size:29px; margin:3px 0; } .eyebrow { text-transform:uppercase; letter-spacing:1.2px; font-size:10px; color:var(--pink); font-weight:700; }
        .actions { display:flex; gap:10px; } button { border:0; border-radius:9px; padding:11px 15px; font:600 13px 'DM Sans'; cursor:pointer; } .ghost { background:#fff; border:1px solid var(--line); color:#5b5259; } .primary { background:var(--pink); color:#fff; box-shadow:0 7px 16px #dc4f8733; text-decoration:none; display:inline-flex; align-items:center; }
        .metrics { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }.metric { background:var(--card); padding:19px; border:1px solid var(--line); border-radius:14px; }.metric-top { display:flex; justify-content:space-between; color:var(--muted); font-size:12px; }.metric-icon { background:var(--rose); color:#c63b72; width:30px;height:30px;border-radius:9px;display:grid;place-items:center;font-size:15px; }.metric strong { display:block; font-size:25px; margin:14px 0 5px; letter-spacing:-.6px; }.change { font-size:11px; color:var(--green); font-weight:600; }.change.neutral {color:var(--muted)}
        .grid { display:grid; grid-template-columns:1.65fr 1fr; gap:19px; }.card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:21px; }.card-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:17px; }.card h2 { margin:0; font-size:15px; }.link { color:var(--pink); font-size:12px; font-weight:600; text-decoration:none; }
        .date { color:var(--muted); font-size:12px; margin-top:4px; }.appointment { display:grid; grid-template-columns:54px 1fr auto; align-items:center; gap:13px; border-top:1px solid #f5eff1; padding:13px 0; }.appointment:first-of-type{border-top:0}.time { font-weight:700; font-size:13px; }.appointment b { font-size:13px; }.appointment span { color:var(--muted); font-size:11px; display:block; margin-top:3px; }.badge { border-radius:99px; padding:5px 9px; font-size:10px; font-weight:700; }.confirmed { color:#15775a; background:#e4f7f0; }.scheduled { color:#a96d13; background:#fff4df; }
        .mini-calendar { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; text-align:center; font-size:11px; color:var(--muted); }.weekday { font-weight:700; font-size:9px; color:#b3a8ae; padding-bottom:7px; }.day { padding:8px 2px; border-radius:8px; }.day.selected { background:var(--pink); color:#fff; font-weight:700; }.day.has-event { color:var(--pink); font-weight:700; background:#fff1f6; }.empty { visibility:hidden; }
        .stock { margin-top:18px; }.stock-item { padding:12px 0; border-top:1px solid #f5eff1; display:flex; justify-content:space-between; align-items:center; }.stock-item b { font-size:12px; display:block; }.stock-item span { color:var(--muted); font-size:10px; display:block; margin-top:3px; }.level { font-size:10px; padding:4px 7px; border-radius:5px; background:#e4f7f0;color:#147458; font-weight:700; }.low { background:#fff0f2;color:#c53d57; }
        .quick { margin-top:19px; display:grid; grid-template-columns:repeat(3,1fr); gap:11px; }.quick a { text-decoration:none; color:#5d5159; border:1px dashed #e9d8df; border-radius:10px; padding:13px 9px; text-align:center; font-size:11px; font-weight:600; }.quick i { display:block; font-style:normal; font-size:19px; color:var(--pink); margin-bottom:6px; }
        @media(max-width:960px){.app{grid-template-columns:72px 1fr}aside{padding:25px 10px}.brand{padding:0 8px 25px}.brand span,nav a span:not(.nav-icon),.section-label,.profile div{display:none}nav a{justify-content:center;padding:12px}.profile{justify-content:center}main{padding:25px}.metrics{grid-template-columns:repeat(2,1fr)}.grid{grid-template-columns:1fr}}@media(max-width:600px){.app{display:block}aside{display:none}main{padding:20px}.topbar{align-items:flex-start}.actions .ghost{display:none}.metrics{grid-template-columns:1fr 1fr;gap:10px}.metric{padding:14px}.metric strong{font-size:21px}.topbar h1{font-size:24px}.primary{padding:10px}.appointment{grid-template-columns:43px 1fr}.badge{display:none}}
    </style>
</head>
<body>
<div class="app">
    <aside>
        <div class="brand"><div class="brand-mark">Y</div><span>Yeral Stetic</span></div>
        <nav>
            <div class="section-label">Principal</div>
            <a href="#" class="active"><span class="nav-icon">⌂</span><span>Panel general</span></a>
            <a href="{{ route('agenda.index') }}"><span class="nav-icon">◫</span><span>Agenda y citas</span></a>
            <a href="{{ route('pacientes.index') }}"><span class="nav-icon">♙</span><span>Pacientes</span></a>
            <div class="section-label">Gestión clínica</div>
            <a href="#"><span class="nav-icon">✦</span><span>Tratamientos</span></a>
            <a href="#"><span class="nav-icon">▧</span><span>Consentimientos</span></a>
            <a href="#"><span class="nav-icon">▣</span><span>Inventario</span></a>
            <div class="section-label">Administración</div>
            <a href="#"><span class="nav-icon">◉</span><span>Pagos y ventas</span></a>
            <a href="#"><span class="nav-icon">⚙</span><span>Configuración</span></a>
        </nav>
        <div class="profile"><div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div class="profile-info"><b>{{ auth()->user()->name }}</b><small>{{ auth()->user()->isAdmin() ? 'Administrador' : 'Colaborador' }}</small></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Salir</button></form></div>
    </aside>
    <main>
        <header class="topbar"><div><div class="eyebrow">Resumen operativo</div><h1>Buenos días, Yeral</h1><div class="date">{{ ucfirst($today) }}</div></div><div class="actions"><button class="ghost">⌕ &nbsp; Buscar</button><a class="primary" href="{{ route('agenda.create') }}">＋ Nueva cita</a></div></header>
        <section class="metrics">
            <div class="metric"><div class="metric-top"><span>Citas de hoy</span><div class="metric-icon">◫</div></div><strong>12</strong><span class="change">↑ 20% <em style="font-style:normal;color:#a399a1;font-weight:400">vs. ayer</em></span></div>
            <div class="metric"><div class="metric-top"><span>Pacientes activos</span><div class="metric-icon">♙</div></div><strong>248</strong><span class="change">↑ 8 nuevos <em style="font-style:normal;color:#a399a1;font-weight:400">este mes</em></span></div>
            <div class="metric"><div class="metric-top"><span>Ingresos del mes</span><div class="metric-icon">$</div></div><strong>$8.460.000</strong><span class="change">↑ 14.2% <em style="font-style:normal;color:#a399a1;font-weight:400">vs. mes anterior</em></span></div>
            <div class="metric"><div class="metric-top"><span>Stock por reponer</span><div class="metric-icon">▣</div></div><strong>4</strong><span class="change neutral">Productos con alerta</span></div>
        </section>
        <section class="grid">
            <div class="card"><div class="card-head"><div><h2>Agenda de hoy</h2><div class="date">Viernes, 2 de octubre</div></div><a href="#" class="link">Ver agenda completa →</a></div>
                @foreach($appointments as $appointment)
                <div class="appointment"><div class="time">{{ $appointment['time'] }}</div><div><b>{{ $appointment['patient'] }}</b><span>{{ $appointment['service'] }} · {{ $appointment['specialist'] }}</span></div><div class="badge {{ $appointment['status'] === 'Confirmada' ? 'confirmed' : 'scheduled' }}">{{ $appointment['status'] }}</div></div>
                @endforeach
            </div>
            <div>
                <div class="card"><div class="card-head"><h2>Octubre 2026</h2><a class="link" href="#">›</a></div><div class="mini-calendar"><div class="weekday">LUN</div><div class="weekday">MAR</div><div class="weekday">MIÉ</div><div class="weekday">JUE</div><div class="weekday">VIE</div><div class="weekday">SÁB</div><div class="weekday">DOM</div>@for($i=0;$i<3;$i++)<div class="empty day">0</div>@endfor @for($i=1;$i<=31;$i++)<div class="day {{ $i === 2 ? 'selected' : ($i === 6 || $i === 14 || $i === 22 ? 'has-event' : '') }}">{{ $i }}</div>@endfor</div></div>
                <div class="card stock"><div class="card-head"><h2>Alertas de inventario</h2><a href="#" class="link">Ver todo</a></div><div class="stock-item"><div><b>Ácido hialurónico 1 ml</b><span>Vence: 18 Oct 2026</span></div><div class="level low">2 unidades</div></div><div class="stock-item"><div><b>Guantes de nitrilo M</b><span>Stock mínimo: 10 cajas</span></div><div class="level low">6 cajas</div></div><div class="stock-item"><div><b>Protector solar FPS 50</b><span>Producto cosmetológico</span></div><div class="level">18 unidades</div></div></div>
            </div>
        </section>
        <section class="quick"><a href="{{ route('pacientes.create') }}"><i>＋</i>Registrar paciente</a><a href="#"><i>✦</i>Nueva sesión</a><a href="#"><i>$</i>Registrar pago</a></section>
    </main>
</div>
</body>
</html>
