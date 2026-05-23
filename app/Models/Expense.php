<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'expense_date', 'value',
        'description', 'receipt_path', 'receipt_original_name', 'status',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'value'        => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reports()
    {
        return $this->belongsToMany(Report::class, 'report_expenses');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_path
            ? asset('storage/' . $this->receipt_path)
            : null;
    }

    public function getReceiptExtAttribute(): ?string
    {
        return $this->receipt_path
            ? strtolower(pathinfo($this->receipt_path, PATHINFO_EXTENSION))
            : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'available' => 'Disponível',
            'locked'    => 'Vinculada',
            'archived'  => 'Concluída',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available' => 'green',
            'locked'    => 'amber',
            'archived'  => 'gray',
            default     => 'gray',
        };
    }
}
