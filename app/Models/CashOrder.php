<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOrder extends Model
{
    protected $fillable = [
        'fk_customer_id',
        'amount_cash_order',
        'status_cash_order',
    ];
    use HasFactory;
}
