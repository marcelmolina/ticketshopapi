<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PuntoVentum
 * 
 * @property int $id
 * @property string $nombre_razon
 * @property string $identificacion
 * @property string $direccion
 * @property string $telefono
 * @property bool $tipo_identificacion
 * @property string $responsable
 * @property string $email
 * @property string $zona
 * @property int $id_ciudad
 * 
 * @property \Illuminate\Database\Eloquent\Collection $boleta_reservas
 * @property \Illuminate\Database\Eloquent\Collection $devolucions
 * @property \Illuminate\Database\Eloquent\Collection $grupo_vendedores_ptos
 * @property \Illuminate\Database\Eloquent\Collection $palco_reservas
 * @property \Illuminate\Database\Eloquent\Collection $puntoventa_eventos
 * @property \Illuminate\Database\Eloquent\Collection $vents
 *
 * @package App\Models
 */
class PuntoVentum extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'tipo_identificacion' => 'bool'
	];

	protected $fillable = [
		'nombre_razon',
		'identificacion',
		'direccion',
		'telefono',
		'tipo_identificacion',
		'responsable',
		'email',
		'zona',
		'id_ciudad'
	];

	public function ciudades()
	{
		return $this->belongsTo(\App\Models\Ciudad::class, 'id_ciudad');
	}

	public function boleta_reservas()
	{
		return $this->hasMany(\App\Models\BoletaReserva::class, 'id_punto_venta');
	}

	public function devolucions()
	{
		return $this->hasMany(\App\Models\Devolucion::class, 'id_punto_venta');
	}

	public function grupo_vendedores_ptos()
	{
		return $this->hasMany(\App\Models\GrupoVendedoresPto::class, 'id_punto_venta');
	}

	public function palco_reservas()
	{
		return $this->hasMany(\App\Models\PalcoReserva::class, 'id_punto_venta');
	}

	public function puntoventa_eventos()
	{
		return $this->hasMany(\App\Models\PuntoventaEvento::class, 'id_puntoventa');
	}

	public function vents()
	{
		return $this->hasMany(\App\Models\Vent::class, 'id_punto_venta');
	}
}
