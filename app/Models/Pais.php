<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
/**
 * Class Pais
 * 
 * @property int $id
 * @property string $descripcion
 * 
 * @property \Illuminate\Database\Eloquent\Collection $departamentos
 *
 * @package App\Models
 */
class Pais extends Eloquent
{
    protected $table = 'pais';
	public $timestamps = false;

	protected $fillable = [
		'descripcion'
	];

	public function departamentos()
	{
		return $this->hasMany(\App\Models\Departameto::class, 'id_pais');
	}
}
