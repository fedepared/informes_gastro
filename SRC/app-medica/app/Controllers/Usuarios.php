<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use CodeIgniter\HTTP\ResponseInterface;

class Usuarios extends BaseController
{
    private $UsuariosModel;

    public function __construct()
    {
        $this->UsuariosModel = new UsuariosModel();
    }

    // 🔹 Obtener todos los usuarios
    public function getUsuarios()
    {
        $usuarios = $this->UsuariosModel->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $usuarios
        ]);
    }

    // 🔹 Obtener un usuario por ID
    public function getByIdUsuarios($id)
    {
        $usuario = $this->UsuariosModel->find($id);

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $usuario
        ]);
    }

    // 🔹 Crear un nuevo usuario
    public function postUsuarios()
    {
        $claveHash = password_hash('1234', PASSWORD_DEFAULT);

        $data = [
            'nombre_usuario' => 'admin2',
            'pass' => $claveHash,
            'mail' => 'admin2@gmail.com',
        ];

        $this->UsuariosModel->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Usuario creado exitosamente',
            'data' => $data
        ])->setStatusCode(ResponseInterface::HTTP_CREATED);
    }

    // 🔹 Eliminar un usuario por ID
    public function deleteUsuarios($id)
    {
        $usuario = $this->UsuariosModel->find($id);

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $this->UsuariosModel->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Usuario eliminado correctamente'
        ]);
    }

    // 🔹 Actualizar usuario por ID
    public function updateUsuarios($id)
    {
        $usuario = $this->UsuariosModel->find($id);

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Datos correctos para Usuarios
        $data = [
            'nombre_usuario' => 'abril',
            'mail' => 'abril@gmail.com'
        ];

        $this->UsuariosModel->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Usuario actualizado correctamente',
            'data' => $data
        ]);
    }
}
