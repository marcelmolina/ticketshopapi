<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PalcoEvento
 * 
 * @property int $id
 * @property int $id_evento
 * @property int $id_palco
 * @property float $precio_venta
 * @property float $precio_servicio
 * @property float $impuesto
 * @property int $status
 * @property string $codigo_moneda
 * 
 * @property \App\Models\Evento $evento
 * @property \App\Models\Palco $palco
 * @property \App\Models\Moneda $moneda
 * @property \Illuminate\Database\Eloquent\Collection $detalle_descuentos
 * @property \Illuminate\Database\Eloquent\Collection $detalle_vents
 * @property \Illuminate\Database\Eloquent\Collection $detalle_venta_temporadas
 * @property \App\Models\PalcoPreimpreso $palco_preimpreso
 * @property \App\Models\PalcoPrevent $palco_prevent
 * @property \App\Models\PalcoReserva $palco_reserva
 * @property \Illuminate\Database\Eloquent\Collection $puestos_palco_eventos
 *
 * @package App\Models
 */
class PalcoEvento extends Eloquent
{
	protected $table = 'palco_evento';
	public $timestamps = false;

	protected $casts = [
		'id_evento' => 'int',
		'id_palco' => 'int',
		'precio_venta' => 'float',
		'precio_servicio' => 'float',
		'impuesto' => 'float',
		'status' => 'int'
	];

	protected $fillable = [
		'id_evento',
		'id_palco',
		'precio_venta',
		'precio_servicio',
		'impuesto',
		'status',
		'codigo_moneda'
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda');
	}

	public function palco()
	{
		return $this->belongsTo(\App\Models\Palco::class, 'id_palco');
	}

	public function detalle_descuentos()
	{
		return $this->hasMany(\App\Models\DetalleDescuento::class, 'id_palco_evento');
	}

	public function detalle_vents()
	{
		return $this->hasMany(\App\Models\DetalleVent::class, 'id_palco_evento');
	}

	public function detalle_venta_temporadas()
	{
		return $this->hasMany(\App\Models\DetalleVentaTemporada::class, 'id_palco_evento');
	}

	public function palco_preimpreso()
	{
		return $this->hasOne(\App\Models\PalcoPreimpreso::class, 'id_palco_evento');
	}

	public function palco_prevent()
	{
		return $this->hasOne(\App\Models\PalcoPrevent::class, 'id_palco_evento');
	}

	public function palco_reserva()
	{
		return $this->hasOne(\App\Models\PalcoReserva::class, 'id_palco_evento');
	}

	public function puestos_palco_eventos()
	{
		return $this->hasMany(\App\Models\PuestosPalcoEvento::class, 'id_palco_evento');
	}
}
