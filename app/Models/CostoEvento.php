<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CostoEvento
 * 
 * @property int $id
 * @property int $id_evento
 * @property int $id_tipo_costo
 * @property string $descripcion
 * @property float $valor
 * @property string $codigo_moneda
 * 
 * @property \App\Models\TipoCupon $evento
 * @property \App\Models\Cuponera $tipo_costo
 * @property \App\Models\Cuponera $codigo_moneda
 *
 * @package App\Models
 */
class CostoEvento extends Eloquent
{
    protected $table = 'costo_evento';
	public $timestamps = false;

	protected $casts = [
		'id_evento' => 'int',
		'id_tipo_costo' => 'int',
		'valor' => 'float'		
	];

	protected $fillable = [
		'id_evento',
		'id_tipo_costo',
		'descripcion',
		'valor',
		'codigo_moneda'
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function tipo_costo()
	{
		return $this->belongsTo(\App\Models\TipoCosto::class, 'id_tipo_costo');
	}

	public function codigo_moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda');
	}
}
