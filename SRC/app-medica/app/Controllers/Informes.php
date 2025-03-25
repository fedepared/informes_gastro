<?php

namespace App\Controllers;

use App\Models\InformesModel;
use App\Controllers\Coberturas;

use Dompdf\Dompdf;
use Dompdf\Options;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Informes extends BaseController
{
    private  $InformesModel;
    public function __construct()
    {
        $this->InformesModel = new InformesModel();
    }






    public function getInformes()
    {
        $resultado = $this->InformesModel->getInformesWithCoberturas();

        if (!empty($resultado)) {
            echo '<pre>';
            print_r($resultado);
            echo '</pre>';
        } else {
            echo 'No se encontraron informes.';
        }
    }

    public function getByIdInformes($id)
    {
        $resultado = $this->InformesModel->getInformeByIdWithCobertura($id);

        if (!empty($resultado)) {
            echo '<pre>';
            print_r($resultado);
            echo '</pre>';
        } else {
            echo 'No se encontró el informe.';
        }
    }

    
    public function postInforme()
    {
        // Obtener la cobertura desde el modelo
        $coberturaModel = new Coberturas();
        $coberturaData = $coberturaModel->getByIdCoberturas(5); // Reemplaza con el ID correcto
    
        // Verifica si el resultado es válido y accede correctamente a la propiedad
        if (is_array($coberturaData)) {
            $nombreCobertura = $coberturaData['nombre_cobertura'] ?? 'No especificada';
        } elseif (is_object($coberturaData)) {
            $nombreCobertura = $coberturaData->nombre_cobertura ?? 'No especificada';
        } else {
            $nombreCobertura = 'No especificada';
        }
    
        // Datos para guardar en la base de datos
        $data = [
            'nombre_paciente' => 'Paciente de prueba',
            'fecha' => '2025-03-22',
            'url_archivo' => 'url',
            'mail_paciente' => 'agustin.moya.4219@gmail.com',
            'id_cobertura' => 5, // Solo se guarda el ID como número
        ];
    
        // Guardar en la base de datos
        $this->InformesModel->insert($data);
    
        // Generar el PDF y obtener la ruta del archivo
        $pdfPath = $this->generatePDF($data, $nombreCobertura);
    
        // Enviar el correo con el PDF adjunto y capturar el resultado
        $resultadoCorreo = $this->sendEmailWithPDF($data['mail_paciente'], $pdfPath);
        print_r($resultadoCorreo);
        // Retornar el estado del informe y el envío del correo
        return [
            'mensaje' => 'Informe creado',
            'envio_correo' => $resultadoCorreo
        ];
    }
    
    



    function sendEmailWithPDF($recipientEmail, $pdfPath)
    {
        $mail = new PHPMailer(true);
    
        try {
            // Habilitar la salida de depuración detallada
            $mail->SMTPDebug = 2; // 0 = desactivar depuración, 2 = mensajes detallados
            $mail->Debugoutput = 'html'; // Para mostrar los mensajes en HTML
    
            // Configuración del servidor SMTP de Outlook
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com'; // Servidor SMTP de Outlook
            $mail->SMTPAuth = true;
            $mail->Username = 'agusfull22@hotmail.com'; // Tu correo de Outlook/Hotmail
            $mail->Password = 'Afma0018'; // ⚠ ¡Nunca compartas tu contraseña en público!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Seguridad TLS
            $mail->Port = 587; // Puerto SMTP de Outlook
    
            // Configuración del correo
            $mail->setFrom('agusfull22@hotmail.com', 'Sistema de Informes'); // Remitente
            $mail->addAddress($recipientEmail); // Destinatario
            $mail->Subject = 'Informe generado';
            $mail->Body = 'Adjunto encontrarás el informe en formato PDF.';
            $mail->isHTML(true);
    
            // Adjuntar el PDF
            $mail->addAttachment($pdfPath);
            
            
            // Enviar el correo
            if ($mail->send()) {
                return $res =   "Correo enviado correctamente";
            } else {
                return  $res ="Error en el envío: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            return $res = "Excepción al enviar el correo: {$mail->ErrorInfo}";
        }

        print_r($res);
    }
    

    

    /*
    public function postInforme()
{
    $file = $this->request->getFile('archivo'); // Obtener el archivo enviado

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $newName = $file->getRandomName(); // Genera un nombre aleatorio
        $file->move(ROOTPATH . 'public/uploads/', $newName); // Mover el archivo a 'public/uploads/'

        $urlArchivo = base_url('uploads/' . $newName); // URL accesible desde el navegador
    } else {
        $urlArchivo = null; // En caso de error
    }

    // Guardar en la base de datos
    $data = [
        'nombre_paciente' => $this->request->getPost('nombre_paciente'),
        'fecha' => $this->request->getPost('fecha'),
        'url_archivo' => $urlArchivo,
        'mail_paciente' => $this->request->getPost('mail_paciente'),
        'id_cobertura' => '5',
    ];

    $this->InformesModel->insert($data);

    echo 'Archivo subido correctamente. <br>';
    echo 'Datos guardados: <pre>';
    print_r($data);
    echo '</pre>';
}
 
     */
    private function generatePDF($data, $id)
    {
        $dompdf = new Dompdf();

        // Estilos CSS profesionales
        $css = "
        <style>
            body { font-family: Arial, sans-serif; }
            .container { width: 100%; padding: 20px; border: 1px solid #ccc; }
            h2 { text-align: center; color: #007BFF; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            table, th, td { border: 1px solid black; }
            th, td { padding: 10px; text-align: left; }
            th { background-color: #007BFF; color: white; }
        </style>";

        // Contenido del PDF
        $html = "
        <html>
        <head>$css</head>
        <body>
            <div class='container'>
                <h2>Informe del Paciente</h2>
                <table>
                    <tr>
                        <th>Campo</th>
                        <th>Valor</th>
                    </tr>
                    <tr>
                        <td><b>Nombre del Paciente</b></td>
                        <td>{$data['nombre_paciente']}</td>
                    </tr>
                    <tr>
                        <td><b>Fecha</b></td>
                        <td>{$data['fecha']}</td>
                    </tr>
                    <tr>
                        <td><b>Correo Electrónico</b></td>
                        <td>{$data['mail_paciente']}</td>
                    </tr>
                    <tr>
                        <td><b>Cobertura</b></td>
                        <td>{$id}</td>

                    </tr>
                </table>
            </div>
        </body>
        </html>";

        // Cargar el HTML en Dompdf
        $dompdf->loadHtml($html);

        // Configurar el tamaño del papel y la orientación
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar el PDF
        $dompdf->render();

        // Descargar el archivo
        $dompdf->stream("informe_paciente.pdf", ["Attachment" => true]);

        exit();
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
        /*    print_r($antes); */

        echo 'despues';
        /*     print_r($despues); */
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
        /*      print_r($antes); */

        echo 'despues';
        /*   print_r($despues); */
        echo '</pre>';
    }

    /* 
    public function GenerarPDF($request, $response, $args)
    {
      ob_clean();
      ob_start();
      $lista = Mesa::obtenerTodos();
  
      $pdf = new FPDF();
      $pdf->SetTitle("Lista de Mesas");
      $pdf->AddPage();
      $pdf->SetFont('Arial', 'B', 12); // Establece la fuente
      $pdf->Cell(150, 10, 'Lista Mesas: ', 0, 1);
  
      foreach ($lista as $Mesa) {
        $pdf->SetFont('Arial', '', 12); // Establece la fuente para las filas
        $pdf->Cell(150, 10, Mesa::toString($Mesa));
        $pdf->Ln();
      }
  
      $pdf->Output('F', './archivo/PDFMESAS.pdf', false);
      ob_end_flush();
  
      $payload = json_encode(array("message" => "pdf generado"));
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    } */
}
