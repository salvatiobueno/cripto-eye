<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eye - Market Intelligence</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="text-gray-100">
  <div class="background-grid"></div>
  <div class="bg-orb bg-orb-cyan"></div>
  <div class="bg-orb bg-orb-purple"></div>

  <main class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-10">
    <header class="hero-shell mb-6">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <p class="text-xs uppercase tracking-widest text-cyan-200">Eye Intelligence Console</p>
          <h1 class="mt-2 text-3xl font-extrabold leading-tight text-white sm:text-4xl">Panel de inteligencia de mercado</h1>
          <p class="mt-2 max-w-2xl text-sm text-gray-300">Terminal visual para detectar sincronias, anomalias y relaciones entre criptoactivos en tiempo real con explicaciones generadas por IA cloud.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <span class="badge badge-live">LIVE</span>
          <span id="marketStatePill" class="badge badge-neutral">neutral</span>
          <div class="glass rounded-xl px-4 py-2 text-sm">
            <span class="text-gray-300">Actualizacion</span>
            <span id="lastUpdate" class="ml-2 font-semibold text-cyan-300">--:--:--</span>
          </div>
        </div>
      </div>
    </header>

    <section class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
      <article class="metric-card"><p>Estado de mercado</p><h3 id="marketState">neutral</h3></article>
      <article class="metric-card"><p>BTC Dominancia</p><h3 id="btcDom">0%</h3></article>
      <article class="metric-card"><p>Promedio 24h</p><h3 id="avg24h">0%</h3></article>
      <article class="metric-card"><p>Anomalias activas</p><h3 id="anomalyCount">0</h3></article>
    </section>

    <section class="mb-6 grid grid-cols-1 gap-4 xl:grid-cols-12">
      <article class="glass section-panel p-5 xl:col-span-8">
        <div class="mb-3 flex items-center justify-between">
          <h2 class="section-title">Tendencias de momentum</h2>
          <span class="badge">Actualizacion continua</span>
        </div>
        <canvas id="momentumChart" height="105"></canvas>
      </article>
      <article class="glass section-panel p-5 xl:col-span-4">
        <h2 class="section-title mb-3">Mapa de correlacion</h2>
        <div id="correlationList" class="space-y-2 text-sm"></div>
      </article>
    </section>

    <section class="mb-6 grid grid-cols-1 gap-4 xl:grid-cols-12">
      <article class="glass section-panel p-5 xl:col-span-8">
        <h2 class="section-title mb-3">Dashboard principal</h2>
        <div class="table-shell overflow-x-auto">
          <table class="w-full table-min text-sm text-gray-200">
            <thead class="text-gray-300">
              <tr>
                <th class="py-2 text-left">Activo</th><th class="text-right">Precio</th><th class="text-right">1h</th>
                <th class="text-right">24h</th><th class="text-right">7d</th><th class="text-right">Cap</th>
                <th class="text-right">Vol</th><th class="text-right">Momentum</th><th class="text-right">Mini chart</th>
              </tr>
            </thead>
            <tbody id="coinsTable"></tbody>
          </table>
        </div>
      </article>
      <article class="glass section-panel p-5 xl:col-span-4">
        <h2 class="section-title mb-3">Alertas visuales</h2>
        <div id="alertPanel" class="space-y-2"></div>
      </article>
    </section>

    <section class="mb-6 grid grid-cols-1 gap-4 xl:grid-cols-12">
      <article class="glass section-panel p-5 xl:col-span-4">
        <h2 class="section-title mb-3">Heatmap de variacion 24h</h2>
        <div id="heatmap" class="grid grid-cols-2 gap-2 sm:grid-cols-3"></div>
      </article>
      <article class="glass section-panel p-5 xl:col-span-8">
        <h2 class="section-title mb-3">Insights IA</h2>
        <div id="insights" class="insight-body leading-relaxed text-gray-100">Cargando analisis...</div>
      </article>
    </section>

    <footer class="glass legal-note mt-6 p-4 text-sm text-gray-300">
      Los patrones detectados por IA son unicamente informativos y no constituyen asesoramiento financiero ni garantia predictiva.
    </footer>
  </main>

  <script src="assets/js/app.js" defer></script>
</body>
</html>
