<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArtistaEvento
 * 
 * @property int $id_artista
 * @property int $id_evento
 * 
 * @property \App\Models\Artist $artist
 * @property \App\Models\Evento $evento
 *
 * @package App\Models
 */
class ArtistaEvento extends Eloquent
{
	protected $table = 'artista_evento';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_artista' => 'int',
		'id_evento' => 'int'
	];

	public function artist()
	{
		return $this->belongsTo(\App\Models\Artist::class, 'id_artista');
	}

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}
}
