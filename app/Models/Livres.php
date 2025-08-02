<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livres extends Model
{
    use HasFactory;

     protected $fillable = [
        'titre', 'description', 'date_sortie',
        
    ];

    public function categorie() {
    return $this->belongsTo(Categories::class);
}

public function user() {
    return $this->belongsTo(User::class);
}

}
