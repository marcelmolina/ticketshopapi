<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PalcoReserva
 * 
 * @property int $id_palco_evento
 * @property int $id_preventa
 * @property float $abono
 * @property float $precio_venta
 * @property float $precio_servicio
 * @property float $impuesto
 * @property float $status
 * @property string $email_usuario
 * @property int $id_punto_venta
 * @property string $identificacion
 * @property string $razon_nombre
 * @property string $telefono
 * @property string $direccion
 * @property string $email
 * @property string $email_referido
 * @property string $codigo_moneda
 * 
 * @property \App\Models\PalcoEvento $palco_evento
 * @property \App\Models\Preventum $preventum
 * @property \App\Models\Moneda $moneda
 * @property \App\Models\Usuario $usuario
 * @property \App\Models\PuntoVentum $punto_ventum
 * @property \Illuminate\Database\Eloquent\Collection $abono_reservas
 *
 * @package App\Models
 */
class PalcoReserva extends Eloquent
{
	protected $table = 'palco_reserva';
	protected $primaryKey = 'id_palco_evento';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_palco_evento' => 'int',
		'id_preventa' => 'int',
		'abono' => 'float',
		'precio_venta' => 'float',
		'precio_servicio' => 'float',
		'impuesto' => 'float',
		'status' => 'int',
		'id_punto_venta' => 'int'
	];

	protected $fillable = [
		'id_preventa',
		'abono',
		'precio_venta',
		'precio_servicio',
		'impuesto',
		'status',
		'email_usuario',
		'id_punto_venta',
		'identificacion',
		'razon_nombre',
		'telefono',
		'direccion',
		'email',
		'email_referido',
		'codigo_moneda'
	];

	public function palco_evento()
	{
		return $this->belongsTo(\App\Models\PalcoEvento::class, 'id_palco_evento');
	}

	public function preventum()
	{
		return $this->belongsTo(\App\Models\Preventum::class, 'id_preventa');
	}

	public function moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda');
	}

	public function usuario()
	{
		return $this->belongsTo(\App\Models\Usuario::class, 'email_usuario');
	}

	public function punto_ventum()
	{
		return $this->belongsTo(\App\Models\PuntoVentum::class, 'id_punto_venta');
	}

	public function abono_reservas()
	{
		return $this->hasMany(\App\Models\AbonoReserva::class, 'id_palco_reserva');
	}
}
