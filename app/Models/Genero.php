<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Genero
 * 
 * @property int $id
 * @property string $nombre
 * 
 * @property \Illuminate\Database\Eloquent\Collection $artists
 *
 * @package App\Models
 */
class Genero extends Eloquent
{
	protected $table = 'genero';
	public $timestamps = false;

	protected $fillable = [
		'nombre'
	];

	public function artists()
	{
		return $this->hasMany(\App\Models\Artist::class, 'id_genero');
	}
}
