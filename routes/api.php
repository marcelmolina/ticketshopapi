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

	Route::post('login', 'UsuarioController@login');
	Route::post('register', 'UsuarioController@register');
	
	Route::get('validateToken', 'UsuarioController@validateToken');

	Route::get('auth/signup/activate/{token}', 'UsuarioController@signupActivate');

	Route::get('auth/{provider}', 'UsuarioController@redirectToProvider');
	Route::get('auth/{provider}/callback', 'UsuarioController@handleProviderCallback');

	Route::get('listausuarios', 'UsuarioController@listausuarios');

	Route::get('comprasrealizadas/{comprasrealizadas}', 'UsuarioController@comprasrealizadas');
	Route::get('temporadascompradas/{temporadascompradas}', 'UsuarioController@temporadascompradas');
	Route::get('reservas/{reservas}', 'UsuarioController@reservas');

	Route::post('creatreset', 'PasswordResetController@creatreset');
	Route::get('password/find/{token}', 'PasswordResetController@find');
	Route::post('reset', 'PasswordResetController@reset');

	Route::get('get_qr','QrController@get_qr');
	Route::post('info_token_qr','QrController@info_token_qr');

	Route::apiResource('rol','RolController');
	Route::get('rol_usuarios/{id_rol}','RolController@rol_usuarios');
	Route::get('roles_all','RolController@roles_all');

	Route::apiResource('condicion','CondicionController');
	Route::get('buscarCondicion','CondicionController@buscarCondicion');
	Route::get('condiciones_all','CondicionController@condiciones_all');

	Route::apiResource('tipoidentificacion','TipoIdentificacionController');
	Route::get('buscarTipoIdentificacion','TipoIdentificacionController@buscarTipoIdentificacion');
	Route::get('tipo_identificacion_all','TipoIdentificacionController@tipo_identificacion_all');

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
	
	Route::apiResource('precios_monedas','PreciosMonedasController');
	Route::get('precios_evento/{id_evento}','PreciosMonedasController@precios_evento');
	Route::get('precios_evento_moneda/{id_evento}/{codigo_moneda}','PreciosMonedasController@precios_evento_moneda');
	Route::delete('destroy_evento/{id_evento}/{codigo_moneda}','PreciosMonedasController@destroy_evento');

	Route::apiResource('moneda','MonedaController');
	Route::get('buscarMoneda','MonedaController@buscarMoneda');
	Route::get('moneda_all','MonedaController@moneda_all');

	Route::apiResource('abonoreserva','AbonoReservaController');
	Route::get('abonos_boleta/{id_boleto_reserva}','AbonoReservaController@abonos_boleta');
	Route::get('abonos_palco/{id_palco_reserva}','AbonoReservaController@abonos_palco');
	
	Route::apiResource('palcosreservado','PalcoReservaController');
	Route::get('palcosreservados_all','PalcoReservaController@palcosreservados_all');

	Route::apiResource('boletasreservada','BoletaReservaController');
	Route::get('boletasreservadas_all','BoletaReservaController@boletasreservadas_all');

	Route::apiResource('boletasprevent','BoletasPreventController');
	Route::apiResource('condicionesevento','CondicionesEventoController');
	
	Route::apiResource('costoevento','CostoEventoController');
	Route::delete('costosevento','CostoEventoController@destroyxevento');
	Route::get('costos_evento/{id_evento}','CostoEventoController@costos_evento');
	
	Route::apiResource('boletapreimpresa','BoletasPreimpresaController');
	Route::get('boletapreimpresa_all','BoletasPreimpresaController@boletaspreimpresas_all');

	Route::apiResource('palcopreimpreso','PalcoPreimpresoController');
    Route::get('palcospreimpresos_all','PalcoPreimpresoController@palcospreimpresos_all');

	Route::apiResource('boletaevento','BoletaEventoController');
	Route::put('boletaevento_status/{id}','BoletaEventoController@update_status');
	Route::get('listado_puestos_evento/{id}','BoletaEventoController@listado_puestos_evento');
	Route::get('listado_boletas_localidad/{id_localidad}/{id_evento}/{codigo_moneda}','BoletaEventoController@listado_boletas_localidad');
	Route::post('boletasxlocalidad','BoletaEventoController@storexlocalidad');	
	//Route::post('boletas_palcos_reservadas/{id_localidad}/{id_evento}','BoletaEventoController@boletas_palcos_reservadas');
	Route::post('boletas_palcos_reservadas','BoletaEventoController@boletas_palcos_reservadas');
	Route::post('precioPuestoEvento','BoletaEventoController@getPrecio');
	
	Route::apiResource('palcoevento','PalcoEventoController');	
	Route::put('palcoevento_status/{id}','PalcoEventoController@update_status');	
	Route::post('palcosxlocalidad','PalcoEventoController@storexlocalidad');
	Route::get('listado_palcos_localidad/{id_localidad}','PalcoEventoController@listado_palcos_localidad');

	Route::delete('deleteboletaspalcos/{id_evento}','LocalidadEventoController@deletexevento');

	Route::apiResource('palcoprevent','PalcoPreventController');

	Route::apiResource('devolucion','DevolucionController');
	Route::get('devolucion_all','DevolucionController@devolucion_all');
	
	Route::apiResource('tasa','TasaController');
	Route::post('convertir','TasaController@convertir');
	Route::get('tasa_all','TasaController@tasa_all');
/*---------------------------------------------------------------------------------------*/
  	Route::apiResource('genero','GeneroController');
  	Route::get('buscarGenero','GeneroController@buscarGenero');
  	Route::get('generos_all','GeneroController@generos_all');
	
	Route::apiResource('artista','ArtistController');
	Route::post('updateArtist','ArtistController@updateArtist');
	Route::get('buscarArtistas','ArtistController@buscarArtistas');
	Route::get('artistas_all','ArtistController@artistas_all');
	Route::get('listado_detalle_artistas','ArtistController@listado_detalle_artistas');
	Route::get('listadoartistevento','ArtistController@listadoartistevento');

	Route::apiResource('artista_evento','ArtistaEventoController');
	
	Route::apiResource('temporada','TemporadaController');
	Route::get('buscarTemporada','TemporadaController@buscarTemporada');
	Route::get('temporada_all','TemporadaController@temporada_all');
	Route::get('listado_venta_temporadas','TemporadaController@listado_venta_temporadas');
	
	Route::apiResource('imagen','ImagenController');
	Route::post('updateImage/{imagen}','ImagenController@updateImage');
	Route::post('save_base64','ImagenController@save_base64');

	Route::apiResource('auditorio','AuditorioController');
	Route::get('buscarAuditorio','AuditorioController@buscarAuditorio');
	Route::get('auditorio_all','AuditorioController@auditorio_all');
	Route::get('listado_detalle_auditorios','AuditorioController@listado_detalle_auditorios');
	Route::get('localidades_auditorio/{id}','AuditorioController@localidades_auditorio');

	Route::apiResource('auditorio_map','AuditorioMapeadoController');
	Route::post('update_auditorio_map/{id}','AuditorioMapeadoController@update_auditorio_map');
	Route::get('auditoriosmap_auditorio/{id_auditorio}','AuditorioMapeadoController@auditoriosmap_auditorio');
	Route::get('auditorios_map_all','AuditorioMapeadoController@auditorios_map_all');
	Route::get('localidades_auditorio_map/{id_auditorio_map}','AuditorioMapeadoController@localidades_auditorio_map');
	Route::get('localidadesevento_auditorio_map/{id_evento}','AuditorioMapeadoController@localidadesevento_auditorio_map');

	Route::apiResource('localidad_evento','LocalidadEventoController');


	Route::apiResource('auditorio_map_tribuna','AuditorioMapeadoTribunaController');
	Route::get('auditorios_map_tribuna_all','AuditorioMapeadoTribunaController@auditorios_map_tribuna_all');

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
	Route::get('pais_all','PaisController@pais_all');

	Route::apiResource('departamento','DepartamentoController');
	Route::get('departamentos_pais','DepartamentoController@departamentos_pais');
	Route::get('buscarDepartamento','DepartamentoController@buscarDepartamento');
	Route::get('departamento_all','DepartamentoController@departamento_all');

	Route::apiResource('ciudad','CiudadController');
	Route::get('ciudades_departamento','CiudadController@ciudades_departamento');
	Route::get('buscarCiudad','CiudadController@buscarCiudad');
	Route::get('ciudades_all','CiudadController@ciudades_all');	

	Route::apiResource('evento','EventoController');
	Route::get('buscarEvento','EventoController@buscarEvento');
	Route::get('evento_all','EventoController@evento_all');
	Route::get('listeventipo/{listeventipo}','EventoController@listeventipo');
	Route::get('detalle_evento/{detalle_evento}','EventoController@detalle_evento');
	Route::post('buscar_evento','EventoController@buscar_evento');
	Route::get('eventos_usuario','EventoController@eventos_usuario');
	Route::get('eventos_estado/{estado}','EventoController@eventos_estado');
	Route::get('artist_evento_precios','EventoController@artist_evento_precios');

	Route::apiResource('imagenevento','ImagenEventoController');
	Route::apiResource('imagenevento','ImagenEventoController');
	Route::delete('deleteimagenevento/{evento}/{imagen}', 'ImagenEventoController@destroy_evento_imagen');
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
    Route::get('filas_localidad/{id_localidad}','FilaController@filas_localidad');

	Route::apiResource('puesto','PuestoController');
	Route::post('puestosxfila','PuestoController@storexfila');
	Route::get('buscarPuestos','PuestoController@buscarPuestos');
	Route::get('puesto_all','PuestoController@puesto_all');
	Route::get('puestos_fila/{id_fila}','PuestoController@puestos_fila');
	Route::get('puestos_auditorio/{id_auditorio}','PuestoController@puestos_auditorio');
	
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

	Route::get('payment_confirm','PaymentController@payment_confirm');

	Route::apiResource('venta','VentController');
	Route::post('generarsha','VentController@generarsha');
	Route::post('obtener_refventa','VentController@obtener_refventa');

	Route::apiResource('auditoria_evento','AuditoriaEventoController');

	Route::group(['middleware' => 'auth:api' ], function () {
	Route::post('cambioclave', 'UsuarioController@cambioclave');
	Route::post('detailsuser', 'UsuarioController@detailsuser');
	Route::put('updateprofile/{updateprofile}', 'UsuarioController@updateprofile');	
	Route::post('logout', 'UsuarioController@logout');
	Route::delete('destroy/{usuario}', 'UsuarioController@destroy');

	Route::apiResource('detalleventa','DetalleVentController');
	Route::get('detalleventa_all','DetalleVentController@detalleventa_all');
});






