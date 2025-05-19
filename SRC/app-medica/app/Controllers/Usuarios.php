<?php

namespace App\Controllers;


use CodeIgniter\HTTP\ResponseInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\UsuariosModel;
use Config\Services;
use Config\Session;

use CodeIgniter\Controller; 
use CodeIgniter\API\ResponseTrait; 

class Usuarios extends BaseController
{
    use ResponseTrait; 
    private $UsuariosModel;

    public function __construct()
    {
        $this->UsuariosModel = new UsuariosModel();
    }
    
 public function verificarSesion()
{
   if (!session()->has('id_usuario')) {
        return $this->response->setJSON(['status' => 'expirado']);
    }

    return $this->response->setJSON(['status' => 'activo']);
}


    private function enviarCorreoPHPMailer($to, $subject, $message)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        try {
            $mail->isSMTP();
            $mail->Host       = 'c0170053.ferozo.com';  // Verificá que sea el correcto
            $mail->SMTPAuth   = true;
            $mail->Username   = 'estudio@dianaestrin.com';
            $mail->Password   = '@Wurst2024@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('estudio@dianaestrin.com', 'Estudio Diana Estrin');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            return 'Correo enviado correctamente';
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            return 'Error al enviar el correo: ' . $mail->ErrorInfo;
        }
    }

    public function solicitarCambioPassword()
{
    // Generar el código de verificación aleatorio
    $codigoCambio = random_int(100000, 999999);

    // Datos a actualizar en la base de datos
    $dataUpdate = [
        'pass_aux' => $codigoCambio,
        'pidio_cambio' => true,
        'pass' => password_hash($codigoCambio, PASSWORD_DEFAULT) 
    ];

    // Actualiza el usuario con id_usuario = 1
    if (!$this->UsuariosModel->update(1, $dataUpdate)) {
        return $this->failServerError('Error al guardar el código de verificación.');
    }

    // Recupera el usuario actualizado
    $usuario = $this->UsuariosModel->find(1);

    if (!$usuario) {
        return $this->failNotFound('Usuario no encontrado.');
    }

    $asunto = 'Solicitud de Cambio de Contraseña';
    $mensaje = "
        <p>Hola <strong>{$usuario['nombre_usuario']}</strong>,</p>
        <p>Has solicitado cambiar tu contraseña. Tu código de verificación es:</p>
        <h2>$codigoCambio</h2>
        <p>Utiliza este código para establecer una nueva contraseña.</p>
        <p>Si no solicitaste este cambio, simplemente ignora este correo.</p>
    ";

    // Enviar el correo
    $resultadoCorreo = $this->enviarCorreoPHPMailer($usuario['mail'], $asunto, $mensaje);

    if (strpos($resultadoCorreo, 'Correo enviado correctamente') !== false) {
        return $this->respond([
            'status' => 'success',
            'message' => 'Se ha enviado un código de verificación a tu correo electrónico.',
        ]);
    } else {
        // Revertir cambios si falla el envío
        $this->UsuariosModel->update(1, [
            'pass_aux' => null,
            'pidio_cambio' => false,

        ]);

        return $this->failServerError('Error al enviar el correo: ' . $resultadoCorreo);
    }
}

    
public function verificarYActualizarPassword()
{
    $model = new UsuariosModel();
    $data = $this->request->getJSON(true);

    // Validar datos de entrada
    if (
        !$data ||
        !isset($data['mail']) ||
        !isset($data['codigo']) ||
        !isset($data['password_nuevo'])
    ) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Se requieren el correo, el código y la nueva contraseña.'
        ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    $mail = trim($data['mail']);
    $codigo = trim($data['codigo']);
    $passwordNuevo = trim($data['password_nuevo']);

    // Buscar al usuario (asumimos ID 1 por ahora)
    $usuario = $model->find(1);

    if (
        !$usuario ||
        $usuario['mail'] !== $mail ||
        $usuario['pass_aux'] !== $codigo ||
        !$usuario['pidio_cambio']
    ) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Datos inválidos o el código ha expirado.'
        ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
    }

    // Actualizar la contraseña
    $passwordHashNuevo = password_hash($passwordNuevo, PASSWORD_DEFAULT);
    $dataUpdate = [
        'pass' => $passwordHashNuevo,
        'pass_aux' => null,
        'pidio_cambio' => false,
    ];

    if ($model->update($usuario['id_usuario'], $dataUpdate)) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Contraseña actualizada exitosamente.'
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Error al actualizar la contraseña en la base de datos.'
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }
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
        if (!isset($data['id_usuario']) || !isset($data['password_nuevo']) || !isset($data['password_confirmar'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos requeridos: id_usuario, password_nuevo, password_confirmar'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $idUsuario = $data['id_usuario'];
        // $passwordActual = $data['password_actual'];
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


        // Verificar si la nueva contraseña y la confirmación coinciden
        if ($passwordNuevo !== $passwordConfirmar) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'La nueva contraseña y la confirmación no coinciden'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

    
        // Hashear la nueva contraseña
        $passwordHashNuevo = password_hash($passwordNuevo, PASSWORD_DEFAULT);

        // Actualizar la contraseña del usuario
        $dataUpdate = [
            'pass' => $passwordHashNuevo,
            'pass_aux' => null,
            'pidio_cambio' => false,
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


        
        //logica token carpetas usadas , config ->filters  y Filters -> authGuard
        \Config\Services::session()->set([
            'usuario_logueado' => true, // esta es la clave que revisa el filtro
            'id_usuario' => $usuario['id_usuario'],
            'nombre_usuario' => $usuario['nombre_usuario'],
            
        ]);
        // Fin logica token        

        $sessionExpiration =  config('Session')->expiration;
        session()->set('expiracion', time() + $sessionExpiration);
        // Si las credenciales son correctas, devolver una respuesta de éxito
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'id' => $usuario['id_usuario'],
                'nombre_usuario' => $usuario['nombre_usuario'],
                'mail' => $usuario['mail'],
                'pidio_cambio' => $usuario['pidio_cambio'],
                'expiracion' => time() + $sessionExpiration 
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
    

    public function logout()
    {
        session()->destroy();
        
        return redirect()->to('/login');
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
