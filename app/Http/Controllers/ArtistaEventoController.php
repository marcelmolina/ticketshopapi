<?php

namespace App\Http\Controllers;

use App\Models\ArtistaEvento;
use App\Models\Evento;
use App\Models\Artist;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Artista - Evento
 *
 * APIs para la asignación del artista con el evento
 */
class ArtistaEventoController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);
    }

    /**
     * Lista de la tabla artista evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $artist_evento = ArtistaEvento::with("evento")
                    ->with("artist")
                    ->paginate(15);

        return $this->sendResponse($artist_evento->toArray(), 'Artistas de los eventos devueltos con éxito');
    }

    /**
     * Agrega un nuevo elemento a la tabla artista_evento
     *
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam id_artista int required ID del artista.    
     *
     *@response{
     *       "id_evento" : 2,
     *       "id_artista" : 2,              
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_evento'=> 'required|integer',
            'id_artista' => 'required|integer'           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $artist = Artist::find($request->input('id_artista'));
        if (is_null($artist)) {
            return $this->sendError('El artista indicado no existe');
        }

        if($this->artist_evento_search($request->input('id_evento'),$request->input('id_artista'))){
            return $this->sendError('El artista ya se encuentra asignado al evento.');
        }        

        $artist_evento = new ArtistaEvento();
        $artist_evento->id_evento =  $request->input('id_evento');
        $artist_evento->id_artista = $request->input('id_artista');
        $artist_evento->save();
        return $this->sendResponse($artist_evento->toArray(), 'Artista asignado al evento con éxito');
    }

    

    /**
     * Listado de los artistas por evento en especifico 
     *
     * [Se filtra por el ID del evento]
     *
     * @param  \App\Models\ArtistaEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $artist_evento = ArtistaEvento::with("evento")
                                        ->with("artist")
                                        ->with("artist.imagens")
                                        ->where('id_evento', $id)
                                        ->get();

        if (count($artist_evento) == 0) {
            return $this->sendError('No se encuentran artistas por evento');
        }
        return $this->sendResponse($artist_evento->toArray(), 'Artistas por evento devueltos con éxito');
    }

 

    /**
     * Actualiza un elemeto de la tabla imagenes_auditorio 
     *
     * [Se filtra por el ID del auditorio]
     *@bodyParam id_artista_old int required Id del artista (El cual se quiere editar).
     *@bodyParam id_artista_new int required Id del artista (Id del nuevo artista).
     * @response {
     *  "id_artista_old": 1,
     *  "id_artista_new": 2,      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Evento  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [            
            'id_artista_old' => 'required|integer',
            'id_artista_new' => 'required|integer',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());      
        }

        $evento_artista_search = $this->artist_evento_search($id, $input['id_artista_old']);

        if($evento_artista_search){

            $artist = Artist::find($input['id_artista_new']);
            if (is_null($artist)) {
                return $this->sendError('El artista indicado no existe');
            }

            $evento_artista_search2 = $this->artist_evento_search($id, $input['id_artista_new']);
            
            if($evento_artista_search2){
                return $this->sendError('El artista por evento ya se encuentra asociado'); 
            }

        }else{
           return $this->sendError('El artista por evento no se encuentra'); 
        }

        ArtistaEvento::where('id_evento', $id)
                            ->where('id_artista',  $input['id_artista_old'])
                            ->update(['id_artista' => $input['id_artista_new']]);  
        
        $artist_evento = ArtistaEvento::with("evento")
                                        ->with("artist")
                                        ->where('id_evento', $id)
                                        ->get();
                            
        return $this->sendResponse($artist_evento->toArray(), 'Artistas por evento actualizados con éxito');


    }




    /**
     * Elimina los artistas del evento
     *
     * [Se filtra por el ID del evento]
     *
     * @param  \App\Models\ArtistaEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $artista_evento = ArtistaEvento::where('id_evento', $id)->get();
            if (count($artista_evento) == 0) {
                return $this->sendError('No se encuentran artistas por evento');
            }
            ArtistaEvento::where('id_evento', $id)->delete();
            return $this->sendResponse($artista_evento->toArray(), 'Artistas por evento eliminados con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'Los artistas por evento no se puede eliminar', 'exception' => $e->errorInfo], 400);
        }
    }




    public function artist_evento_search($id_evento, $id_artista){

        $search = ArtistaEvento::where('id_evento', $id_evento)
                                ->where('id_artista', $id_artista)
                                ->first();
        return $search;
    }
}

