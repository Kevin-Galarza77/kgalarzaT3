<?php

namespace App\Http\Controllers\Positions\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeCorporateInfo\Applications\GetCorpInfo;
use App\Http\Controllers\EmployeeCorporateInfo\Applications\UpdateCorpInfo;
use App\Http\Controllers\Employees\Applications\GetEmployees;
use App\Http\Controllers\Positions\Applications\CreatePosition;
use App\Http\Controllers\Positions\Applications\GetPosition;
use App\Http\Controllers\Positions\Applications\UpdatePosition;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Imports\FilesImport;
use Validator;

class UpdatePositionEmployeeController extends Controller
{
    public function update(Request $request)
    {

        $alert = 'No se ha podido actualizar los cargos de los empleados';
        $data = [];
        $messages = [];
        $status = false;

        try {

            $requestFile = $request->file('Documento');
            $validate = self::validateData($requestFile);

            if ($validate['status'] == false)
            {
                $messages = $validate['messages'];
            } else {

                $file = Excel::toArray(new FilesImport, $request->file('Documento'));

                if (count($file[0]) > 1)
                {

                    array_shift($file[0]);
                    $file = array_filter($file[0], function ($row) {
                        return !empty(array_filter($row));
                    });

                    $information  = [];

                    foreach ($file as $employee)
                    {
                        $information[] = [
                            'cedula'  => $employee[0],
                            'cargo'   => rtrim(explode('/', $employee[1])[0])
                        ];
                    }

                    foreach ($information as $employeeInfo)
                    {

                        $getEmploye = new GetEmployees();
                        $getEmploye->getWithCorpInfoByCI(['*'], $employeeInfo['cedula']);

                        if ($getEmploye->employee != null)
                        {

                            $getPosition = new GetPosition();
                            $getPosition->getPositionByParam('cargo_nom', strtoupper($employeeInfo['cargo']));

                            if ($getPosition->position != null)
                            {
                                if ($getEmploye->employee['cargo_id'] != $getPosition->position['cargo_id'])
                                {
                                    $infoUpdate = new UpdateCorpInfo(['cargo_id' => $getPosition->position['cargo_id']]);
                                    $infoUpdate->update($getEmploye->employee['info_corporativa_id']);
                                }
                            } else {

                                $position = new CreatePosition(['name' => $employeeInfo['cargo']]);
                                $position->create();

                                if ($position->positionStatus == true)
                                {
                                    $infoUpdate = new UpdateCorpInfo(['cargo_id' => $position->position['cargo_id']]);
                                    $infoUpdate->update($getEmploye->employee['info_corporativa_id']);
                                }
                            }
                        }else{
                            $messages[] = 'No se encontro al empleado con cédula '.$employeeInfo['cedula'];
                        }
                    }

                    $getPosition = new GetPosition();
                    $getPosition->getAllPositions();

                    //Se agrego el cargo_estado PARA NO TOCAR DATA HISTORICA relacionada con los cargos, y solo mostrar en los selects los cargos activos
                    foreach ($getPosition->positions as $position)
                    {
                        $getCorpInfo = new GetCorpInfo();

                        $getCorpInfo->getEmployeeCorpInfoByParam(2, 'cargo_id', $position['cargo_id']);

                        if (count($getCorpInfo->corpInfos) != 0)
                        {
                            $updatePosition =  new UpdatePosition(['cargo_estado' => 1]);
                        } else {
                            $updatePosition =  new UpdatePosition(['cargo_estado' => 0]);
                        }

                        $updatePosition->update($position['cargo_id']);
                    }

                    $status = true;

                    if (count($messages) == 0)
                    {
                        $alert = 'Se actualizo los cargos de los empleados';
                    } else {
                        $alert = 'Se actualizo los cargos de los empleados pero existen empleados que no se han encontrado';
                    }


                }
            }
        } catch (\Exception $e) {
            \Log::info($e);
            $alert = 'Se produjo algun error en uno de los registros';
        }

        return [
            'alert'     => $alert,
            'data'      => $data,
            'messages'  => $messages,
            'status'    => $status,
        ];
    }

    public function validateData($data)
    {
        $status = true;
        $messages = [];

        $customMessages = [
            'Documento.required'    =>  'El documento de excel es requerido',
            'Documento.mimes'       =>  'El documento ingresado debe ser un archivo Excel (.xlsx)',
            'Documento.max'         =>  'El tamaño del documento debe ser máximo 20 MB'
        ];

        $rules = [
            'Documento' => 'required|file|mimes:xlsx|max:20000'
        ];

        $validator = Validator::make(['Documento' => $data], $rules, $customMessages);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $status = false;
        }

        return [
            'messages' => $messages,
            'status' => $status
        ];
    }
}
