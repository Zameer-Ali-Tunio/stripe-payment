<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'status'];
    public const STATUS_PENDING =  'pending';
    public const STATUS_RESOLVED =  'resolved';
    public const STATUS_CANCELLED =  'cancelled';

    function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
