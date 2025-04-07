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

    public function cambiarPassword()
    {
        $model = new UsuariosModel(); // Instanciamos el modelo de usuarios
        $data = $this->request->getJSON(true);

        // Verificar si se recibieron los datos correctamente
        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se recibieron datos'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Validar que los datos requeridos estén presentes
        if (!isset($data['id_usuario']) || !isset($data['password_actual']) || !isset($data['password_nuevo']) || !isset($data['password_confirmar'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos requeridos: id_usuario, password_actual, password_nuevo, password_confirmar'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $idUsuario = $data['id_usuario'];
        $passwordActual = $data['password_actual'];
        $passwordNuevo = $data['password_nuevo'];
        $passwordConfirmar = $data['password_confirmar'];

        // Buscar el usuario por ID
        $usuario = $model->find($idUsuario);

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Verificar la contraseña actual
        if (!password_verify($passwordActual, $usuario['pass'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'La contraseña actual es incorrecta'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // Verificar si la nueva contraseña y la confirmación coinciden
        if ($passwordNuevo !== $passwordConfirmar) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'La nueva contraseña y la confirmación no coinciden'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Verificar la longitud de la nueva contraseña (opcional, pero recomendado)
        if (strlen($passwordNuevo) < 6) { // Ejemplo: mínimo 6 caracteres
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'La nueva contraseña debe tener al menos 6 caracteres'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Hashear la nueva contraseña
        $passwordHashNuevo = password_hash($passwordNuevo, PASSWORD_DEFAULT);

        // Actualizar la contraseña del usuario
        $dataUpdate = [
            'pass' => $passwordHashNuevo
        ];

        if ($model->update($idUsuario, $dataUpdate)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Contraseña actualizada exitosamente'
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al actualizar la contraseña'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
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
