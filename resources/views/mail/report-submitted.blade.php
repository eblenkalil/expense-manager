<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Novo Relatório</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 32px 16px; color: #334155; }
  .card { background: #fff; border-radius: 12px; max-width: 560px; margin: 0 auto; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
  .header { background: #1a56db; color: #fff; padding: 28px 32px; }
  .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
  .header p  { margin: 6px 0 0; opacity: .8; font-size: 13px; }
  .body { padding: 28px 32px; }
  .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
  .info-row:last-child { border: none; }
  .label { color: #94a3b8; }
  .value { font-weight: 600; color: #0f172a; }
  .total { font-size: 22px; color: #1a56db; }
  .btn { display: inline-block; margin-top: 24px; padding: 12px 28px; background: #1a56db; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; }
  .footer { padding: 16px 32px; background: #f8fafc; font-size: 12px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
<div class="card">
  <div class="header">
    <h1>Novo relatório de despesas</h1>
    <p>Aguardando confirmação de pagamento</p>
  </div>
  <div class="body">
    <p style="margin-top:0">Olá! Um colaborador entregou um relatório de despesas que aguarda pagamento.</p>
    <div style="background:#f8fafc;border-radius:8px;padding:16px 20px;margin:20px 0">
      <div class="info-row"><span class="label">Protocolo</span><span class="value">{{ $report->protocol_number }}</span></div>
      <div class="info-row"><span class="label">Colaborador</span><span class="value">{{ $report->user->name }}</span></div>
      <div class="info-row"><span class="label">Título</span><span class="value">{{ $report->title }}</span></div>
      <div class="info-row"><span class="label">Despesas</span><span class="value">{{ $report->expenses->count() }} item(s)</span></div>
      <div class="info-row"><span class="label">Entregue em</span><span class="value">{{ $report->submitted_at->format('d/m/Y H:i') }}</span></div>
      <div class="info-row"><span class="label">Total</span><span class="value total">R$ {{ number_format($report->total_value, 2, ',', '.') }}</span></div>
    </div>
    <a href="{{ route('reports.show', $report) }}" class="btn">Ver Relatório →</a>
  </div>
  <div class="footer">Este e-mail foi enviado automaticamente pelo sistema de Gestão de Despesas.</div>
</div>
</body>
</html>
