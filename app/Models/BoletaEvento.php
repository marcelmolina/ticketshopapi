<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BoletaEvento
 * 
 * @property int $id
 * @property int $id_evento
 * @property int $id_puesto
 * @property float $precio_venta
 * @property float $precio_servicio
 * @property float $impuesto
 * @property int $status
 * @property string $codigo
 * 
 * @property \App\Models\Evento $evento
 * @property \App\Models\Puesto $puesto
 * @property \App\Models\BoletaReserva $boleta_reserva
 * @property \App\Models\BoletasPreimpresa $boletas_preimpresa
 * @property \App\Models\BoletasPrevent $boletas_prevent
 * @property \Illuminate\Database\Eloquent\Collection $detalle_descuentos
 * @property \Illuminate\Database\Eloquent\Collection $detalle_vents
 * @property \Illuminate\Database\Eloquent\Collection $detalle_venta_temporadas
 *
 * @package App\Models
 */
class BoletaEvento extends Eloquent
{
	protected $table = 'boleta_evento';
	public $timestamps = false;

	protected $casts = [
		'id_evento' => 'int',
		'id_puesto' => 'int',
		'precio_venta' => 'float',
		'precio_servicio' => 'float',
		'impuesto' => 'float',
		'status' => 'int'
	];

	protected $fillable = [
		'id_evento',
		'id_puesto',
		'precio_venta',
		'precio_servicio',
		'impuesto',
		'status',
		'codigo'
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function puesto()
	{
		return $this->belongsTo(\App\Models\Puesto::class, 'id_puesto');
	}

	public function boleta_reserva()
	{
		return $this->hasOne(\App\Models\BoletaReserva::class, 'id_boleta');
	}

	public function boletas_preimpresa()
	{
		return $this->hasOne(\App\Models\BoletasPreimpresa::class, 'id_boleta');
	}

	public function boletas_prevent()
	{
		return $this->hasOne(\App\Models\BoletasPrevent::class, 'id_boleta');
	}

	public function detalle_descuentos()
	{
		return $this->hasMany(\App\Models\DetalleDescuento::class, 'id_boleta_evento');
	}

	public function detalle_vents()
	{
		return $this->hasMany(\App\Models\DetalleVent::class, 'id_boleta_evento');
	}

	public function detalle_venta_temporadas()
	{
		return $this->hasMany(\App\Models\DetalleVentaTemporada::class, 'id_boleta_evento');
	}
}
