<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TipoCupon
 * 
 * @property int $id
 * @property string $nombre
 * 
 * @property \Illuminate\Database\Eloquent\Collection $cupons
 *
 * @package App\Models
 */
class TipoCupon extends Eloquent
{
	protected $table = 'tipo_cupon';
	public $timestamps = false;

	protected $fillable = [
		'nombre'
	];

	public function cupons()
	{
		return $this->hasMany(\App\Models\Cupon::class, 'id_tipo_cupon');
	}
}
