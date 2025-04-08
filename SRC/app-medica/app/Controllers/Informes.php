<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\InformesModel;
use App\Controllers\Coberturas;
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use CodeIgniter\HTTP\ResponseInterface;
use ZipArchive;


use PHPMailer\PHPMailer\SMTP;


class Informes extends BaseController
{
    private $InformesModel;

    public function __construct()
    {
        $this->InformesModel = new InformesModel();
    }

    public function enviarCorreoPHPMailerPruebaGmail()
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del Servidor SMTP para Gmail
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Para ver la comunicación detallada con el servidor
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';  // Servidor SMTP de Gmail
            $mail->SMTPAuth   = true;
            $mail->Username   = 'agustin.moya.4219@gmail.com'; // Tu dirección de correo electrónico de Gmail
            $mail->Password   = 'zvij awxq gerx zxuv';    // **¡¡¡COLOCA AQUÍ TU CONTRASEÑA DE GMAIL O CONTRASEÑA DE APLICACIÓN!!!**
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS
            $mail->Port       = 587;                // Puerto TCP para TLS

            // Configuración del Remitente y Destinatario
            $mail->setFrom('agustin.moya.4219@gmail.com', 'Agustín Moya');
            $mail->addAddress('agusfull22@hotmail.com');     // Agregar destinatario

            // Contenido del Correo
            $mail->isHTML(true);               // Establecer el formato del correo electrónico en HTML
            $mail->Subject = 'Prueba de correo con PHPMailer (Desde Gmail)';
            $mail->Body    = '<p>Hola,</p><p>Este es un correo de prueba enviado con PHPMailer desde mi cuenta de Gmail.</p>';
            $mail->AltBody = 'Hola, Este es un correo de prueba enviado con PHPMailer desde mi cuenta de Gmail.'; // Cuerpo de texto plano

            $mail->send();
            return $this->response->setBody("✅ Correo de prueba enviado correctamente con PHPMailer desde Gmail."); // Texto plano
            // O si prefieres JSON:
            // return $this->response->setJSON(['status' => 'success', 'message' => 'Correo de prueba enviado correctamente con PHPMailer desde Gmail.']);

        } catch (Exception $e) {
            return $this->response->setStatusCode(500)->setBody("❌ Error al enviar el correo con PHPMailer desde Gmail: {$mail->ErrorInfo}"); // Texto plano con código de error
            // O si prefieres JSON:
            // return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al enviar el correo con PHPMailer desde Gmail: ' . $mail->ErrorInfo]);
        }
    }

    /**
     * Obtiene todos los informes con coberturas.
     */
    public function getInformes()
    {
        $resultado = $this->InformesModel->getInformesWithCoberturas();
        echo !empty($resultado) ? '<pre>' . print_r($resultado, true) . '</pre>' : 'No se encontraron informes.';
    }

    /**
     * Obtiene un informe por su ID junto con su cobertura.
     */
    public function getByIdInformes($id)
    {
        $resultado = $this->InformesModel->getInformeByIdWithCobertura($id);
        echo !empty($resultado) ? '<pre>' . print_r($resultado, true) . '</pre>' : 'No se encontró el informe.';
    }

    /**
     * Crea un nuevo informe, genera un PDF y lo envía por correo.
     */

    public function postInforme()
    {
        try {
            log_message('info', 'Iniciando postInforme');

            // Obtener datos del request
            $nombrePaciente = trim($this->request->getPost('nombre_paciente'));
            $dniPaciente = trim($this->request->getPost('dni_paciente'));
            $fecha = $this->request->getPost('fecha');
            $mailPaciente = $this->request->getPost('mail_paciente');
            $tipoInforme = trim($this->request->getPost('tipo_informe'));
            $idCobertura = $this->request->getPost('id_cobertura');

            // Validar datos requeridos
            $requiredFields = [
                'nombre_paciente',
                'dni_paciente',
                'fecha',
                'mail_paciente',
                'tipo_informe',
                'id_cobertura'
            ];
            foreach ($requiredFields as $field) {
                if (empty($this->request->getPost($field))) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Faltan datos requeridos: ' . implode(', ', $requiredFields)
                    ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
                }
            }

            // Carpeta principal del paciente
            $nombreCarpetaBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nombrePaciente));
            $dniCarpeta = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dniPaciente);
            $carpetaPaciente = $nombreCarpetaBase . '_' . $dniCarpeta;
            $uploadPathBasePaciente = FCPATH . 'uploads/' . $carpetaPaciente . '/';

            // Subcarpeta para el informe actual (fecha y hora)
            $carpetaInforme = date('Ymd_His');
            $uploadPathInforme = $uploadPathBasePaciente . $carpetaInforme . '/';

            // Crear la carpeta del paciente si no existe
            if (!is_dir($uploadPathBasePaciente)) {
                if (!mkdir($uploadPathBasePaciente, 0777, true)) {
                    log_message('error', 'Error al crear el directorio del paciente: ' . $uploadPathBasePaciente);
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Error al crear el directorio para el paciente.'
                    ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            // Crear la subcarpeta del informe si no existe
            if (!is_dir($uploadPathInforme)) {
                if (!mkdir($uploadPathInforme, 0777, true)) {
                    log_message('error', 'Error al crear el directorio del informe: ' . $uploadPathInforme);
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Error al crear el directorio para el informe.'
                    ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            $archivosSubidos = [];
            $archivos = $this->request->getFileMultiple('archivo');

            foreach ($archivos as $archivo) {
                if ($archivo->isValid() && !$archivo->hasMoved()) {
                    $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                    if (!in_array($archivo->getMimeType(), $allowedMimeTypes)) {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => 'Tipo de archivo no permitido: ' . $archivo->getClientMimeType() . '. Solo se permiten: ' . implode(', ', $allowedMimeTypes)
                        ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
                    }
                    $nuevoNombre = $archivo->getName();
                    $archivo->move($uploadPathInforme, $nuevoNombre);
                    $archivosSubidos[] = base_url('uploads/' . $carpetaPaciente . '/' . $carpetaInforme . '/' . $nuevoNombre);
                    log_message('info', 'Archivo subido: ' . base_url('uploads/' . $carpetaPaciente . '/' . $carpetaInforme . '/' . $nuevoNombre));
                } else {
                    log_message('error', 'Error al subir archivo: ' . $archivo->getErrorString());
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Error al subir uno de los archivos.'
                    ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            // Obtener el nombre de la cobertura
            $coberturaModel = new \App\Models\CoberturasModel();
            $coberturaData = $coberturaModel->find($idCobertura);

            if (!$coberturaData) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cobertura no encontrada.'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $nombreCobertura = $coberturaData['nombre_cobertura'] ?? 'No especificada';

            // Datos para el PDF
            $data = [
                'nombre_paciente' => $nombrePaciente,
                'dni_paciente' => $dniPaciente,
                'fecha' => $fecha,
                'mail_paciente' => $mailPaciente,
                'tipo_informe' => $tipoInforme,

                'nombre_cobertura' => $nombreCobertura,
            ];

            // Generar PDF y obtener la ruta dentro de la carpeta del informe
            $pdfFileName = $this->generatePDF($data, $nombreCobertura, $uploadPathInforme);
            $pdfRelativePath = 'uploads/' . $carpetaPaciente . '/' . $carpetaInforme . '/' . $pdfFileName;

            // Datos para insertar en la BD
            $data = [
                'nombre_paciente' => $nombrePaciente,
                'dni_paciente' => $dniPaciente,
                'fecha' => $fecha,
                'url_archivo' => $pdfRelativePath, // Guardar la ruta del PDF como archivo principal
                'mail_paciente' => $mailPaciente,
                'tipo_informe' => $tipoInforme,
                'id_cobertura' => $idCobertura,
            ];

            // Insertar en la BD
            $this->InformesModel->insert($data);
            $idInformeInsertado = $this->InformesModel->insertID(); // Obtener el ID del informe insertado

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Informe creado correctamente.',
                'id_informe' => $idInformeInsertado,
                'archivos' => $archivosSubidos, // Puedes devolver esto o la ruta de la carpeta principal
                'pdf_path' => base_url($pdfRelativePath),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en postInforme: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hubo un error al procesar la solicitud: ' . $e->getMessage(),
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function generatePDF($data, $cobertura, $outputPath)
    {
        $dompdf = new Dompdf();

        $html = "
         <html>
         <head>
             <style>
                 body { font-family: Arial, sans-serif; }
                 .container { width: 100%; padding: 20px; }
                 h2 { text-align: center; }
                 ul { list-style-type: none; padding: 0; }
                 li { padding: 5px 0; }
                 .fecha { font-weight: bold; font-size: 18px; }
             </style>
         </head>
         <body>
             <div class='container'>
                 <h2>Informe del Paciente</h2>
                 <ul>
                     <li><strong>Nombre del Paciente:</strong> {$data['nombre_paciente']}</li>
                     <li><strong>DNI del Paciente:</strong> {$data['dni_paciente']}</li>
                     <li class='fecha'><strong>Fecha:</strong> {$data['fecha']}</li>
                     <li><strong>Correo Electrónico:</strong> {$data['mail_paciente']}</li>
                     <li><strong>Tipo de Informe:</strong> {$data['tipo_informe']}</li>
                     <li><strong>Cobertura:</strong> {$cobertura}</li>
                 </ul>
             </div>
         </body>
         </html>";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nombrePacienteSanitized = preg_replace('/[^A-Za-z0-9]/', '_', strtolower($data['nombre_paciente']));
        $dniPacienteSanitized = preg_replace('/[^A-Za-z0-9]/', '_', $data['dni_paciente']);
        $fechaSanitized = str_replace('-', '_', $data['fecha']);
        $timestamp = time();
        $fileName = "informe_{$fechaSanitized}_{$timestamp}.pdf";
        $filePath = $outputPath . $fileName;

        file_put_contents($filePath, $dompdf->output());

        return $fileName; // Retornar solo el nombre del archivo
    }

    public function descargarCarpeta()
    {
        $carpetaRelativa = $this->request->getGet('url');

        if (!$carpetaRelativa) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'No se proporcionó la URL de la carpeta.'
            ]);
        }

        $rutaAbsolutaCarpeta = FCPATH . str_replace('\\', '/', $carpetaRelativa);

        if (!is_dir($rutaAbsolutaCarpeta)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'La carpeta no se encontró.'
            ]);
        }

        $archivos = scandir($rutaAbsolutaCarpeta);
        $archivosParaZip = [];

        foreach ($archivos as $archivo) {
            if ($archivo !== '.' && $archivo !== '..') {
                $archivosParaZip[] = $rutaAbsolutaCarpeta . '/' . $archivo;
            }
        }

        if (empty($archivosParaZip)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'La carpeta está vacía.'
            ]);
        }

        $zip = new ZipArchive();
        $nombreZip = basename(dirname($carpetaRelativa)) . '_' . basename($carpetaRelativa) . '_archivos.zip';
        $rutaZip = WRITEPATH . 'temp/' . $nombreZip; // Usar un directorio temporal

        if ($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($archivosParaZip as $archivo) {
                $zip->addFile($archivo, basename($archivo));
            }
            $zip->close();

            // Forzar la descarga del ZIP
            $this->response->setHeader('Content-Type', 'application/zip');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $nombreZip . '"');
            $this->response->setHeader('Content-Length', filesize($rutaZip));
            readfile($rutaZip);

            // Eliminar el archivo ZIP temporal (opcional)
            unlink($rutaZip);

            return $this->response;
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error al crear el archivo ZIP.'
            ]);
        }
    }




    /**
     * Elimina un informe por su ID.
     */
    public function deleteInforme($id)
    {
        $this->InformesModel->delete($id);
        echo 'Informe eliminado';
    }

    /**
     * Actualiza un informe existente.
     */
    public function updateInforme($id)
    {
        $data = $this->request->getJSON(true);

        // Verificar si se recibieron datos
        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se recibieron datos para actualizar'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Validar que al menos un campo para actualizar esté presente
        if (empty($data)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se proporcionaron campos para actualizar'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Crear array con los datos a actualizar, solo incluyendo los que se reciben
        $updateData = [];
        if (isset($data['nombre_paciente'])) {
            $updateData['nombre_paciente'] = trim($data['nombre_paciente']);
        }
        if (isset($data['dni_paciente'])) {
            $updateData['dni_paciente'] = trim($data['dni_paciente']);
        }
        if (isset($data['fecha'])) {
            $updateData['fecha'] = $data['fecha'];
        }
        if (isset($data['url_archivo'])) {
            $updateData['url_archivo'] = $data['url_archivo'];
        }
        if (isset($data['mail_paciente'])) {
            $updateData['mail_paciente'] = $data['mail_paciente'];
        }
        if (isset($data['tipo_informe'])) {
            $updateData['tipo_informe'] = trim($data['tipo_informe']);
        }
        if (isset($data['id_cobertura'])) {
            $updateData['id_cobertura'] = $data['id_cobertura'];
        }

        // Si no hay datos válidos para actualizar, retornar un error
        if (empty($updateData)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se proporcionaron datos válidos para actualizar'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Intentar actualizar el informe
        $informeModel = new \App\Models\InformesModel();
        $informe = $informeModel->find($id);

        if (!$informe) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Informe no encontrado con ID: ' . $id
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        if ($informeModel->update($id, $updateData)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Informe actualizado exitosamente',
                'data' => $updateData
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al actualizar el informe',
                'errors' => $informeModel->errors() // Puedes devolver los errores de validación si los tienes configurados en el modelo
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function descargarArchivo()
    {
        $urlRelativa = $this->request->getGet('url');

        if (!$urlRelativa) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'No se proporcionó la URL del archivo.'
            ]);
        }

        // Construir la ruta completa al archivo
        $rutaCompleta = FCPATH . str_replace('\\', '/', $urlRelativa);

        // Verificar si el archivo existe
        if (!file_exists($rutaCompleta)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'El archivo no se encontró.'
            ]);
        }

        // Obtener información del archivo
        $nombreArchivo = basename($rutaCompleta);
        $mime = mime_content_type($rutaCompleta);

        // Preparar la respuesta para la descarga
        $this->response->setHeader('Content-Type', $mime);
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');
        $this->response->setHeader('Content-Length', filesize($rutaCompleta));
        readfile($rutaCompleta);
        return $this->response;
        /* http://localhost:8080/descargar-archivo?url=pdfs\agus_123456\agus_123456_iosfa_2025_03_22_1744038309.pdf */
    }
}
