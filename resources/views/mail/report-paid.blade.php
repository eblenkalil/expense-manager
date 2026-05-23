<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reembolso Confirmado</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 32px 16px; color: #334155; }
  .card { background: #fff; border-radius: 12px; max-width: 560px; margin: 0 auto; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
  .header { background: #16a34a; color: #fff; padding: 28px 32px; }
  .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
  .header p  { margin: 6px 0 0; opacity: .8; font-size: 13px; }
  .body { padding: 28px 32px; }
  .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
  .info-row:last-child { border: none; }
  .label { color: #94a3b8; }
  .value { font-weight: 600; color: #0f172a; }
  .total { font-size: 22px; color: #16a34a; }
  .btn { display: inline-block; margin-top: 24px; padding: 12px 28px; background: #16a34a; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; }
  .footer { padding: 16px 32px; background: #f8fafc; font-size: 12px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
<div class="card">
  <div class="header">
    <h1>✓ Reembolso confirmado!</h1>
    <p>Seu relatório foi pago pelo administrativo</p>
  </div>
  <div class="body">
    <p style="margin-top:0">Olá, <strong>{{ $report->user->name }}</strong>! Seu reembolso de despesas foi processado com sucesso.</p>
    <div style="background:#f0fdf4;border-radius:8px;padding:16px 20px;margin:20px 0;border:1px solid #bbf7d0">
      <div class="info-row"><span class="label">Protocolo</span><span class="value">{{ $report->protocol_number }}</span></div>
      <div class="info-row"><span class="label">Título</span><span class="value">{{ $report->title }}</span></div>
      <div class="info-row"><span class="label">Pago em</span><span class="value">{{ $report->paid_at->format('d/m/Y H:i') }}</span></div>
      <div class="info-row"><span class="label">Valor reembolsado</span><span class="value total">R$ {{ number_format($report->total_value, 2, ',', '.') }}</span></div>
    </div>
    <a href="{{ route('reports.show', $report) }}" class="btn">Ver Comprovante →</a>
  </div>
  <div class="footer">Este e-mail foi enviado automaticamente pelo sistema de Gestão de Despesas.</div>
</div>
</body>
</html>
