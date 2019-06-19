<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DetalleVent
 * 
 * @property int $id
 * @property int $id_venta
 * @property int $id_boleta_evento
 * @property int $id_palco_evento
 * @property float $precio_venta
 * @property float $precio_servicio
 * @property float $impuesto
 * @property bool $status
 * @property int $id_descuento
 * @property float $monto_domicilio
 * @property string $codigo_moneda
 * 
 * @property \App\Models\Vent $vent
 * @property \App\Models\Moneda $moneda
 * @property \App\Models\BoletaEvento $boleta_evento
 * @property \App\Models\PalcoEvento $palco_evento
 * @property \App\Models\DescuentoEvento $descuento_evento
 * @property \Illuminate\Database\Eloquent\Collection $devolucions
 * @property \Illuminate\Database\Eloquent\Collection $domicilios
 *
 * @package App\Models
 */
class DetalleVent extends Eloquent
{
	protected $table = 'detalle_vent';
	public $timestamps = false;

	protected $casts = [
		'id_venta' => 'int',
		'id_boleta_evento' => 'int',
		'id_palco_evento' => 'int',
		'precio_venta' => 'float',
		'precio_servicio' => 'float',
		'impuesto' => 'float',
		'status' => 'bool',
		'id_descuento' => 'int',
		'monto_domicilio' => 'float'
	];

	protected $fillable = [
		'id_venta',
		'id_boleta_evento',
		'id_palco_evento',
		'precio_venta',
		'precio_servicio',
		'impuesto',
		'status',
		'id_descuento',
		'monto_domicilio',
		'codigo_moneda'
	];

	public function vent()
	{
		return $this->belongsTo(\App\Models\Vent::class, 'id_venta');
	}

	public function moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda');
	}

	public function boleta_evento()
	{
		return $this->belongsTo(\App\Models\BoletaEvento::class, 'id_boleta_evento');
	}

	public function palco_evento()
	{
		return $this->belongsTo(\App\Models\PalcoEvento::class, 'id_palco_evento');
	}

	public function descuento_evento()
	{
		return $this->belongsTo(\App\Models\DescuentoEvento::class, 'id_descuento');
	}

	public function devolucions()
	{
		return $this->hasMany(\App\Models\Devolucion::class, 'id_palco_evento', 'id_palco_evento');
	}

	public function domicilios()
	{
		return $this->hasMany(\App\Models\Domicilio::class, 'id_detalle_venta');
	}
}
