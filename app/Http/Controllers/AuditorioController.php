<?php

namespace App\Http\Controllers;

use App\Models\Auditorio;
use Illuminate\Http\Request;
use Validator;

class AuditorioController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $auditorio = Auditorio::paginate(15);

         return $this->sendResponse($auditorio->toArray(), 'Auditorios devueltos con éxito');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
         $validator = Validator::make($request->all(), [
            'nombre' => 'required',   
            'ciudad' => 'required',
        'departamento' => 'required',
        'pais' => 'required',
        'direccion' => 'required',
        'longitud' => 'numeric',
         'latitud' => 'numeric',
        'aforo' => 'integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $auditorio=Auditorio::create($request->all());        
         return $this->sendResponse($auditorio->toArray(), 'Auditorio creado con éxito');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         $auditorio = Auditorio::find($id);


        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }


        return $this->sendResponse($auditorio->toArray(), 'Auditorio devuelto con éxito');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Auditorio $auditorio)
    {
        //
         $input = $request->all();


        $validator = Validator::make($request->all(), [
           'nombre' => 'required',   
            'ciudad' => 'required',
        'departamento' => 'required',
        'pais' => 'required',
        'direccion' => 'required',
        'longitud' => 'numeric',
         'latitud' => 'numeric',
        'aforo' => 'integer'           
        ]);


        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }


        $auditorio->nombre = $input['nombre'];
        $auditorio->ciudad = $input['ciudad'];
        $auditorio->departamento = $input['departamento'];
        $auditorio->pais = $input['pais'];
        $auditorio->direccion = $input['direccion'];        
        if (!is_null($request->input('latitud'))) 
            $auditorio->latitud = $input['latitud'];
        if (!is_null($request->input('longitud'))) 
            $auditorio->longitud = $input['longitud'];
        if (!is_null($request->input('aforo'))) 
            $auditorio->aforo = $input['aforo'];
         $auditorio->save();

        return $this->sendResponse($auditorio->toArray(), 'Auditorio actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $auditorio =Auditorio::find($id);
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }
        $auditorio->delete();


        return $this->sendResponse($auditorio->toArray(), 'Auditorio eliminado con éxito');
    }
}
