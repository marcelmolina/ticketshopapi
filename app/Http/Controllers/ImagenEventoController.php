<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imagen;
use App\Models\Evento;
use App\Models\ImagenEvento;
use Validator;
/**
 * @group Administración de Imagenes Evento
 *
 * APIs para la gestion de la tabla imagen_evento
 */
class ImagenEventoController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }


    /**
     * Listado de las imagenes por eventos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $img_evento = ImagenEvento::with('evento')
                        ->with('imagen')
                      ->paginate(15);
        return $this->sendResponse($img_evento->toArray(), 'Imagenes de eventos devueltas con éxito');
    }


    /**
     * Agrega un nuevo elemento a la tabla imagen_evento 
     *@bodyParam id_imagen int required Id de la imagen.
     *@bodyParam id_evento int required Id del evento. 
     * @response {
     *  "id_imagen": 1,
     *  "id_evento": 1,      
     * } 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         
        $validator = Validator::make($request->all(), [
            'id_imagen' => 'required|integer',
            'id_evento' => 'required|integer',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $img_evento_search = ImagenEventoController::img_evento_search($request->input('id_evento'), $request->input('id_imagen'));

        if(count($img_evento_search) != 0){
           return $this->sendError('El evento ya posee esa imagen asociada'); 
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $imagen = Imagen::find($request->input('id_imagen'));
        if (is_null($imagen)) {
            return $this->sendError('La imagen indicada no existe');
        }


         $img_evento = ImagenEvento::create($request->all());        
         return $this->sendResponse($img_evento->toArray(), 'Imagenes de evento creado con éxito');
    }

    /**
     * Lista de imagenes por evento en especifico
     * [Se filtra por el ID del evento]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $img_evento = ImagenEvento::with('evento')
                        ->with('imagen')->where('id_evento','=',$id)->get();
        if (count($img_evento) == 0) {
            return $this->sendError('Imagenes por evento no encontradas');
        }
        return $this->sendResponse($img_evento->toArray(), 'Imagenes por evento devueltas con éxito');
    }


    /**
     * Actualiza un elemeto de la tabla imagen_evento 
     *
     * [Se filtra por el ID del evento]
     *@bodyParam id_imagen_old int required Id de la imagen (La cual se quiere editar).
     *@bodyParam id_imagen_new int required Id de la imagen (Id nuevo de la imagen).
     * @response {
     *  "id_imagen_old": 1,
     *  "id_imagen_new": 2,      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [            
            'id_imagen_old' => 'required',
            'id_imagen_new' => 'required',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());      
        }
        $img_evento_search = ImagenEventoController::img_evento_search($id, $input['id_imagen_old']);

        if(count($img_evento_search) != 0){

            $evento = Evento::find($id);
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }

            $imagen = Imagen::find($input['id_imagen_new']);
            if (is_null($imagen)) {
                return $this->sendError('La imagen indicada no existe');
            }

            $img_evento_search2 = ImagenEventoController::img_evento_search($id, $input['id_imagen_new']);
            
            if(count($img_evento_search2) != 0){
                return $this->sendError('La imagen por evento ya se encuentra asociada'); 
            }
            
        }else{
           return $this->sendError('La imagen por evento no se encuentra'); 
        }

        ImagenEvento::where('id_evento','=',$id)
                            ->where('id_imagen','=', $input['id_imagen_old'])
                            ->update(['id_imagen' => $input['id_imagen_new']]);  
        
        $imagen_evento = ImagenEventoController::img_evento_search($id, $input['id_imagen_new']);
                            
        return $this->sendResponse($imagen_evento->toArray(), 'Imagen por evento actualizada con éxito');
    }

    /**
     * Elimina todos los elemento de la tabla imagen_evento
     *
     * [Se filtra por el ID del evento]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $imagen_evento = ImagenEvento::where('id_evento','=',$id)->get();
        if (count($imagen_evento) == 0) {
            return $this->sendError('Imagenes por evento no encontradas');
        }
        ImagenEvento::where('id_evento','=',$id)->delete();
        return $this->sendResponse($imagen_evento->toArray(), 'Imagenes por evento eliminadas con éxito');
    }

     public function img_evento_search($id_evento, $id_imagen){

        $search = ImagenEvento::where('id_imagen','=',$id_imagen)
                                ->where('id_evento','=', $id_evento)->get();
        return $search;
    }
}
