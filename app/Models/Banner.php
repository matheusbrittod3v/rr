<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'imagens',
        'tempo_rotacao',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'imagens' => 'array',
        'ativo' => 'boolean',
        'tempo_rotacao' => 'integer',
        'ordem' => 'integer',
    ];

    /**
     * Invalidar cache quando banner for modificado
     */
    protected static function booted()
    {
        static::created(function ($banner) {
            Cache::forget('active_banners');
        });

        static::updated(function ($banner) {
            Cache::forget('active_banners');
        });

        static::deleted(function ($banner) {
            Cache::forget('active_banners');
        });
    }

    /**
     * Escopo para banners ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Escopo para ordenar por ordem
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem', 'asc');
    }

    /**
     * Retorna o tempo de rotação em segundos
     */
    public function getTempoRotacaoSegundosAttribute(): float
    {
        return $this->tempo_rotacao / 1000;
    }

    /**
     * Retorna a quantidade de imagens
     */
    public function getQuantidadeImagensAttribute(): int
    {
        return is_array($this->imagens) ? count($this->imagens) : 0;
    }
}
