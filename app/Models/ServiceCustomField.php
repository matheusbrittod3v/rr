<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'parametros_campo',
    ];

    protected $casts = [
        'parametros_campo' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Accessor to get field name from parametros_campo
    public function getFieldNameAttribute()
    {
        $params = $this->parametros_campo;
        return $params['field_name'] ?? null;
    }

    // Accessor to get field type from parametros_campo
    public function getFieldTypeAttribute()
    {
        $params = $this->parametros_campo;
        return $params['field_type'] ?? null;
    }

    // Mutator to set field name and type in parametros_campo
    public function setFieldNameAttribute($value)
    {
        $params = $this->parametros_campo ?? [];
        $params['field_name'] = $value;
        $this->parametros_campo = $params;
    }

    // Mutator to set field type in parametros_campo
    public function setFieldTypeAttribute($value)
    {
        $params = $this->parametros_campo ?? [];
        $params['field_type'] = $value;
        $this->parametros_campo = $params;
    }
}