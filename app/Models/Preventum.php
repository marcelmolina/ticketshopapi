<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Preventum
 * 
 * @property int $id
 * @property string $nombre
 * @property int $id_evento
 * @property float $porcentaje_descuento
 * @property \Carbon\Carbon $fecha_inicio
 * @property \Carbon\Carbon $fecha_fin
 * @property int $activo
 * 
 * @property \App\Models\Evento $evento
 * @property \Illuminate\Database\Eloquent\Collection $boleta_reservas
 * @property \Illuminate\Database\Eloquent\Collection $boletas_preimpresas
 * @property \Illuminate\Database\Eloquent\Collection $boletas_prevents
 * @property \Illuminate\Database\Eloquent\Collection $palco_prevents
 * @property \Illuminate\Database\Eloquent\Collection $palco_reservas
 *
 * @package App\Models
 */
class Preventum extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'id_evento' => 'int',
		'activo' => 'int'
	];


	protected $fillable = [
		'nombre',
		'id_evento',
		'id_evento_origen',
		'id_tribuna',
		'id_localidad',		
		'fecha_inicio',
		'fecha_fin',
		'hora_inicio',
		'hora_fin',
		'activo',
		'porcentaje_descuento_precio',
		'porcentaje_descuento_servicio'
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function evento_origen()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento_origen');
	}
	
	public function tribuna()
	{
		return $this->belongsTo(\App\Models\Tribuna::class, 'id_tribuna');
	}

	public function localidad()
	{
		return $this->belongsTo(\App\Models\Localidad::class, 'id_localidad');
	}

	public function boleta_reservas()
	{
		return $this->hasMany(\App\Models\BoletaReserva::class, 'id_preventa');
	}

	public function boletas_preimpresas()
	{
		return $this->hasMany(\App\Models\BoletasPreimpresa::class, 'id_preventa');
	}

	public function boletas_prevents()
	{
		return $this->hasMany(\App\Models\BoletasPrevent::class, 'id_preventa');
	}

	public function palco_prevents()
	{
		return $this->hasMany(\App\Models\PalcoPrevent::class, 'id_preventa');
	}

	public function palco_reservas()
	{
		return $this->hasMany(\App\Models\PalcoReserva::class, 'id_preventa');
	}

	public function precios_monedas()
	{
		return $this->hasMany(\App\Models\PreciosMonedas::class, 'id_preventa');
	}
}

