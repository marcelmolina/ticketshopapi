<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AbonoReserva
 * 
 * @property int $id
 * @property int $id_boleto_reserva
 * @property int $id_palco_reserva
 * @property float $monto_abono
 * 
 * @property \App\Models\BoletaReserva $boleta_reserva
 * @property \App\Models\PalcoReserva $palco_reserva
 *
 * @package App\Models
 */
class AbonoReserva extends Eloquent
{
	protected $table = 'abono_reserva';
	public $timestamps = false;

	protected $casts = [
		'id_boleto_reserva' => 'int',
		'id_palco_reserva' => 'int',
		'monto_abono' => 'float'
	];

	protected $fillable = [
		'id_boleto_reserva',
		'id_palco_reserva',
		'monto_abono'
	];

	public function boleta_reserva()
	{
		return $this->belongsTo(\App\Models\BoletaReserva::class, 'id_boleto_reserva');
	}

	public function palco_reserva()
	{
		return $this->belongsTo(\App\Models\PalcoReserva::class, 'id_palco_reserva');
	}
}
