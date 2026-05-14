const fmtUsd = new Intl.NumberFormat("en-US", { style: "currency", currency: "USD", maximumFractionDigits: 2 });
const fmtCompact = new Intl.NumberFormat("en-US", { notation: "compact", maximumFractionDigits: 2 });
let momentumChart;

async function fetchJson(url) {
  const res = await fetch(url, { headers: { "Accept": "application/json" } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

function pct(v) {
  const n = Number(v || 0);
  const cls = n > 0 ? "text-emerald-300" : n < 0 ? "text-rose-300" : "text-gray-300";
  return `<span class="${cls}">${n.toFixed(2)}%</span>`;
}

function momentum(v1, v24, v7) {
  return (v1 * 0.5) + (v24 * 0.35) + (v7 * 0.15);
}

function renderSparkline(values = []) {
  const width = 96, height = 32;
  if (!Array.isArray(values) || values.length < 2) return "";
  const min = Math.min(...values), max = Math.max(...values), d = max - min || 1;
  const points = values.map((v, i) => `${(i / (values.length - 1)) * width},${height - ((v - min) / d) * height}`).join(" ");
  return `<svg class="sparkline" viewBox="0 0 ${width} ${height}" fill="none"><polyline points="${points}" stroke="#38bdf8" stroke-width="1.8" /></svg>`;
}

function renderCoinsTable(coins) {
  const rows = coins.map((c) => {
    const m = momentum(c.price_change_percentage_1h_in_currency || 0, c.price_change_percentage_24h_in_currency || 0, c.price_change_percentage_7d_in_currency || 0);
    return `<tr class="border-t border-gray-700 fade-in">
      <td class="py-2"><div class="font-semibold text-gray-100">${c.name}</div><div class="text-xs text-gray-400">${String(c.symbol).toUpperCase()}</div></td>
      <td class="text-right">${fmtUsd.format(c.current_price || 0)}</td>
      <td class="text-right">${pct(c.price_change_percentage_1h_in_currency)}</td>
      <td class="text-right">${pct(c.price_change_percentage_24h_in_currency)}</td>
      <td class="text-right">${pct(c.price_change_percentage_7d_in_currency)}</td>
      <td class="text-right">${fmtCompact.format(c.market_cap || 0)}</td>
      <td class="text-right">${fmtCompact.format(c.total_volume || 0)}</td>
      <td class="text-right ${m > 0 ? "text-emerald-300" : "text-rose-300"}">${m.toFixed(2)}</td>
      <td class="text-right">${renderSparkline(c.sparkline_in_7d?.price || [])}</td>
    </tr>`;
  }).join("");
  document.getElementById("coinsTable").innerHTML = rows;
}

function renderHeatmap(coins) {
  const html = coins.slice(0, 12).map((c) => {
    const v = Number(c.price_change_percentage_24h_in_currency || 0);
    const abs = Math.min(Math.abs(v), 12) / 12;
    const color = v >= 0 ? `rgba(16,185,129,${0.2 + abs * 0.55})` : `rgba(244,63,94,${0.2 + abs * 0.55})`;
    return `<div class="rounded-xl border border-gray-600 p-2" style="background:${color}">
      <div class="text-xs text-gray-100">${String(c.symbol).toUpperCase()}</div>
      <div class="text-sm font-semibold text-white">${v.toFixed(2)}%</div>
    </div>`;
  }).join("");
  document.getElementById("heatmap").innerHTML = html;
}

function renderCorrelation(list) {
  const html = list.slice(0, 8).map((x) => {
    const state = x.type === "high" ? "glow-bullish" : (x.type === "medium" ? "glow-neutral" : "glow-bearish");
    return `<div class="rounded-lg border border-gray-700 bg-gray-900 bg-opacity-60 px-3 py-2 ${state}">
      <div class="flex items-center justify-between">
        <span>${x.pair}</span><span class="text-cyan-300">${x.score}</span>
      </div>
    </div>`;
  }).join("");
  document.getElementById("correlationList").innerHTML = html;
}

function renderAlerts(items) {
  const html = items.slice(0, 7).map((a) => {
    const cls = a.state === "bullish" ? "glow-bullish" : (a.state === "bearish" ? "glow-bearish" : "glow-neutral");
    return `<div class="rounded-lg border border-gray-700 bg-gray-900 bg-opacity-60 p-2 ${cls}">
      <div class="flex items-center justify-between"><b class="text-gray-100">${a.symbol}</b><span class="capitalize text-xs text-gray-300">${a.state}</span></div>
      <div class="mt-1 text-xs text-gray-300">Volatilidad ${a.volatility} | Presion vol ${a.volume_pressure}</div>
    </div>`;
  }).join("");
  document.getElementById("alertPanel").innerHTML = html || `<div class="text-sm text-gray-400">Sin alertas fuera de rango.</div>`;
}

function updateMomentumChart(coins) {
  const labels = coins.slice(0, 10).map(c => String(c.symbol).toUpperCase());
  const data = coins.slice(0, 10).map(c => momentum(c.price_change_percentage_1h_in_currency || 0, c.price_change_percentage_24h_in_currency || 0, c.price_change_percentage_7d_in_currency || 0));
  const ctx = document.getElementById("momentumChart");
  if (!momentumChart) {
    momentumChart = new Chart(ctx, {
      type: "bar",
      data: { labels, datasets: [{ data, borderRadius: 8, backgroundColor: data.map(v => v >= 0 ? "rgba(16,185,129,0.55)" : "rgba(244,63,94,0.55)") }] },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { grid: { color: "rgba(148,163,184,0.15)" }, ticks: { color: "#b5c5e2" } }, x: { ticks: { color: "#c8d5ef" } } }
      }
    });
  } else {
    momentumChart.data.labels = labels;
    momentumChart.data.datasets[0].data = data;
    momentumChart.data.datasets[0].backgroundColor = data.map(v => v >= 0 ? "rgba(16,185,129,0.55)" : "rgba(244,63,94,0.55)");
    momentumChart.update("none");
  }
}

async function refreshDashboard() {
  const data = await fetchJson("api/dashboard.php");
  const coins = data.coins || [];
  const analysis = data.analysis || {};
  renderCoinsTable(coins);
  renderHeatmap(coins);
  renderCorrelation(analysis.correlations || []);
  renderAlerts(analysis.anomalies || []);
  updateMomentumChart(coins);

  const state = analysis.market_pulse?.market_state || "neutral";
  document.getElementById("marketState").textContent = state;
  const pill = document.getElementById("marketStatePill");
  if (pill) {
    pill.textContent = state;
    pill.classList.remove("badge-neutral", "badge-bullish", "badge-bearish");
    pill.classList.add(state === "bullish" ? "badge-bullish" : state === "bearish" ? "badge-bearish" : "badge-neutral");
  }
  document.getElementById("btcDom").textContent = `${analysis.market_pulse?.btc_dominance || 0}%`;
  document.getElementById("avg24h").textContent = `${analysis.market_pulse?.avg_24h || 0}%`;
  document.getElementById("anomalyCount").textContent = (analysis.anomalies || []).length;
  document.getElementById("lastUpdate").textContent = new Date(data.generated_at).toLocaleTimeString();
}

async function refreshInsights() {
  const data = await fetchJson("api/insights.php");
  const insight = data.insight || {};
  document.getElementById("insights").innerHTML = `
    <div class="mb-2 text-xs uppercase tracking-wider text-cyan-300">${insight.source || "local"}</div>
    <p class="mb-3">${insight.summary || "Sin datos de insight."}</p>
    <p class="text-xs text-gray-400">${insight.disclaimer || ""}</p>`;
}

async function boot() {
  try {
    await Promise.all([refreshDashboard(), refreshInsights()]);
  } catch (e) {
    document.getElementById("insights").textContent = "No fue posible cargar datos en este momento.";
  }
  setInterval(refreshDashboard, 30000);
  setInterval(refreshInsights, 45000);
}

document.addEventListener("DOMContentLoaded", boot);
