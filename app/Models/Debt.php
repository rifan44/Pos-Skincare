<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'direction',
        'party_type',
        'party_name',
        'reference',
        'amount',
        'paid_amount',
        'due_date',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_amount' => 'integer',
            'due_date' => 'date',
        ];
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRemainingAmountAttribute(): int
    {
        return max(0, (int) $this->amount - (int) $this->paid_amount);
    }

    public function getDirectionLabelAttribute(): string
    {
        return $this->direction === 'payable' ? 'Utang' : 'Piutang';
    }
}
