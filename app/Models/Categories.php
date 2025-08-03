<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

     protected $fillable = [
        'nom',
        'slug',
    ];


    public function livres() {
    return $this->hasMany(Livres::class);
}

}
