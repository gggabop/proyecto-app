<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{

    use HasFactory;

    protected $fillable = [
        'description_aud',
        'fk_id_user',
        'action_aud',
    ];
}
