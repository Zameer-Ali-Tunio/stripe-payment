<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'amount',
        'due_date',
        'status',
    ];
    public const STATUS_PENDING  = 'Pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_DISPUTED = 'disputed';
    public const STATUS_REFUNDED = 'refunded';

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function dispute()
    {
        return $this->hasOne(Dispute::class);
    }

    function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
