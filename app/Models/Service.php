<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'marca_id',
        'photo_patch',
        'nome_servico',
        'descricao',
    ];

    protected static function booted()
    {
        static::created(function ($service) {
            Cache::forget('all_services_with_relations');
        });

        static::updated(function ($service) {
            Cache::forget('all_services_with_relations');
        });

        static::deleted(function ($service) {
            Cache::forget('all_services_with_relations');
        });
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function customFields()
    {
        return $this->hasMany(ServiceCustomField::class, 'service_id');
    }
}