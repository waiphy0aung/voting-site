<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miss extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'name',
        'slug',
        'height',
        'weight',
        'bust',
        'waist',
        'hips',
        'age',
        'location',
        'hobby'
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class,"missId","id");
    }
}
