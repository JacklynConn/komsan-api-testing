<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceRating extends Model
{
    use HasFactory;

    protected $primaryKey = 'place_rating_id';

    protected $fillable = [
        'place_id',
        'user_id',
        'rating',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
