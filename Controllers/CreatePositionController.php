<?php

namespace App\Http\Controllers\Positions\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Positions\Applications\CreatePosition;
use App\Http\Controllers\Positions\Applications\GetPosition;
use Illuminate\Http\Request;
use Validator;

class CreatePositionController extends Controller
{
    public function store(Request $request)
    {
        $alert = 'No se ha podido crear el cargo, intente nuevamente';
        $data = [];
        $messages = [];
        $status = false;

        try{

            $validator = self::validatedata($request->all());

            if($validator['status'] == false)
            {
                $messages = $validator['messages'];
            }else{
                $getPosition = new GetPosition();
                $getPosition->getPositionByParam('cargo_nom', $request->get('nombre'));

                if($getPosition->position == null)
                {
                    $getPosition->getPositionByParam('cargo_cod', $request->get('codigo'));

                    if($getPosition->position == null)
                    {
                        $createPosition = new CreatePosition([
                            'code'     =>  $request->get('codigo'),
                            'name'     =>  $request->get('nombre'),
                            'status'   =>  $request->get('estado')
                        ]);

                        $createPosition->create();

                        if($createPosition->positionStatus == true)
                        {
                            $alert = 'Se ha creado el cargo exitosamente';
                            $data = $createPosition->position;
                            $status = true;
                        }

                    } else {
                        $messages [] = 'Ya existe un codigo con el nombre ingresado';
                    }
                }else{
                    $messages [] = 'Ya existe un cargo con el nombre ingresado';
                }
            }
        }catch(\Exception $e){
            \Log::info($e);
        }

        return[
            'alert'     =>  $alert,
            'data'      =>  $data,
            'messages'  =>  $messages,
            'status'    =>  $status
        ];
    }

    private function validatedata($data)
    {
        $status = true;
        $messages = [];

        $customMessages = [
            'nombre.required'       =>  'El nombre del cargo es requerido'
        ];

        $rules = [
            'nombre'        =>  'required'
        ];

        $validator = Validator::make($data, $rules, $customMessages);

        if($validator->fails())
        {
            $status = false;
            $messages = $validator->errors()->all();
        }

        return [
            'messages'  =>  $messages,
            'status'    =>  $status
        ];
    }

}
