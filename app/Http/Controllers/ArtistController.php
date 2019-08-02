<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ArtistaEvento;
use App\Models\Genero;
use App\Models\ImagenArtist;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Input;
/**
 * @group Administración de Artista
 *
 * APIs para la gestion de artista
 */
class ArtistController extends BaseController
{
    /**
     * Lista de la tabla artista paginados.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $artist = Artist::paginate(15);
        return $this->sendResponse($artist->toArray(), 'Artistas devueltos con éxito');
    }


    /**
     * Lista de la tabla de todos los artista.
     *
     * @return \Illuminate\Http\Response
     */
    public function artistas_all()
    {
        
        $artist = Artist::get();
        return $this->sendResponse($artist->toArray(), 'Artistas devueltos con éxito');
    }


    /**
     * Buscar Artistas por nombre.
     *@bodyParam nombre string Nombre del artista.
     *@response{
     *    "nombre" : "Artista 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarArtistas(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $artist = Artist::with('genero')
                ->where('artist.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($artist->toArray(), 'Todos los Artistas filtrados');
       }else{
            
            $artist = Artist::with('genero') 
                ->get();
            return $this->sendResponse($artist->toArray(), 'Todos los Artistas devueltos'); 
       }

        
    }


    /**
     * Listado detallado de los artistas.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado_detalle_artistas()
    {
        
        $artist = Artist::with('genero')->paginate(15);
        $lista_artist = compact('artist');
        return $this->sendResponse($lista_artist, 'Artistas devueltos con éxito');
    }

   /**
     * Agrega un nuevo elemento a la tabla artista
     *@bodyParam nombre string required El nombre del artista.
     *@bodyParam manager string required Nombre del manager del artista.
     *@bodyParam id_genero int required id del genero.
     *@response{
     *       "nombre" : "Artist",
     *       "manager" : "Manager Artist",
     *       "id_genero": 1
     *     }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
         $validator = Validator::make($request->all(), [
            'nombre' => 'required',            
            'manager' => 'required',
            'id_genero' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        $genero = Genero::find($request->input('id_genero'));
        if (is_null($genero)) {
            return $this->sendError('El Género indicado no existe');
        }

          $artist=Artist::create($request->all());        
         return $this->sendResponse($artist->toArray(), 'Artista creado con éxito');
    }

    /**
     * Lista un artista en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         $artist = Artist::find($id);


        if (is_null($artist)) {
            return $this->sendError('Artista no encontrado');
        }


        return $this->sendResponse($artist->toArray(), 'Artista devuelto con éxito');
    }

 
     /**
     * Actualiza un elemeto de la tabla artista 
     *
     * [Se filtra por el ID]
     *@bodyParam nombre string required El nombre del artista.
     *@bodyParam manager string required Nombre del manager del artista.
     *@bodyParam id_genero int required id del genero.
     *@response{
     *       "nombre" : "Artist Edit",
     *       "manager" : "Manager Artist",
     *       "id_genero": 1
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artist  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
       
        $input = $request->all();


        $validator = Validator::make($input, [
            'nombre' => 'required',            
            'manager' => 'required',
            'id_genero' => 'required'           
        ]);


        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
         $genero = Genero::find($request->input('id_genero'));
        if (is_null($genero)) {
            return $this->sendError('El Género indicado no existe');
        }

        $artist2 = Artist::find($id);
        if (is_null($artist2)) {
                    return $this->sendError('Artista no encontrado');
                }
        $artist2->nombre = $input['nombre'];
        $artist2->manager = $input['manager'];
        $artist2->id_genero = $input['id_genero'];         
         $artist2->save();
        

        return $this->sendResponse($artist2->toArray(), 'Artista actualizado con éxito');

    }

    /**
     * Elimina un elemento de la tabla artista
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $artist = Artist::find($id);
        $artist->delete();


        return $this->sendResponse($artist->toArray(), 'Artista eliminado con éxito');
    }


    /**
     * Listado de artistas por evento
     *
     * [Solo se mostrarán aquellos artistas que posean un evento asociado]
     *
     *
    */
    public function listadoartistevento(){

        $artist_event = ArtistaEvento::all();
        $id_array = array();
        $artist_full = array();
        if(count($artist_event) != 0){
            foreach ($artist_event as $artist) {
                array_push($id_array, $artist->id_artista);
            }
           
           for ($i=0; $i < sizeof($id_array); $i++) { 
               
               $artist = \DB::table('artista_evento')
                      ->join('artist', 'artista_evento.id_artista', '=', 'artist.id')
                      ->join('genero', 'artist.id_genero', '=', 'genero.id')
                      ->where('artista_evento.id_artista','=', $id_array[$i])
                      ->select('artist.nombre AS nombre_artista', 'artist.manager', 'genero.nombre AS genero')
                      ->get();

                $artist_img = \DB::table('imagen_artist')
                      ->join('imagen', 'imagen_artist.id_imagen', '=', 'imagen.id')
                      ->where('imagen_artist.id_artista','=', $id_array[$i])
                      ->select('imagen.nombre', 'imagen.url')
                      ->get();

                $artist->first()->imagenes_artista = $artist_img->toArray();

                array_push($artist_full, $artist);
           }

           return $this->sendResponse($artist_full, 'Listado de artistas con eventos planificados devuelto con éxito');

        }else{
            return $this->sendError('No existen artistas con eventos planificados');
        }

        
    }
}
