<?php

namespace App\Controllers;

use App\Models\UsuariosModel;

class Usuarios extends BaseController
{
    private  $UsuariosModel;
    public function __construct()
    {
        $this->UsuariosModel = new UsuariosModel();
    }



    public function getUsuarios()
    {

        $resultado = $this->UsuariosModel->findAll();

        echo 'get all';
        echo '<pre>';
        print_r($resultado);
        echo '</pre>';
    }

    public function getByIdUsuarios($id)
    {
        $resultado = $this->UsuariosModel->find($id);

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

    public function postUsuarios()
    {
        $claveHash = password_hash(1234, PASSWORD_DEFAULT);

        $data = [
            'nombre_usuario' => 'admin2',
            'pass' => $claveHash,
            'mail' => 'admin2@gmail.com',
            
       
        ];

        $this->UsuariosModel->insert($data);

        echo 'Post';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
    public function deleteUsuarios($id)
    {

        $antes = $this->getByIdUsuarios($id);


        $this->UsuariosModel->delete($id);

        $despues = $this->getByIdUsuarios($id);

        echo '<pre>';
        echo 'Delete';
        echo '<pre>';
        echo 'antes';
        print_r($antes);

        echo 'despues';
        print_r($despues);
        echo '</pre>';
    }

    public function updateUsuarios($id)
    {

        $antes = $this->getByIdUsuarios($id);

        $data = [
            'nombre_paciente' => 'abril',
            'fecha' => '2025-03-22',
            'url_archivo' => 'url',
            'mail_paciente' => 'abril@gmail.com',
            'id_cobertura' => '5',
       
        ];


        $this->UsuariosModel->update($id, $data);
        $despues = $this->getByIdUsuarios($id);


        echo 'Update';
        echo '<pre>';
        echo 'antes';
        print_r($antes);

        echo 'despues';
        print_r($despues);
        echo '</pre>';
    }
}
