# Guía de uso de la plataforma Eye

## Aviso legal (lectura obligatoria)

**Los patrones detectados por IA son únicamente informativos y no constituyen asesoramiento financiero ni garantía predictiva.**

Esta herramienta ayuda a **entender comportamiento de mercado**, no a asegurar resultados.

---

## Objetivo de la plataforma

Eye está diseñada para que un usuario pueda:

- Ver el estado general del mercado cripto en tiempo real.
- Detectar relaciones entre activos (sincronías y divergencias).
- Identificar anomalías de volatilidad o volumen.
- Leer explicaciones automáticas en lenguaje natural sobre patrones detectados.

La idea es apoyar la toma de decisiones con contexto, no reemplazar el análisis de riesgo personal.

---

## Cómo está organizada la interfaz

## 1) Encabezado principal (Hero)

Incluye:

- `LIVE`: confirma que el panel está en actualización dinámica.
- `Estado de mercado` (pill bullish/bearish/neutral): sesgo agregado.
- `Actualización`: hora de última lectura de datos.

### ¿En qué fijarse si vas a operar?

- Si la hora de actualización está reciente.
- Si el estado general cambia con frecuencia (mercado inestable).
- Si el estado agregado coincide o contradice tu hipótesis previa.

---

## 2) Tarjetas KPI rápidas

### `Estado de mercado`

Resume el sesgo calculado por el motor (promedio de variaciones y contexto general).

### `BTC Dominancia`

Porcentaje de peso de Bitcoin respecto al total de market cap observado.

- Dominancia alta: mercado más concentrado en BTC.
- Dominancia bajando: rotación posible a altcoins (no garantía).

### `Promedio 24h`

Media de variación de activos monitorizados en 24 horas.

- Positivo fuerte: contexto de impulso general.
- Negativo fuerte: contexto de presión bajista amplia.

### `Anomalías activas`

Cantidad de eventos fuera de rango normal detectados por el motor.

### ¿En qué fijarse?

- No leer un KPI aislado: combinar `Estado` + `Promedio 24h` + `Anomalías`.
- Si hay muchas anomalías con promedio neutro, puede haber mercado errático.

---

## 3) Tendencias de momentum (gráfico principal)

Gráfico de barras con momentum por activo (mezcla de 1h, 24h y 7d).

### ¿Cómo interpretarlo?

- Barras positivas y crecientes: aceleración relativa.
- Barras negativas y profundas: pérdida de fuerza.
- Cambios bruscos de signo: posible transición de régimen.

### ¿En qué fijarse?

- Coherencia entre momentum y volumen.
- Si varios activos cambian en la misma dirección al mismo tiempo.

---

## 4) Mapa de correlación

Muestra pares con mayor similitud de comportamiento reciente.

- `High`: movimientos muy parecidos.
- `Medium`: relación moderada.
- `Low`: relación débil.

### ¿Para qué sirve?

- Detectar si el mercado se mueve “en bloque”.
- Evitar sobreexposición involuntaria a activos muy correlacionados.

### ¿En qué fijarse?

- Si muchos pares aparecen en `high`, hay mayor riesgo sistémico.
- Si un activo deja de correlacionar, puede estar en fase particular.

---

## 5) Dashboard principal (tabla de activos)

Campos:

- `Precio`
- `1h`, `24h`, `7d`
- `Cap` (capitalización)
- `Vol` (volumen)
- `Momentum`
- `Mini chart` (microtendencia visual)

### Lectura práctica por columna

- **1h**: pulso muy corto plazo.
- **24h**: dirección intradía/diaria.
- **7d**: tendencia semanal.
- **Cap**: tamaño relativo y robustez de mercado.
- **Vol**: interés/actividad; valida o debilita movimientos.
- **Mini chart**: forma visual rápida del comportamiento.

### ¿En qué fijarse según enfoque?

- **Enfoque conservador**: activos de mayor cap + volumen estable + menor anomalía.
- **Enfoque táctico/corto plazo**: cambios de 1h con confirmación en volumen y momentum.
- **Enfoque de rotación**: comparar quién acelera y quién pierde fuerza en 24h/7d.

> No implica recomendación de compra/venta; solo marco de lectura.

---

## 6) Alertas visuales

Estados:

- `bullish`
- `bearish`
- `neutral`

Incluye métricas como volatilidad y presión de volumen.

### ¿Para qué sirve?

- Señalar activos con comportamiento fuera de normalidad estadística.

### ¿En qué fijarse?

- Una alerta aislada no confirma tendencia.
- Múltiples alertas en misma dirección pueden sugerir episodio de mercado relevante.
- Priorizar alertas acompañadas de correlación y momentum coherentes.

---

## 7) Heatmap de variación 24h

Vista de calor de rendimiento diario por activo.

### ¿Para qué sirve?

- Comprender de un vistazo si el movimiento es amplio o puntual.

### ¿En qué fijarse?

- Mayoría verde/rojo intenso = régimen dominante.
- Mezcla dispersa = mercado selectivo o sin dirección clara.

---

## 8) Insights IA (resumen narrativo)

Texto automático con:

- Resumen de mercado.
- Patrones detectados.
- Relaciones extrañas.
- Posibles explicaciones macro.

### ¿Cómo usarlo bien?

- Como capa de contexto, no como “señal final”.
- Contrastar su narrativa con datos cuantitativos del panel.
- Verificar si el insight cambia en cada actualización.

---

## Flujo recomendado de análisis (paso a paso)

1. Verifica que `LIVE` y `Actualización` estén correctos.
2. Revisa KPIs: Estado, Promedio 24h, Dominancia BTC, Anomalías.
3. Observa momentum para identificar aceleraciones/desaceleraciones.
4. Revisa correlación para entender riesgo de movimiento conjunto.
5. Baja a la tabla para validar con volumen, cap y mini chart.
6. Consulta alertas para detectar comportamiento atípico.
7. Usa heatmap para confirmar amplitud del movimiento.
8. Lee Insights IA y contrástalo con lo anterior.

---

## Errores de interpretación a evitar

- Operar por una sola métrica.
- Confundir correlación con causalidad.
- Tomar una anomalía como certeza direccional.
- Ignorar contexto macro (noticias, liquidez, eventos regulatorios).
- Considerar el texto de IA como recomendación.

---

## Buenas prácticas de riesgo (generales)

- Definir de antemano tamaño de posición y límite de pérdida.
- No sobreexponerse a activos altamente correlacionados.
- Evitar decisiones impulsivas por velas de corto plazo.
- Revaluar hipótesis cuando cambian Estado/Anomalías/Correlación.

---

## Recordatorio final

La herramienta Eye es un **sistema de inteligencia de mercado**, no un sistema de señales garantizadas.

**Los patrones detectados por IA son únicamente informativos y no constituyen asesoramiento financiero ni garantía predictiva.**
