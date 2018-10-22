<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;
use Validator;

class GeneroController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $genero = Genero::paginate(15);

        return $this->sendResponse($genero->toArray(), 'Géneros devueltos con éxito');
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
            'nombre' => 'required'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $genero=Genero::create($request->all());        
         return $this->sendResponse($genero->toArray(), 'Género creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Genero  $genero
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         $genero = Genero::find($id);


        if (is_null($genero)) {
            return $this->sendError('Género no encontrado');
        }


        return $this->sendResponse($genero->toArray(), 'Género devuelto con éxito');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Genero  $genero
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Genero  $genero
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Genero $genero)
    {
        //
        $input = $request->all();


        $validator = Validator::make($input, [
            'nombre' => 'required',            
        ]);


        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }


        $genero->nombre = $input['nombre'];
         $genero->save();

        return $this->sendResponse($genero->toArray(), 'Género actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Genero  $genero
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $genero = Genero::find($id);
        if (is_null($genero)) {
            return $this->sendError('Género no encontrado');
        }
        $genero->delete();


        return $this->sendResponse($genero->toArray(), 'Genero eliminado con éxito');
    }
}
