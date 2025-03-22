<?php

namespace App\Controllers;

use App\Models\InformesModel;

class Informes extends BaseController
{
    private  $InformesModel;
    public function __construct()
    {
        $this->InformesModel = new InformesModel();
    }



    public function getInformes()
    {

        $resultado = $this->InformesModel->findAll();

        echo 'get all';
        echo '<pre>';
        print_r($resultado);
        echo '</pre>';
    }

    public function getByIdInformes($id)
    {
        $resultado = $this->InformesModel->find($id);

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

    public function postInforme()
    {


        $data = [
            'nombre_paciente' => 'fede',
            'fecha' => '2025-03-22',
            'url_archivo' => 'url',
            'mail_paciente' => 'fede@gmail.com',
            'id_cobertura' => '5',
       
        ];

        $this->InformesModel->insert($data);

        echo 'Post';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
    public function deleteInforme($id)
    {

        $antes = $this->getByIdInformes($id);


        $this->InformesModel->delete($id);

        $despues = $this->getByIdInformes($id);

        echo '<pre>';
        echo 'Delete';
        echo '<pre>';
        echo 'antes';
        print_r($antes);

        echo 'despues';
        print_r($despues);
        echo '</pre>';
    }

    public function updateInforme($id)
    {

        $antes = $this->getByIdInformes($id);

        $data = [
            'nombre_paciente' => 'abril',
            'fecha' => '2025-03-22',
            'url_archivo' => 'url',
            'mail_paciente' => 'abril@gmail.com',
            'id_cobertura' => '5',
       
        ];


        $this->InformesModel->update($id, $data);
        $despues = $this->getByIdInformes($id);


        echo 'Update';
        echo '<pre>';
        echo 'antes';
        print_r($antes);

        echo 'despues';
        print_r($despues);
        echo '</pre>';
    }
}
