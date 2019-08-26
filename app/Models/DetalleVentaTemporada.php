<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DetalleVentaTemporada
 * 
 * @property int $id
 * @property int $id_venta_temporada
 * @property int $id_evento
 * @property int $id_boleta_evento
 * @property int $id_palco_evento
 * @property bool $status
 * 
 * @property \App\Models\VentaTemporada $venta_temporada
 * @property \App\Models\Evento $evento
 * @property \App\Models\BoletaEvento $boleta_evento
 * @property \App\Models\PalcoEvento $palco_evento
 *
 * @package App\Models
 */
class DetalleVentaTemporada extends Eloquent
{
	protected $table = 'detalle_venta_temporada';
	public $timestamps = false;

	protected $casts = [
		'id_venta_temporada' => 'int',
		'id_evento' => 'int',
		'id_boleta_evento' => 'int',
		'id_palco_evento' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'id_venta_temporada',
		'id_evento',
		'id_boleta_evento',
		'id_palco_evento',
		'status'
	];

	public function venta_temporada()
	{
		return $this->belongsTo(\App\Models\VentaTemporada::class, 'id_venta_temporada');
	}

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
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
