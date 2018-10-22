<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PuestosPalcoEvento
 * 
 * @property int $id_palco_evento
 * @property int $id_palco
 * @property int $id_puesto
 * 
 * @property \App\Models\PalcoEvento $palco_evento
 * @property \App\Models\PuestosPalco $puestos_palco
 *
 * @package App\Models
 */
class PuestosPalcoEvento extends Eloquent
{
	protected $table = 'puestos_palco_evento';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_palco_evento' => 'int',
		'id_palco' => 'int',
		'id_puesto' => 'int'
	];

	public function palco_evento()
	{
		return $this->belongsTo(\App\Models\PalcoEvento::class, 'id_palco_evento');
	}

	public function puestos_palco()
	{
		return $this->belongsTo(\App\Models\PuestosPalco::class, 'id_puesto', 'id_puesto');
	}
}
