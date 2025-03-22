<?php

namespace App\Controllers;

use App\Models\CoberturasModel;

class Coberturas extends BaseController
{
    private  $coberturasModel;
    public function __construct()
    {
        $this->coberturasModel = new CoberturasModel();
    }

    public function getCoberturas()
    {
        /*         $coberturasModel = new CoberturasModel();
 */
        $resultado = $this->coberturasModel->findAll();

        echo 'get all';
        echo '<pre>';
        print_r($resultado);
        echo '</pre>';
    }

    public function getByIdCoberturas($id)
    {
        $resultado = $this->coberturasModel->find($id);

        if (!empty($resultado)) {
            echo 'getbyid';
            echo '<pre>';
            print_r($resultado);
            echo '</pre>';
            return $resultado;
        } else {
            echo 'no se encontro cobertura  ';
            return null;
        }
    }

    public function postCobertura()
    {


        $data = [
            'nombre_cobertura' => 'iosfa'
        ];

        $this->coberturasModel->insert($data);

        echo 'Post';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
    public function deleteCobertura($id)
    {
       
        $antes = $this->getByIdCoberturas($id);


        $this->coberturasModel->delete($id);

        $despues = $this->getByIdCoberturas($id);

        echo '<pre>';
        echo 'Delete';
        echo '<pre>';
        echo 'antes';
        print_r($antes);

        echo 'despues';
        print_r($despues);
        echo '</pre>';
    }

    public function updateCobertura($id)
    {

        $antes = $this->getByIdCoberturas($id);

        $data = [
            'nombre_cobertura' => 'iosfa modificado'
        ];


        $this->coberturasModel->update($id, $data);
        $despues = $this->getByIdCoberturas($id);


        echo 'Update';
        echo '<pre>';
        echo 'antes';
        print_r($antes);

        echo 'despues';
        print_r($despues);
        echo '</pre>';
    }
}
