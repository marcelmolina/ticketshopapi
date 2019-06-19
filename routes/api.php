<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
	Route::apiResource('condicion','CondicionController');
	Route::get('buscarCondicion','CondicionController@buscarCondicion');

	Route::apiResource('tipocosto','TipoCostoController');
	Route::get('buscarTipoCosto','TipoCostoController@buscarTipoCosto');

	Route::apiResource('descuentoevento','DescuentoEventoController');

	Route::apiResource('puestospalcoevento','PuestosPalcoEventoController');

	Route::apiResource('detalledescuento','DetalleDescuentoController');
	
	Route::apiResource('preventum','PreVentumController');
	Route::get('buscarPreventa','PreVentumController@buscarPreventa');
	
	Route::apiResource('moneda','MonedaController');
	Route::get('buscarMoneda','MonedaController@buscarMoneda');
	
	Route::apiResource('boletasprevent','BoletasPreventController');
	Route::apiResource('condicionesevento','CondicionesEventoController');
	Route::apiResource('costoevento','CostoEventoController');
	Route::apiResource('boletaevento','BoletaEventoController');
	Route::apiResource('tasa','TasaController');
	Route::apiResource('palcoevento','PalcoEventoController');
	Route::apiResource('palcoprevent','PalcoPreventController');
	Route::post('convertir','TasaController@convertir');
/*---------------------------------------------------------------------------------------*/
  	Route::apiResource('genero','GeneroController');
  	Route::get('buscarGenero','GeneroController@buscarGenero');
	
	Route::apiResource('artista','ArtistController');
	Route::get('buscarArtistas','ArtistController@buscarArtistas');
	Route::get('listado_detalle_artistas','ArtistController@listado_detalle_artistas');
	
	Route::apiResource('temporada','TemporadaController');
	Route::get('buscarTemporada','TemporadaController@buscarTemporada');
	Route::get('listado_venta_temporadas','TemporadaController@listado_venta_temporadas');
	
	Route::apiResource('imagen','ImagenController');
	Route::post('updateImage/{imagen}','ImagenController@updateImage');

	Route::apiResource('auditorio','AuditorioController');
	Route::get('buscarAuditorio','AuditorioController@buscarAuditorio');
	Route::get('listado_detalle_auditorios','AuditorioController@listado_detalle_auditorios');

	Route::apiResource('imagenesauditorio','ImagenesAuditorioController');

	Route::apiResource('tipodescuento','TipoDescuentoController');
	Route::get('buscarTipoDescuento','TipoDescuentoController@buscarTipoDescuento');

	Route::apiResource('cupon','CuponController');
	Route::get('listado_detalle_cupones','CuponController@listado_detalle_cupones');

	Route::apiResource('tipocupon','TipoCuponController');
	Route::get('buscarTipoCupon','TipoCuponController@buscarTipoCupon');

	Route::apiResource('cuponera','CuponeraController');
	Route::get('buscarCuponera','CuponeraController@buscarCuponera');
	Route::get('listado_detalle_cuponeras','CuponeraController@listado_detalle_cuponeras');

	Route::apiResource('tipoevento','TipoEventoController');
	Route::get('buscarTipoEvento','TipoEventoController@buscarTipoEvento');

	Route::apiResource('pais','PaisController');
	Route::get('buscarPais','PaisController@buscarPais');

	Route::apiResource('departamento','DepartamentoController');
	Route::get('buscarDepartamento','DepartamentoController@buscarDepartamento');

	Route::apiResource('ciudad','CiudadController');
	Route::get('buscarCiudad','CiudadController@buscarCiudad');	

	Route::apiResource('evento','EventoController');
	Route::get('buscarEvento','EventoController@buscarEvento');

	Route::apiResource('imagenevento','ImagenEventoController');
	Route::apiResource('imagenartist','ImagenArtistController');
	Route::apiResource('puntoventaevento','PuntoventaEventoController');
	Route::apiResource('eventocuponera','EventoCuponeraController');
	
	Route::apiResource('cliente','ClienteController');
	Route::get('buscarClientes','ClienteController@buscarClientes');

    Route::apiResource('puestospalco','PuestosPalcoController');
    
    Route::apiResource('puntoventum','PuntoVentumController');
    Route::get('buscarPuntoVentum','PuntoVentumController@buscarPuntoVentum');
    
    Route::apiResource('grupovendedorespto','GrupoVendedoresPtoController');

	Route::apiResource('palco','PalcoController');
	Route::get('buscarPalco','PalcoController@buscarPalco');
	Route::get('listado_detalle_palcos','PalcoController@listado_detalle_palcos');

	Route::apiResource('fila','FilaController');
	Route::get('buscarFila','FilaController@buscarFila');
    Route::get('listado_detalle_filas','FilaController@listado_detalle_filas');

	Route::apiResource('puesto','PuestoController');
	Route::get('buscarPuestos','PuestoController@buscarPuestos');
	Route::get('listado_detalle_puestos','PuestoController@listado_detalle_puestos');

	
	Route::apiResource('localidad','LocalidadController');
	Route::get('listado_detalle_localidades','LocalidadController@listado_detalle_localidades');	
	Route::get('buscarLocalidad','LocalidadController@buscarLocalidad');	

	Route::apiResource('tribuna','TribunaController');
	Route::get('listado_detalle_tribunas','TribunaController@listado_detalle_tribunas');
	Route::get('buscarTribuna','TribunaController@buscarTribuna');

	Route::apiResource('grupsvendedore','GrupsVendedoreController');
	Route::get('buscarGrupoVendedores','GrupsVendedoreController@buscarGrupoVendedores');	

	Route::get('listeventipo/{listeventipo}','EventoController@listeventipo');
	Route::get('detalle_evento/{detalle_evento}','EventoController@detalle_evento');
	Route::get('buscar_evento','EventoController@buscar_evento');
	Route::get('listadoartistevento','ArtistController@listadoartistevento');

	Route::get('listausuarios', 'UsuarioController@listausuarios');

	Route::get('comprasrealizadas/{comprasrealizadas}', 'UsuarioController@comprasrealizadas');
	Route::get('temporadascompradas/{temporadascompradas}', 'UsuarioController@temporadascompradas');
	Route::get('reservas/{reservas}', 'UsuarioController@reservas');

	Route::post('login', 'UsuarioController@login');
	Route::post('register', 'UsuarioController@register');
	Route::delete('destroy/{usuario}', 'UsuarioController@destroy');

	Route::get('validateToken', 'UsuarioController@validateToken');

	Route::get('auth/signup/activate/{token}', 'UsuarioController@signupActivate');

	Route::get('auth/{provider}', 'UsuarioController@redirectToProvider');
	Route::get('auth/{provider}/callback', 'UsuarioController@handleProviderCallback');

	Route::post('creatreset', 'PasswordResetController@creatreset');
	Route::get('password/find/{token}', 'PasswordResetController@find');
	Route::post('reset', 'PasswordResetController@reset');

	Route::group(['middleware' => 'auth:api'], function () { 
		Route::post('cambioclave', 'UsuarioController@cambioclave');
		Route::post('detailsuser', 'UsuarioController@detailsuser');
		Route::put('updateprofile/{updateprofile}', 'UsuarioController@updateprofile');	
		Route::post('logout', 'UsuarioController@logout');
	});




