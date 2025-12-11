<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'caminho_imagem',
    ];

    protected static function booted()
    {
        static::created(function ($marca) {
            Cache::forget('all_services_with_relations');
        });

        static::updated(function ($marca) {
            Cache::forget('all_services_with_relations');
        });

        static::deleted(function ($marca) {
            Cache::forget('all_services_with_relations');
        });
    }
}