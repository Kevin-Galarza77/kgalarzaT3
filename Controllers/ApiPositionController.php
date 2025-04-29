<?php

namespace App\Http\Controllers\Positions\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Positions\Applications\GetPosition;

class ApiPositionController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $position = new CreatePositionController();
        return $position->store($request);
    }

    public function show($id)
    {
        $alert = 'No se ha podido mostrar los cargos';
        $status = false;
        $data = [];
        $position = new GetPosition();
        switch($id)
        {
            case 'allPositions':
                $position->getAllPositions(['*']);
                if($position->positionStatus == true)
                {
                    $data = $position->positions;
                    $alert = 'Cargos encontrados';
                    $status = true;
                }
                break;

        }

        return [
            'alert'     =>   $alert,
            'data'      =>   $data,
            'status'    =>   $status
        ];
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $position = new UpdatePositionController();
        return $position->update($id, $request);
    }

    public function destroy($id)
    {
        //
    }
}
