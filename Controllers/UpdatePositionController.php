<?php

namespace App\Http\Controllers\Positions\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Positions\Applications\GetPosition;
use App\Http\Controllers\Positions\Applications\UpdatePosition;
use Illuminate\Http\Request;
use Validator;

class UpdatePositionController extends Controller
{

    public function update($id, Request $request)
    {
        $alert = 'No se ha podido actualizar el cargo, intente nuevamente';
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

                if($getPosition->position != null && $getPosition->position->cargo_id != $id)
                {
                    $messages [] = 'Ya existe un cargo con el nombre ingresado';

                }else{

                    $getPosition->getPositionByParam('cargo_cod', $request->get('codigo'));

                    if($getPosition->position != null && $getPosition->position->cargo_id != $id)
                    {
                        $messages [] = 'Ya existe un cargo con el codigo ingresado';

                    } else {

                        $updatePosition = new UpdatePosition([
                            'cargo_cod'     =>  $request->get('codigo'),
                            'cargo_nom'     =>  $request->get('nombre'),
                            'cargo_estado'  =>  $request->get('estado')
                        ]);

                        $updatePosition->update($id);

                        if($updatePosition->positionStatus == true)
                        {
                            $alert = 'Se ha actualizado el cargo exitosamente';
                            $data = $updatePosition->position;
                            $status = true;
                        }

                    }

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
            'nombre.required'       =>  'El nombre del cargo es requerido',
            'estado.required'       =>  'El estado del cargo es requerido',
        ];

        $rules = [
            'nombre'        =>  'required',
            'estado'        =>  'required'
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
