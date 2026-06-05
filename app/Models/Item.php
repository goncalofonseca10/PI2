<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'quantidade',
        'comprado',
        'kit_id',
        'categoria_id',
    ];

    protected $casts = [
        'comprado' => 'boolean',
        'quantidade' => 'integer',
    ];

    public function kit(){
        return $this->belongsTo(Kit::class);
    }

    public function categoria(){
        return $this->belongsTo(Categoria::class);
    }
}
