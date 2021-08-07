<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $fillable = [
        "name_customer",
        "cedula_customer",
        "address_work_customer",
        "address_home_customer",
        "extra_address_customer",
        "cellphone_customer",
        "extra_cellphone_customer",
    ];
}
