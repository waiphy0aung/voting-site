<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile',
        'name',
        'number_of_vote',
        'height',
        'weight',
        'age',
        'phone',
        'location',
        'hobby'
    ];

}
