<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Localidad
 * 
 * @property int $id
 * @property string $nombre
 * @property int $id_tribuna
 * @property string $puerta_acceso
 * @property string $ruta
 * @property string $url_imagen
 * 
 * @property \App\Models\Tribuna $tribuna
 * @property \Illuminate\Database\Eloquent\Collection $detalle_descuentos
 * @property \Illuminate\Database\Eloquent\Collection $filas
 * @property \Illuminate\Database\Eloquent\Collection $palcos
 * @property \Illuminate\Database\Eloquent\Collection $puestos
 *
 * @package App\Models
 */
class Localidad extends Eloquent
{
	protected $table = 'localidad';
	public $timestamps = false;

	protected $casts = [
		'id_tribuna' => 'int',
		'aforo' => 'int',
		'silleteria' => 'int'
	];

	protected $fillable = [
		'nombre',
		'id_tribuna',
		'puerta_acceso',
		'ruta',
		'url_imagen',
		'silleteria',
		'aforo',
		'palco',
		'puestosxpalco',
	];

	public function tribuna()
	{
		return $this->belongsTo(\App\Models\Tribuna::class, 'id_tribuna');
	}

	public function detalle_descuentos()
	{
		return $this->hasMany(\App\Models\DetalleDescuento::class, 'id_localidad');
	}

	public function filas()
	{
		return $this->hasMany(\App\Models\Fila::class, 'id_localidad');
	}

	public function palcos()
	{
		return $this->hasMany(\App\Models\Palco::class, 'id_localidad');
	}

	public function puestos()
	{
		return $this->hasMany(\App\Models\Puesto::class, 'id_localidad');
	}

	public function localidad_evento()
	{
		return $this->hasMany(\App\Models\LocalidadEvento::class, 'id_localidad');
	}
}

