<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'protocol_number', 'title', 'total_value',
        'notes', 'pix_key', 'status', 'payment_receipt_path', 'payment_receipt_name',
        'submitted_at', 'paid_at', 'rejection_reason', 'rejected_at',
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
        'submitted_at' => 'datetime',
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenses()
    {
        return $this->belongsToMany(Expense::class, 'report_expenses')
            ->with('category')
            ->orderBy('expense_date');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Rascunho',
            'submitted' => 'Pendente Pagamento',
            'paid' => 'Pago e Concluído',
            'rejected' => 'Reprovado',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'submitted' => 'amber',
            'paid' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getPaymentReceiptUrlAttribute(): ?string
    {
        return $this->payment_receipt_path
            ? asset('storage/'.$this->payment_receipt_path)
            : null;
    }
}
