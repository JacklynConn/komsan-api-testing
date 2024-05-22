<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant_Rating extends Model
{
    use HasFactory;
    protected $table = "res_rating";

    protected $fillable = [
        'res_rating_id',
        'user_id',
        'res_id',
        'rating',
        'status',
        'created_at',
        'updated_at',
    ];
}
