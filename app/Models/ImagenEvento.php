<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ImagenEvento
 * 
 * @property int $id_imagen
 * @property int $id_evento
 * 
 * @property \App\Models\Imagen $imagen
 * @property \App\Models\Evento $evento
 *
 * @package App\Models
 */
class ImagenEvento extends Eloquent
{
	protected $table = 'imagen_evento';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_imagen' => 'int',
		'id_evento' => 'int'
	];

	protected $fillable = [
		'id_imagen',
		'id_evento'
	];

	public function imagen()
	{
		return $this->belongsTo(\App\Models\Imagen::class, 'id_imagen');
	}

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}
}
