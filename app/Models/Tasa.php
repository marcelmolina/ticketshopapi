<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Tasa
 * 
 * @property int $id
 * @property string $codigo_moneda_alta
 * @property string $codigo_moneda_baja
 * @property float $valor
 * @property \Carbon\Carbon $fecha_hora
 * @property int $activo
 * 
 * @property \Illuminate\Database\Eloquent\Collection $moneda_alta
 * @property \Illuminate\Database\Eloquent\Collection $moneda_baja
 * @package App\Models
 */
class Tasa extends Eloquent
{
    protected $table = 'tasa';
    public $timestamps = false;

	protected $casts = [
		'valor' => 'float',
		'activo' => 'int'
	];

	protected $dates = [
		'fecha_hora'
	];

	protected $fillable = [
		'codigo_moneda_alta',
		'codigo_moneda_baja',
		'valor',
		'fecha_hora',
		'activo'
	];

	public function moneda_alta()
	{
		return $this->hasMany(\App\Models\Moneda::class, 'codigo_moneda', 'codigo_moneda_alta');
	}

	public function moneda_baja()
	{
		return $this->hasMany(\App\Models\Moneda::class, 'codigo_moneda', 'codigo_moneda_baja');
	}



}
