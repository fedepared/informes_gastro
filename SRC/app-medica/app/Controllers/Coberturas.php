<?php

namespace App\Controllers;

use App\Models\CoberturasModel;

class Coberturas extends BaseController
{
    public function getCoberturas()
    {
        $coberturasModel = new CoberturasModel();
        $resultado = $coberturasModel->findAll();

        echo 'get all';
        echo '<pre>';
        print_r($resultado);
        echo '</pre>';
    }

    public function getByIdCoberturas($id)
    {
     /*    $id = 1; */

        $coberturasModel = new CoberturasModel();
        $resultado = $coberturasModel->find($id);

        echo 'getbyid';
        echo '<pre>';
        print_r($resultado);
        echo '</pre>';
    }
}
