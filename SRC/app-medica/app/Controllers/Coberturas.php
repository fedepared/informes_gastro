<?php

namespace App\Controllers;

use App\Models\CoberturasModel;

class Coberturas extends BaseController
{
    public function getCoberturas()
    {
        $coberturasModel = new CoberturasModel();
        $resultado = $coberturasModel->findAll();

        echo '<pre>';
        print_r($resultado);
        echo '</pre>';
    }
}