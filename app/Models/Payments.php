<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $fillable = [
        'fk_id_loan',
        'amount_payment',
        'date_payment',
        'serial_payment'
    ];

}
