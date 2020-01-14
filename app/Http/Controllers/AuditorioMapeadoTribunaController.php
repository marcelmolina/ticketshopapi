<?php

namespace App\Http\Controllers;

use App\Models\AuditorioMapeadoTribuna;
use App\Models\AuditorioMapeado;
use App\Models\Tribuna;
use Illuminate\Http\Request;
use Validator;

/**
 * @group Administración de Auditorio_Mapeado-Tribuna
 *
 * APIs para la gestion del Auditorio_mapeado_tribuna
 */
class AuditorioMapeadoTribunaController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla auditorio_mapeado_tribuna paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $aud_map = AuditorioMapeadoTribuna::with('auditorio_mapeado')
                    ->with('tribuna')
                    ->paginate(15);
        return $this->sendResponse($aud_map->toArray(), 'Auditorios mapeados tribuna devueltos con éxito');
    }

    /**
     * Lista de todos los auditorios_mapeados_tribuna.
     *
     * @return \Illuminate\Http\Response
     */
    public function auditorios_map_tribuna_all()
    {
        
         $aud_map = AuditorioMapeadoTribuna::with('auditorio_mapeado')
                    ->with('tribuna')
                    ->get();

         return $this->sendResponse($aud_map->toArray(), 'Auditorios mapeados tribuna devueltos con éxito');
    }

    
    /**
     * Agrega un nuevo elemento a la tabla auditorio_mapeado
     *
     *@bodyParam id_auditorio_mapeado int required ID del auditorio mapeado.  
     *@bodyParam id_tribuna int required ID de la tribuna.
     *@response{       
     *       "id_auditorio_mapeado" : 1,
     *       "id_tribuna":1
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_auditorio_mapeado' => 'required|integer',
            'id_tribuna' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $aud_mapeado = AuditorioMapeado::find($request->input('id_auditorio_mapeado'));
        if (is_null($aud_mapeado)) {
            return $this->sendError('Auditorio mapeado no encontrado');
        }

        $tribuna = Tribuna::find($request->input('id_tribuna'));
        if (is_null($tribuna)) {
            return $this->sendError('Tribuna no encontrada');
        }

        if($this->auditoriomap_tribuna_search($request->input('id_auditorio_mapeado'), $request->input('id_tribuna'))){
             return $this->sendError('Ya se encuentra registrado el auditorio mapeado con la tribuna indicada');
        }

        $aud_map = new AuditorioMapeadoTribuna();
        $aud_map->id_auditorio_mapeado = $request->input('id_auditorio_mapeado');
        $aud_map->id_tribuna = $request->input('id_tribuna');

        $aud_map->save();

        return $this->sendResponse($aud_map->toArray(), 'Auditorio mapeado por tribuna creado con éxito');

    }

    /**
     * Lista de todas las tribuna por auditorio mapeado en especifico 
     *
     * [Se filtra por el ID del auditorio mapeado]
     *
     * @param  \App\Models\AuditorioMapeadoTribuna  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $aud_map = AuditorioMapeadoTribuna::with('auditorio_mapeado')
                    ->with('tribuna')
                    ->where('id_auditorio_mapeado', $id)->get();
        if (is_null($aud_map)) {
            return $this->sendError('Tribunas por auditorio mapeado no encontradas');
        }
        return $this->sendResponse($aud_map->toArray(), 'Tribunas por auditorio mapeado devueltas con éxito');
    }

    
    /**
     * Actualiza un nuevo elemento de la tabla auditorio_mapeado_tribuna
     *
     * [Se filtra por el ID del auditorio_mapeado]
     *
     *@bodyParam id_tribuna_old int required ID de la tribuna antiguo.
     *@bodyParam id_tribuna_new int required ID de la tribuna nuevo.
     *@response{       
     *       "id_tribuna_old":1,     
     *       "id_tribuna_new":2
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AuditorioMapeadoTribuna  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_tribuna_old' => 'required|integer',
            'id_tribuna_new' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $aud_mapeado = AuditorioMapeado::find($id);
        if (is_null($aud_mapeado)) {
            return $this->sendError('Auditorio mapeado no encontrado');
        }

        $tribuna = Tribuna::find($request->input('id_tribuna_new'));
        if (is_null($tribuna)) {
            return $this->sendError('Tribuna nueva indicada no encontrada');
        }

        $aud_map = $this->auditoriomap_tribuna_search($id, $request->input('id_tribuna_old'));
        
        if (!$aud_map) {
            return $this->sendError('Tribuna por auditorio mapeado inidicada no se encuentra registrada');
        }else{

            if($this->auditoriomap_tribuna_search($id, $request->input('id_tribuna_new') )){
                return $this->sendError('Tribuna por auditorio mapeado ya se encuentra registrada');
            }

            $aud_map = AuditorioMapeadoTribuna::where('id_auditorio_mapeado', $id)->update(['id_tribuna' => $request->input('id_tribuna_new')]);

            $aud_map = $this->auditoriomap_tribuna_search($id, $request->input('id_tribuna_new'));

            return $this->sendResponse($aud_map->toArray(), 'Tribuna por auditorio mapeado actualizada con éxito');

        }
    }

    /**
     * Elimina todos las tribunas por auditorio mapeado
     *
     * [Se filtra por el ID del auditorio mapeado]
     *
     * @param  \App\Models\AuditorioMapeadoTribuna  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $aud_map = AuditorioMapeadoTribuna::where('id_auditorio_mapeado', $id)->get();
            if (count($aud_map) == 0) {
                return $this->sendError('Tribunas por auditorio mapeado no encontradas');
            }            
            
            $aud_map_del = AuditorioMapeadoTribuna::where('id_auditorio_mapeado', $id)->delete();
            
            return $this->sendResponse($aud_map->toArray(), 'Tribunas por auditorio mapeado eliminadas con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'Las Tribunas por auditorio mapeado no se pueden eliminar', 'exception' => $e->errorInfo], 400);
        }
    }


    public function auditoriomap_tribuna_search($id_aud_map, $id_tribuna){

        $search = AuditorioMapeadoTribuna::where('id_auditorio_mapeado', $id_aud_map)
                                ->where('id_tribuna', $id_tribuna)
                                ->first();
        return $search;
    }
}

