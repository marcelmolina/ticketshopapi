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
	Route::get('condiciones_all','CondicionController@condiciones_all');

	Route::apiResource('tipocosto','TipoCostoController');
	Route::get('buscarTipoCosto','TipoCostoController@buscarTipoCosto');
	Route::get('tipo_costo_all','TipoCostoController@tipo_costo_all');

	Route::apiResource('descuentoevento','DescuentoEventoController');
	Route::get('buscarDescuentoEvento','DescuentoEventoController@buscarDescuentoEvento');

	Route::apiResource('puestospalcoevento','PuestosPalcoEventoController');

	Route::apiResource('detalledescuento','DetalleDescuentoController');
	
	Route::apiResource('preventum','PreVentumController');
	Route::get('buscarPreventa','PreVentumController@buscarPreventa');
	Route::get('preventum_all','PreVentumController@preventum_all');
	Route::get('listado_preventasEvento/{listado_preventasEvento}','PreVentumController@listado_preventasEvento');
	
	Route::apiResource('moneda','MonedaController');
	Route::get('buscarMoneda','MonedaController@buscarMoneda');
	Route::get('moneda_all','MonedaController@moneda_all');
	
	Route::apiResource('boletasprevent','BoletasPreventController');
	Route::apiResource('condicionesevento','CondicionesEventoController');
	Route::apiResource('costoevento','CostoEventoController');
	Route::apiResource('boletaevento','BoletaEventoController');
	
	Route::apiResource('palcoevento','PalcoEventoController');
	Route::apiResource('palcoprevent','PalcoPreventController');
	
	Route::apiResource('tasa','TasaController');
	Route::post('convertir','TasaController@convertir');
	Route::get('tasa_all','TasaController@tasa_all');
/*---------------------------------------------------------------------------------------*/
  	Route::apiResource('genero','GeneroController');
  	Route::get('buscarGenero','GeneroController@buscarGenero');
  	Route::get('generos_all','GeneroController@generos_all');
	
	Route::apiResource('artista','ArtistController');
	Route::get('buscarArtistas','ArtistController@buscarArtistas');
	Route::get('artistas_all','ArtistController@artistas_all');
	Route::get('listado_detalle_artistas','ArtistController@listado_detalle_artistas');
	
	Route::apiResource('temporada','TemporadaController');
	Route::get('buscarTemporada','TemporadaController@buscarTemporada');
	Route::get('temporada_all','TemporadaController@temporada_all');
	Route::get('listado_venta_temporadas','TemporadaController@listado_venta_temporadas');
	
	Route::apiResource('imagen','ImagenController');
	Route::post('updateImage/{imagen}','ImagenController@updateImage');

	Route::apiResource('auditorio','AuditorioController');
	Route::get('buscarAuditorio','AuditorioController@buscarAuditorio');
	Route::get('auditorio_all','AuditorioController@auditorio_all');
	Route::get('listado_detalle_auditorios','AuditorioController@listado_detalle_auditorios');

	Route::apiResource('imagenesauditorio','ImagenesAuditorioController');

	Route::apiResource('tipodescuento','TipoDescuentoController');
	Route::get('buscarTipoDescuento','TipoDescuentoController@buscarTipoDescuento');
	Route::get('tipo_descuento_all','TipoDescuentoController@tipo_descuento_all');

	Route::apiResource('cupon','CuponController');
	Route::get('listado_detalle_cupones','CuponController@listado_detalle_cupones');

	Route::apiResource('tipocupon','TipoCuponController');
	Route::get('buscarTipoCupon','TipoCuponController@buscarTipoCupon');
	Route::get('tipo_cupon_all','TipoCuponController@tipo_cupon_all');

	Route::apiResource('cuponera','CuponeraController');
	Route::get('buscarCuponera','CuponeraController@buscarCuponera');
	Route::get('cuponera_all','CuponeraController@cuponera_all');
	Route::get('listado_detalle_cuponeras','CuponeraController@listado_detalle_cuponeras');

	Route::apiResource('tipoevento','TipoEventoController');
	Route::get('buscarTipoEvento','TipoEventoController@buscarTipoEvento');
	Route::get('tipo_evento_all','TipoEventoController@tipo_evento_all');

	Route::apiResource('pais','PaisController');
	Route::get('buscarPais','PaisController@buscarPais');
	Route::get('pais_all','pais_all@buscarPais');

	Route::apiResource('departamento','DepartamentoController');
	Route::get('buscarDepartamento','DepartamentoController@buscarDepartamento');
	Route::get('departamento_all','DepartamentoController@departamento_all');

	Route::apiResource('ciudad','CiudadController');
	Route::get('buscarCiudad','CiudadController@buscarCiudad');
	Route::get('ciudades_all','CiudadController@ciudades_all');	

	Route::apiResource('evento','EventoController');
	Route::get('buscarEvento','EventoController@buscarEvento');
	Route::get('evento_all','EventoController@evento_all');

	Route::apiResource('imagenevento','ImagenEventoController');
	Route::apiResource('imagenartist','ImagenArtistController');
	Route::apiResource('puntoventaevento','PuntoventaEventoController');
	Route::apiResource('eventocuponera','EventoCuponeraController');
	
	Route::apiResource('cliente','ClienteController');
	Route::get('buscarClientes','ClienteController@buscarClientes');
	Route::get('clientes_all','ClienteController@clientes_all');

    Route::apiResource('puestospalco','PuestosPalcoController');
    
    Route::apiResource('puntoventum','PuntoVentumController');
    Route::get('buscarPuntoVentum','PuntoVentumController@buscarPuntoVentum');
    Route::get('puntoventum_all','PuntoVentumController@puntoventum_all');
    
    Route::apiResource('grupovendedorespto','GrupoVendedoresPtoController');

	Route::apiResource('palco','PalcoController');
	Route::get('buscarPalco','PalcoController@buscarPalco');
	Route::get('palco_all','PalcoController@palco_all');

	Route::apiResource('fila','FilaController');
	Route::get('buscarFila','FilaController@buscarFila');
	Route::get('fila_all','FilaController@fila_all');
    Route::get('listado_detalle_filas','FilaController@listado_detalle_filas');

	Route::apiResource('puesto','PuestoController');
	Route::get('buscarPuestos','PuestoController@buscarPuestos');
	Route::get('puesto_all','PuestoController@puesto_all');

	
	Route::apiResource('localidad','LocalidadController');
	Route::get('localidad_all','LocalidadController@localidad_all');	
	Route::get('buscarLocalidad','LocalidadController@buscarLocalidad');	

	Route::apiResource('tribuna','TribunaController');
	Route::get('listado_detalle_tribunas','TribunaController@listado_detalle_tribunas');
	Route::get('buscarTribuna','TribunaController@buscarTribuna');
	Route::get('tribuna_all','TribunaController@tribuna_all');

	Route::apiResource('grupsvendedore','GrupsVendedoreController');
	Route::get('buscarGrupoVendedores','GrupsVendedoreController@buscarGrupoVendedores');
	Route::get('groups_vendedores_all','GrupsVendedoreController@groups_vendedores_all');	

	Route::get('listeventipo/{listeventipo}','EventoController@listeventipo');
	Route::get('detalle_evento/{detalle_evento}','EventoController@detalle_evento');
	Route::post('buscar_evento','EventoController@buscar_evento');
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




