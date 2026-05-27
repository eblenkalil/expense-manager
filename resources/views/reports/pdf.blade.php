<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>{{ $report->protocol_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 12px;
    color: #334155;
    padding: 40px;
    background: #fff;
  }

  /* Header */
  .header {
    border-bottom: 3px solid #1a56db;
    padding-bottom: 20px;
    margin-bottom: 28px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
  }
  .header-left h1 {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
  }
  .header-left p {
    font-size: 12px;
    color: #64748b;
  }
  .header-right {
    text-align: right;
  }
  .protocol {
    font-size: 14px;
    font-weight: 700;
    color: #1a56db;
    background: #eff6ff;
    padding: 4px 10px;
    border-radius: 4px;
    display: inline-block;
    margin-bottom: 6px;
  }
  .header-right p {
    font-size: 11px;
    color: #94a3b8;
    line-height: 1.6;
  }

  /* Info grid */
  .info-grid {
    display: flex;
    gap: 16px;
    margin-bottom: 28px;
  }
  .info-card {
    flex: 1;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 16px;
  }
  .info-card .label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #94a3b8;
    font-weight: 600;
    margin-bottom: 4px;
  }
  .info-card .value {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
  }

  /* Table */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
  }
  thead tr {
    background: #f8fafc;
  }
  th {
    padding: 9px 12px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #94a3b8;
    border-bottom: 2px solid #e2e8f0;
  }
  th.right  { text-align: right; }
  th.center { text-align: center; }
  td.center { text-align: center; }
  td {
    padding: 10px 12px;
    font-size: 12px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  td.right { text-align: right; }
  td.mono  { font-size: 11px; color: #64748b; }
  .category-badge {
    background: #eff6ff;
    color: #1a56db;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
  }

  /* Total row */
  .total-row td {
    border-top: 2px solid #e2e8f0;
    border-bottom: none;
    padding-top: 12px;
    padding-bottom: 12px;
    background: #f8fafc;
    font-weight: 700;
    font-size: 13px;
    color: #0f172a;
  }
  .total-row .total-value {
    font-size: 18px;
    color: #1a56db;
  }

  /* Notes */
  .notes {
    margin-top: 24px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 16px;
  }
  .notes .label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #94a3b8;
    font-weight: 600;
    margin-bottom: 6px;
  }

  /* PIX */
  .pix-section {
    margin-top: 20px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 14px 16px;
  }
  .pix-section .label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #15803d;
    font-weight: 700;
    margin-bottom: 6px;
  }
  .pix-section .value {
    font-size: 14px;
    font-weight: 700;
    color: #166534;
  }

  /* Status */
  .status-badge {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    display: inline-block;
  }
  .status-draft     { background: #f1f5f9; color: #475569; }
  .status-submitted { background: #fffbeb; color: #b45309; }
  .status-paid      { background: #f0fdf4; color: #15803d; }

  /* Footer */
  .footer {
    margin-top: 40px;
    padding-top: 16px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: #94a3b8;
  }
</style>
</head>
<body>

  <div class="header">
    <div class="header-left">
      <img src="{{ public_path('images/logo.png') }}" alt="Veloce Tech" style="max-width:120px;height:auto;margin-bottom:6px;display:block;">
      <h1>Relatório de Despesas</h1>
      <p>{{ $report->user->name }} &bull; {{ $report->user->email }}</p>
    </div>
    <div class="header-right">
      <div class="protocol">{{ $report->protocol_number }}</div>
      <p>
        Criado em: {{ $report->created_at->format('d/m/Y') }}<br>
        @if($report->submitted_at)
          Entregue em: {{ $report->submitted_at->format('d/m/Y') }}<br>
        @endif
        @if($report->paid_at)
          Pago em: {{ $report->paid_at->format('d/m/Y') }}<br>
        @endif
        Status:
        <span class="status-badge status-{{ $report->status }}">
          {{ $report->status_label }}
        </span>
      </p>
    </div>
  </div>

  <div class="info-grid">
    <div class="info-card">
      <div class="label">Título</div>
      <div class="value">{{ $report->title }}</div>
    </div>
    <div class="info-card">
      <div class="label">Total de itens</div>
      <div class="value">{{ $report->expenses->count() }} despesa(s)</div>
    </div>
    <div class="info-card">
      <div class="label">Valor total</div>
      <div class="value" style="color:#1a56db;font-size:16px">
        R$ {{ number_format($report->total_value, 2, ',', '.') }}
      </div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Data</th>
        <th>Descrição</th>
        <th>Categoria</th>
        <th class="right">Valor</th>
        <th class="center">Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($report->expenses as $e)
        <tr>
          <td class="mono">{{ $e->expense_date->format('d/m/Y') }}</td>
          <td>{{ $e->description ?: '—' }}</td>
          <td><span class="category-badge">{{ $e->category?->name ?? '—' }}</span></td>
          <td class="right">R$ {{ number_format($e->value, 2, ',', '.') }}</td>
          <td class="center">
            <span class="status-badge {{ $e->status === 'archived' ? 'status-paid' : 'status-submitted' }}">
              {{ $e->status_label }}
            </span>
          </td>
        </tr>
      @endforeach
      <tr class="total-row">
        <td colspan="3">TOTAL GERAL</td>
        <td class="right total-value">R$ {{ number_format($report->total_value, 2, ',', '.') }}</td>
        <td></td>
      </tr>
    </tbody>
  </table>

  @if($report->pix_key)
    <div class="pix-section">
      <div class="label">Dados para Pagamento</div>
      <div class="value">Chave PIX: {{ $report->pix_key }}</div>
    </div>
  @endif

  @if($report->notes)
    <div class="notes">
      <div class="label">Observações</div>
      <p>{{ $report->notes }}</p>
    </div>
  @endif

  <div class="footer">
    <span>Gestão de Despesas &bull; Gerado em {{ now()->format('d/m/Y H:i') }}</span>
    <span>{{ $report->protocol_number }}</span>
  </div>

</body>
</html>
