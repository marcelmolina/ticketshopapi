<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class EventoCuponera
 * 
 * @property int $id_evento
 * @property int $id_cuponera
 * 
 * @property \App\Models\Evento $evento
 * @property \App\Models\Cuponera $cuponera
 *
 * @package App\Models
 */
class EventoCuponera extends Eloquent
{
	protected $table = 'evento_cuponera';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_evento' => 'int',
		'id_cuponera' => 'int'
	];

	protected $fillable = [
		'id_evento',
		'id_cuponera'		
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function cuponera()
	{
		return $this->belongsTo(\App\Models\Cuponera::class, 'id_cuponera');
	}
}
