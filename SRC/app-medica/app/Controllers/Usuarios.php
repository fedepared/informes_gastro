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


    public function login()
    {
        $model = new UsuariosModel(); // Instanciamos el modelo correctamente

        $data = $this->request->getJSON(true);
        
        // Verificar si se recibieron los datos correctamente
        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se recibieron datos'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Validar que los datos requeridos estén presentes
        if (!isset($data['nombre_usuario']) || !isset($data['pass'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos requeridos: nombre_usuario, pass'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Usar la función personalizada para buscar el usuario
        $usuario = $model->where('nombre_usuario', $data['nombre_usuario'])->first();
        
        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Verificar la contraseña
        if (!password_verify($data['pass'], $usuario['pass'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Contraseña incorrecta'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // Si las credenciales son correctas, devolver una respuesta de éxito
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'id' => $usuario['id_usuario'],
                'nombre_usuario' => $usuario['nombre_usuario'],
                'mail' => $usuario['mail']
            ]
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }
    
    // Ejemplo de un método ficticio para generar un token JWT
    private function generateJWT($userId)
    {
        // Lógica para generar el token JWT
        // Puedes usar librerías como Firebase JWT para esto.
        return 'JWT_TOKEN_GENERADO';
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

    public function postUsuarios()
    {
        // Obtener los datos enviados desde Postman (en formato JSON)
        $data = $this->request->getJSON(true);

        // Validar que los datos requeridos estén presentes
        if (!isset($data['nombre_usuario']) || !isset($data['pass']) || !isset($data['mail'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos requeridos: nombre_usuario, pass, mail'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Hashear la contraseña
        $data['pass'] = password_hash($data['pass'], PASSWORD_DEFAULT);

        // Insertar los datos en la base de datos
        $this->UsuariosModel->insert($data);

        // Responder con éxito
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
