<?php

namespace App\Controllers;

use App\Models\InformesModel;
use App\Controllers\Coberturas;
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $coberturaModel = new Coberturas();
        $coberturaData = $coberturaModel->getByIdCoberturas(1);
        
        $nombreCobertura = is_array($coberturaData) ? ($coberturaData['nombre_cobertura'] ?? 'No especificada') : (is_object($coberturaData) ? ($coberturaData->nombre_cobertura ?? 'No especificada') : 'No especificada');
        
        $data = [
            'nombre_paciente' => 'Paciente de prueba',
            'fecha' => '2025-03-22',
            'url_archivo' => 'url',
            'mail_paciente' => 'agustin.moya.4219@gmail.com',
            'id_cobertura' => 1,
        ];

        $this->InformesModel->insert($data);
        print_r($data);
        $pdfPath = $this->generatePDF($data, $nombreCobertura);
    /*     $resultadoCorreo = $this->sendEmailWithPDF($data['mail_paciente'], $pdfPath);
        
        print_r($resultadoCorreo);
        return ['mensaje' => 'Informe creado', 'envio_correo' => $resultadoCorreo]; */
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
                .container { width: 100%; padding: 20px; border: 1px solid #ccc; }
                h2 { text-align: center; color: #007BFF; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid black; padding: 10px; text-align: left; }
                th { background-color: #007BFF; color: white; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Informe del Paciente</h2>
                <table>
                    <tr><th>Campo</th><th>Valor</th></tr>
                    <tr><td>Nombre del Paciente</td><td>{$data['nombre_paciente']}</td></tr>
                    <tr><td>Fecha</td><td>{$data['fecha']}</td></tr>
                    <tr><td>Correo Electrónico</td><td>{$data['mail_paciente']}</td></tr>
                    <tr><td>Cobertura</td><td>{$cobertura}</td></tr>
                </table>
            </div>
        </body>
        </html>";
    
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // Limpiar y formatear los datos para el nombre del archivo
        $nombrePaciente = preg_replace('/[^A-Za-z0-9]/', '_', $data['nombre_paciente']); // Solo letras y números
        $coberturaNombre = preg_replace('/[^A-Za-z0-9]/', '_', $cobertura);
        $fecha = str_replace('-', '_', $data['fecha']); // Asegurar formato válido
        $fileName = "{$nombrePaciente}_{$coberturaNombre}_{$fecha}.pdf";
    
        // Ruta donde se guardará dentro de la app
        $folderPath = __DIR__ . "/../../public/pdfs/";
        
        // Asegurar que la carpeta existe
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    
        // Guardar el archivo en la carpeta
        $filePath = $folderPath . $fileName;
        file_put_contents($filePath, $dompdf->output());
    
        return $filePath; // Retorna la ruta del archivo sin descargarlo automáticamente
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
