<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite_res extends Model
{
    use HasFactory;
    protected $table = 'favorite_res';
    protected $fillable = [
        'fav_res_id',
        'res_id',
        'user_id',
        'status',
        
    ];
}
