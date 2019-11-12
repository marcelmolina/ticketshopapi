<?php

namespace App\Http\Controllers;

use App\Models\AuditorioMapeado;
use App\Models\Auditorio;
use App\Models\Localidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
/**
 * @group Administración de Mapeo Auditorio
 *
 * APIs para la gestion del mapeo_auditorio
 */
class AuditorioMapeadoController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }


    /**
     * Lista de la tabla auditorio_mapeado paginado.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $aud_map = AuditorioMapeado::with('auditorio')
                    ->paginate(15);
        return $this->sendResponse($aud_map->toArray(), 'Auditorios mapeados devueltos con éxito');
    }


    /**
     * Lista de todos los auditorios mapeados.
     *
     * @return \Illuminate\Http\Response
     */
    public function auditorios_map_all()
    {
        
         $aud_map = AuditorioMapeado::with('auditorio')
                    ->get();

         return $this->sendResponse($aud_map->toArray(), 'Auditorios mapeados devueltos con éxito');
    }

    /**
     * Listado de los auditorios mapeados por auditorio.
     * [Se filtra por el ID del auditorio]
     *
     * @return \Illuminate\Http\Response
     */
    public function auditoriosmap_auditorio($id_auditorio)
    {
        
        $aud_map = AuditorioMapeado::with('auditorio')                    
                    ->where('id_auditorio', $id_auditorio)
                    ->get();

        return $this->sendResponse($aud_map->toArray(), 'Auditorios mapeados devueltos con éxito');
    }
   

    /**
     * Localidades por auditorio mapeado 
     *
     * [Se filtra por el ID del auditorio_mapeado]
     *
     * @param  \App\Models\AuditorioMapeado  $auditorio_mapeado
     * @return \Illuminate\Http\Response
     */
    public function localidades_auditorio_map($id)
    {

        $auditorio_map = AuditorioMapeado::find($id);
        if (!$auditorio_map) {
            return $this->sendError('Auditorio mapeado no encontrado');
        }

        $loca_auditorios = Localidad::wherehas('tribuna')
                      ->wherehas('tribuna.auditorios_mapeados',function($query) use($id){
                            $query->where('id_auditorio_mapeado','=',$id);
                        })
                      ->with(['tribuna.auditorios_mapeados'=>function($query) use($id){
                            $query->where('id_auditorio_mapeado','=',$id);
                        }])
                      ->get();

        if (count($loca_auditorios)== 0) {
            return $this->sendError('Auditorio mapeads no posee localidades');
        }

        $local_aud = array();
        array_push($local_aud, ["localidades" => $loca_auditorios]);

        return $this->sendResponse($local_aud, 'Localidades por auditorio mapeado devueltas con éxito');

    }



    /**
     * Agrega un nuevo elemento a la tabla auditorio_mapeado
     *
     *@bodyParam id_auditorio int required ID del auditorio.  
     *@bodyParam area_mapeada string Area mapeada.
     *@bodyParam imagen file Archivo de la imagen.
     *@response{       
     *       "id_auditorio" : 1,
     *       "area_mapeada":null,
     *       "imagen":null
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_auditorio' => 'required|integer',
            'area_mapeada' => 'nullable|string',
            'imagen' => 'nullable|mimes:jpeg,jpg,bmp,png'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $auditorio = Auditorio::find($request->input('id_auditorio'));
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }

        $urlFile = null;        
        if($request->hasfile('imagen')){
            $file = $request->file('imagen');
            $fileUrl = Storage::disk('public')->put('imagenes', $file);
            $urlFile = env('APP_URL').'storage/'.$fileUrl;
        }

        $aud_map = new AuditorioMapeado();
        $aud_map->id_auditorio = $request->input('id_auditorio');
        $aud_map->area_mapeada = $request->input('area_mapeada');
        $aud_map->imagen = $urlFile;
        $aud_map->save();

        return $this->sendResponse($aud_map->toArray(), 'Auditorio mapeado creado con éxito');

    }

    /**
     * Lista un auditorio mapeado en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\AuditorioMapeado  $auditorioMapeado
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {        

        $aud_map = AuditorioMapeado::with('auditorio')
                    ->find($id);
        if (is_null($aud_map)) {
            return $this->sendError('Auditorio mapeado no encontrado');
        }
        return $this->sendResponse($aud_map->toArray(), 'Auditorio mapeado devuelto con éxito');
    }

   
    /**
     * Actualiza un elemeto de la tabla auditorio_mapeado 
     *
     * [Se filtra por el ID]
     *
     *@bodyParam id_auditorio int required ID del auditorio.    
     *@bodyParam area_mapeada string Area mapeada.
     *@bodyParam imagen file Archivo de la imagen.
     *@response{       
     *       "id_auditorio" : 1,          
     *       "area_mapeada":null,
     *       "imagen":null
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AuditorioMapeado  $id
     * @return \Illuminate\Http\Response
     */
    public function update_auditorio_map(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_auditorio' => 'required|integer',
            'area_mapeada' => 'nullable|string',
            'imagen' => 'nullable|mimes:jpeg,jpg,bmp,png'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $auditorio = Auditorio::find($request->input('id_auditorio'));
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }

        $aud_map = AuditorioMapeado::find($id);
        if (is_null($aud_map)) {
            return $this->sendError('Auditorio mapeado no encontrado');
        }


        $aud_map->id_auditorio = $request->input('id_auditorio');        
        $aud_map->area_mapeada = $request->input('area_mapeada');
        
        $urlFile = null;        
        if($request->hasfile('imagen')){
            $file = $request->file('imagen');
            $fileUrl = Storage::disk('public')->put('imagenes', $file);
            $urlFile = env('APP_URL').'storage/'.$fileUrl;

            $imagen_name = explode("/", $aud_map->imagen);
            
            $exists = file_exists('storage/imagenes/'.$imagen_name[sizeof($imagen_name) - 1 ]);            
            if($exists){                
                unlink('storage/imagenes/'.$imagen_name[sizeof($imagen_name) - 1 ]);
            }
            $aud_map->imagen = $urlFile;
        }

        $aud_map->save();

        return $this->sendResponse($aud_map->toArray(), 'Auditorio mapeado actualizado con éxito');

    }

    /**
     * Elimina un elemento de la tabla auditorio_mapeado
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\AuditorioMapeado  $auditorioMapeado
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $aud_map = AuditorioMapeado::find($id);
            if (is_null($aud_map)) {
                return $this->sendError('Auditorio mapeado no encontrado');
            }
            $imagen_name = explode("/", $aud_map->imagen);
            $exists = file_exists('storage/imagenes/'.$imagen_name[sizeof($imagen_name) - 1 ]);            
            if($exists){                
                unlink('storage/imagenes/'.$imagen_name[sizeof($imagen_name) - 1 ]);
            }
            $aud_map->delete();
            return $this->sendResponse($aud_map->toArray(), 'Auditorio mapeado eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El auditorio mapeado no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
