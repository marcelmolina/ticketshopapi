<?php

namespace App\Http\Controllers;

use App\Models\LocalidadEvento;
use App\Models\Localidad;
use App\Models\Evento;
use App\Models\Moneda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Validator;


/**
 * @group Administración de Localidad Evento
 *
 * APIs para la gestion de la tabla localidad_evento
 */
class LocalidadEventoController extends BaseController
{
 
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);
    }

   
    /**
     * Lista de la tabla localidad_evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $localidad_evento = LocalidadEvento::with("evento")
                    ->with("localidad")
                    ->with("codigo_moneda")
                    ->paginate(15);

        return $this->sendResponse($localidad_evento->toArray(), 'Localidades de los eventos devueltos con éxito');
    }


    public function getB64Image($base64_image){  
        $image_service_str = substr($base64_image, strpos($base64_image, ",")+1);
        $image = base64_decode($image_service_str);   
        return $image; 
    }

    public function getB64Extension($base64_image, $full=null){  
        preg_match("/^data:image\/(.*);base64/i",$base64_image, $img_extension);   
        return ($full) ?  $img_extension[0] : $img_extension[1];  
    }

    /**
     * Agrega un nuevo elemento a la tabla localidad_evento
     *
     *@bodyParam id_localidad int required ID de la localidad.
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam precio_venta float required Precio de venta de la boleta del evento.
     *@bodyParam precio_servicio float required Precio del servicio.
     *@bodyParam codigo_moneda string Codigo de la moneda.  
     *@bodyParam imagen string Imagen en formato base64.   
     *
     *@response{
     *       "id_localidad" : 2,
     *       "id_evento" : 2,
     *       "impuesto" : 0,
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "imagen": null,
     *       "codigo_moneda" : "USD"               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_localidad' => 'required|integer',
            'id_evento'=> 'required|integer',
            'precio_venta' => 'required',
            'precio_servicio' => 'required',
            'impuesto' => 'nullable',
            'imagen' => 'nullable|string',
            'codigo_moneda' => 'nullable'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('La localidad indicado no existe');
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        if($this->localidad_evento_search($request->input('id_localidad'),$request->input('id_evento'))){
            return $this->sendError('El localidad ya se encuentra asignado al evento.');
        } 

        if(!is_null($request->input('codigo_moneda'))){
            $moneda = Moneda::find($request->input('codigo_moneda'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda indicado no existe');
            }
        }else{
            Input::merge(['codigo_moneda' => null]);
        }

        if(is_null($request->input('impuesto'))){
            Input::merge(['impuesto' => 0]);
        }

        $urlFile = null;
        if($request->input('imagen') != null || $request->input('imagen') != ""){

            $img = $this->getB64Image($request->input('imagen'));  

            $img_extension = $this->getB64Extension($request->input('imagen'));        
            $img_name = 'image_base64'. time() . '.' . $img_extension; 
            $path = public_path() . "/storage/imagenes/" . $img_name;
            file_put_contents($path, $img);
            $urlFile = env('APP_URL').'storage/imagenes/'. $img_name;

        }      


        $localidad = new LocalidadEvento();
        $localidad->id_localidad = $request->input('id_localidad');
        $localidad->id_evento = $request->input('id_evento');
        $localidad->impuesto = $request->input('impuesto');
        $localidad->precio_venta = $request->input('precio_venta');
        $localidad->precio_servicio = $request->input('precio_servicio');
        $localidad->codigo_moneda = $request->input('codigo_moneda');
        $localidad->url_imagen = $urlFile;

        $localidad->save();

        return $this->sendResponse($localidad->toArray(), 'Localidad del evento creada con éxito');
    }

    /**
     * Listado de las localidades por evento en especifico 
     *
     * [Se filtra por el ID del evento]
     *
     * @param  \App\Models\LocalidadEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $localidad_evento = LocalidadEvento::with("evento")
                    ->with("localidad")
                    ->with("codigo_moneda")
                    ->where('id_evento', $id)
                    ->get();   

        if (count($localidad_evento) == 0) {
            return $this->sendError('No se encuentran localidades por evento');
        }
        return $this->sendResponse($localidad_evento->toArray(), 'La localidad por evento devueltos con éxito');
    }
  

    /**
     * Actualiza un elemento a la tabla localidad_evento.
     *
     * [Se filtra por el ID del evento]
     *
     *@bodyParam id_localidad int required ID de la localidad.
     *@bodyParam precio_venta float required Precio de eventa de la boleta del evento.
     *@bodyParam precio_servicio float required Precio del servicio.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam url_imagen string Url de la imagen.
     *@bodyParam codigo_moneda string Codigo de la moneda.     
     *
     *@response{
     *       "id_localidad" : 2,
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "impuesto" : null,
     *       "url_imagen":null
     *       "codigo_moneda" : "USD"               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Evento  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id_localidad' => 'required|integer',
            'precio_venta' => 'required',
            'precio_servicio' => 'required',
            'impuesto' => 'nullable',
            'imagen' => 'nullable|string',
            'codigo_moneda' => 'nullable' 
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $localidad = Localidad::find($input['id_localidad']);
        if (is_null($localidad)) {
            return $this->sendError('La localidad indicado no existe');
        }

        $evento = Evento::find($input['id_evento']);
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        if(!is_null($request->input('codigo_moneda'))){
            $moneda = Moneda::find($request->input('codigo_moneda'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda indicado no existe');
            }
        }else{
            Input::merge(['codigo_moneda' => null]);
        }

        if(is_null($request->input('impuesto'))){
            Input::merge(['impuesto' => 0]);
        }

        $urlFile = null;
        if($request->input('imagen') != null || $request->input('imagen') != ""){

            $img = $this->getB64Image($request->input('imagen'));  

            $img_extension = $this->getB64Extension($request->input('imagen'));        
            $img_name = 'image_base64'. time() . '.' . $img_extension; 
            $path = public_path() . "/storage/imagenes/" . $img_name;
            file_put_contents($path, $img);
            $urlFile = env('APP_URL').'storage/imagenes/'. $img_name;

        }   

        LocalidadEvento::where('id_evento', $id)
                            ->where('id_localidad',  $input['id_localidad'])
                            ->update([
                                'precio_venta' => $input['precio_venta'],
                                'precio_servicio' => $input['precio_servicio'],
                                'impuesto' => $input['impuesto'],
                                'codigo_moneda' => $input['codigo_moneda'],
                                'url_imagen' => $urlFile,
                            ]);  
        
        $localidad_evento = LocalidadEvento::with("evento")
                                        ->with("localidad")
                                        ->with("codigo_moneda")
                                        ->where('id_evento', $id)
                                        ->get();
                            
        return $this->sendResponse($localidad_evento->toArray(), 'Localidades por evento actualizados con éxito');

    }

    /**
     * Elimina un elemento de la tabla localidad_evento
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\BoletaEvento  $boletaEvento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $localidad_evento = LocalidadEvento::where('id_evento', $id)->get();
            if (count($localidad_evento) == 0) {
                return $this->sendError('No se encuentran localidades por evento');
            }
            LocalidadEvento::where('id_evento', $id)->delete();
            return $this->sendResponse($localidad_evento->toArray(), 'Artistas por evento eliminados con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'Las localidades por evento no se puede eliminar', 'exception' => $e->errorInfo], 400);
        }
    }

    public function localidad_evento_search($id_localidad, $id_evento){

        $search = LocalidadEvento::where('id_localidad','=',$id_localidad)
                                ->where('id_evento','=', $id_evento)->first();
        return $search;
    }   

}
