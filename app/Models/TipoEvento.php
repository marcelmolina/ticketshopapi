<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
/**
 * Class TipoEvento
 * 
 * @property int $id
 * @property string $nombre
 * 
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 *
 * @package App\Models
 */
class TipoEvento extends Eloquent
{
    protected $table = 'tipo_evento';
	public $timestamps = false;

	protected $fillable = [
		'nombre'
	];

	public function eventos()
	{
		return $this->hasMany(\App\Models\Evento::class, 'id_tipo_evento');
	}
}
