<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DetalleDescuento
 * 
 * @property int $id
 * @property int $id_descuento
 * @property int $id_tribuna
 * @property int $id_localidad
 * @property int $id_boleta_evento
 * @property int $id_palco_evento
 * @property float $porcentaje
 * @property float $monto
 * @property float $status
 * @property string $codigo_moneda
 * 
 * @property \App\Models\DescuentoEvento $descuento_evento
 * @property \App\Models\Tribuna $tribuna
 * @property \App\Models\Moneda $moneda
 * @property \App\Models\Localidad $localidad
 * @property \App\Models\BoletaEvento $boleta_evento
 * @property \App\Models\PalcoEvento $palco_evento
 *
 * @package App\Models
 */
class DetalleDescuento extends Eloquent
{
	protected $table = 'detalle_descuento';
	public $timestamps = false;

	protected $casts = [
		'id_descuento' => 'int',
		'id_tribuna' => 'int',
		'id_localidad' => 'int',
		'id_boleta_evento' => 'int',
		'id_palco_evento' => 'int',
		'porcentaje' => 'float',
		'monto' => 'float',
		'status' => 'float'
	];

	protected $fillable = [
		'id_descuento',
		'id_tribuna',
		'id_localidad',
		'id_boleta_evento',
		'id_palco_evento',
		'porcentaje',
		'monto',
		'status',
		'codigo_moneda'
	];

	public function descuento_evento()
	{
		return $this->belongsTo(\App\Models\DescuentoEvento::class, 'id_descuento');
	}

	public function tribuna()
	{
		return $this->belongsTo(\App\Models\Tribuna::class, 'id_tribuna');
	}

	public function moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda');
	}

	public function localidad()
	{
		return $this->belongsTo(\App\Models\Localidad::class, 'id_localidad');
	}

	public function boleta_evento()
	{
		return $this->belongsTo(\App\Models\BoletaEvento::class, 'id_boleta_evento');
	}

	public function palco_evento()
	{
		return $this->belongsTo(\App\Models\PalcoEvento::class, 'id_palco_evento');
	}
}
