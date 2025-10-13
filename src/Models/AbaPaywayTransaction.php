<?php

namespace SourngSeng\AbaPayway\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbaPaywayTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'merchant_id',
        'amount',
        'currency',
        'payment_option',
        'customer_email',
        'customer_phone',
        'customer_name',
        'items',
        'status',
        'payment_request',
        'payment_response',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_request' => 'array',
        'payment_response' => 'array',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('aba-payway.transaction_table', 'aba_payway_transactions');
        parent::__construct($attributes);
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction has failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * Scope for successful transactions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}