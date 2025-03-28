<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\InformesModel;
use App\Controllers\Coberturas;
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use CodeIgniter\HTTP\ResponseInterface;

class Informes extends BaseController
{
    private $InformesModel;

    public function __construct()
    {
        $this->InformesModel = new InformesModel();
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
            $nombrePaciente = trim($this->request->getPost('nombre_paciente')); // Eliminar espacios extra
            $fecha = $this->request->getPost('fecha');
            $mailPaciente = $this->request->getPost('mail_paciente');
            $idCobertura = $this->request->getPost('id_cobertura');

            // Validar datos requeridos
            if (empty($nombrePaciente) || empty($fecha) || empty($mailPaciente) || empty($idCobertura)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Faltan datos requeridos: nombre_paciente, fecha, mail_paciente, id_cobertura'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Sanitizar el nombre del paciente para usarlo como nombre de carpeta
            $nombreCarpeta = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nombrePaciente));

            // Directorio donde se guardarán las imágenes del paciente
            $uploadPath = FCPATH . 'uploads/' . $nombreCarpeta . '/';

            // Si la carpeta no existe, crearla
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Obtener archivos (múltiples)
            $archivos = $this->request->getFileMultiple('archivo');

            if (empty($archivos)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No se enviaron archivos.'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            log_message('info', 'Cantidad de archivos recibidos: ' . count($archivos));

            // Array para almacenar las URLs de los archivos subidos
            $archivosSubidos = [];

            foreach ($archivos as $archivo) {
                if ($archivo->isValid() && !$archivo->hasMoved()) {
                    // Crear nombre de archivo con el formato: nombrePaciente_fecha_originalNombre
                    $nuevoNombre = $nombreCarpeta . '_' . date('Ymd', strtotime($fecha)) . '_' . $archivo->getName();
                    $archivo->move($uploadPath, $nuevoNombre);
                    $archivosSubidos[] = base_url('uploads/' . $nombreCarpeta . '/' . $nuevoNombre);
                    log_message('info', 'Archivo subido: ' . base_url('uploads/' . $nombreCarpeta . '/' . $nuevoNombre));
                } else {
                    log_message('error', 'Error al subir archivo: ' . $archivo->getErrorString());
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

            // Datos para insertar en la BD
            $data = [
                'nombre_paciente' => $nombrePaciente,
                'fecha' => $fecha,
                'url_archivo' => implode(',', $archivosSubidos), // Guardar las URLs separadas por coma
                'mail_paciente' => $mailPaciente,
                'id_cobertura' => $idCobertura,
            ];

            // Insertar en la BD
            $this->InformesModel->insert($data);

            // Generar PDF
            $pdfPath = $this->generatePDF($data, $nombreCobertura);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Informe creado correctamente.',
                'archivos' => $archivosSubidos,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en postInforme: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hubo un error al procesar la solicitud: ' . $e->getMessage(),
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Envía un correo con el informe adjunto en PDF.
     */
    function sendEmailWithPDF($recipientEmail, $pdfPath)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'agusfull22@hotmail.com';
            $mail->Password = 'Afma0018'; // ⚠ ¡Nunca compartas tu contraseña en público!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('agusfull22@hotmail.com', 'Sistema de Informes');
            $mail->addAddress($recipientEmail);
            $mail->Subject = 'Informe generado';
            $mail->Body = 'Adjunto encontrarás el informe en formato PDF.';
            $mail->isHTML(true);
            $mail->addAttachment($pdfPath);

            return $mail->send() ? 'Correo enviado correctamente' : 'Error en el envío: ' . $mail->ErrorInfo;
        } catch (Exception $e) {
            return 'Excepción al enviar el correo: ' . $mail->ErrorInfo;
        }
    }

    private function generatePDF($data, $cobertura)
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
                    <li class='fecha'><strong>Fecha:</strong> {$data['fecha']}</li>
                    <li><strong>Correo Electrónico:</strong> {$data['mail_paciente']}</li>
                    <li><strong>Cobertura:</strong> {$cobertura}</li>
                </ul>
            </div>
        </body>
        </html>";
    
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // 🔹 Crear nombre seguro para la carpeta y archivo
        $nombrePaciente = preg_replace('/[^A-Za-z0-9]/', '_', strtolower($data['nombre_paciente']));
        $coberturaNombre = preg_replace('/[^A-Za-z0-9]/', '_', strtolower($cobertura));
        $fecha = str_replace('-', '_', $data['fecha']);
        $timestamp = time(); // Genera un número único basado en la hora actual
        $fileName = "{$nombrePaciente}_{$coberturaNombre}_{$fecha}_{$timestamp}.pdf";
    
        // 🔹 Ruta de la carpeta del paciente
        $folderPath = __DIR__ . "/../../public/pdfs/{$nombrePaciente}/";
    
        // 🔹 Crear la carpeta si no existe
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    
        // 🔹 Ruta final del archivo
        $filePath = $folderPath . $fileName;
    
        // 🔹 Guardar el PDF sin sobrescribir los anteriores
        file_put_contents($filePath, $dompdf->output());
    
        return $filePath; // Retorna la ruta del archivo
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
        $data = [
            'nombre_paciente' => 'abril',
            'fecha' => '2025-03-22',
            'url_archivo' => 'url',
            'mail_paciente' => 'abril@gmail.com',
            'id_cobertura' => '5',
        ];
        $this->InformesModel->update($id, $data);
        echo 'Informe actualizado';
    }
}
