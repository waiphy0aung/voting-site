<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = ['missId','userId','categoryId'];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function miss(){
        return $this->belongsTo(Miss::class);
    }
}
