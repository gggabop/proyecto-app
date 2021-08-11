<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{
    use HasFactory;

    protected $fillable = [
        "fk_id_cliente",
        "fk_id_cashOrder",
        "status_loan",
        "amount_loan",
        "amount_rest_loan",
        "debt_loan",
        "date_start_loan",
        "date_pay_loan",
        "interest_rate_loan"
    ];

}
