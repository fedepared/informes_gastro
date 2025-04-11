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

class Informes extends BaseController
{
    private $InformesModel;

    public function __construct()
    {
        $this->InformesModel = new InformesModel();
    }
    function enviarCorreoPrueba()
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'c0170053.ferozo.com';  // Verificá que sea el correcto
            $mail->SMTPAuth   = true;
            $mail->Username   = 'estudio@dianaestrin.com';
            $mail->Password   = '@Wurst2024@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Remitente (debe coincidir con Username para evitar bloqueos)
            $mail->setFrom('estudio@dianaestrin.com', 'Diana Estrin');

            // Destinatario (probá primero con otro que no sea Gmail si sigue sin funcionar)
            $mail->addAddress('agustin.moya.4219@gmail.com', 'Agustin');

            // Cabecera opcional
            $mail->addReplyTo('estudio@dianaestrin.com', 'No responder');

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Correo de prueba desde PHPMailer';
            $mail->Body    = '<b>¡Hola!</b><br>Este es un correo de prueba enviado desde <i>PHPMailer</i> con autenticación SMTP.';
            $mail->AltBody = '¡Hola! Este es un correo de prueba enviado desde PHPMailer con autenticación SMTP.';

            // Enviar
            $mail->send();
            return '✅ Correo de prueba enviado correctamente.';
        } catch (Exception $e) {
            return "❌ Error al enviar el correo: {$mail->ErrorInfo}";
        }
    }

    /**
     * Obtiene todos los informes con coberturas.
     */
    public function getInformes()
    {
        $nombre = $this->request->getGet('nombre');
        $fecha_desde = $this->request->getGet('fecha_desde');
        $fecha_hasta = $this->request->getGet('fecha_hasta');
    
        if ($nombre !== null || $fecha_desde !== null || $fecha_hasta !== null) {
            // Si algún parámetro de filtro está presente, aplicamos el filtro
            $resultado = $this->InformesModel->getInformesWithFiltros($nombre, $fecha_desde, $fecha_hasta);
        } else {
            // Si no hay parámetros de filtro, obtenemos todos los informes con coberturas
            $resultado = $this->InformesModel->getInformesWithCoberturas();
        }
    
        return $this->response->setJSON($resultado);
    }

    /**
     * Obtiene un informe por su ID junto con su cobertura.
     */
    public function getByIdInformes($id)
    {
        $resultado = $this->InformesModel->getInformeByIdWithCobertura($id);
        echo !empty($resultado) ? '<pre>' . print_r($resultado, true) . '</pre>' : 'No se encontró el informe.';
    }

    public function descargarInformeCompleto()
    {
        $rutaRelativa = $this->request->getGet('ruta');

        $rutaCompleta = FCPATH . $rutaRelativa;

        if (!$rutaRelativa || !is_dir($rutaCompleta)) {
            return $this->response->setStatusCode(404)->setBody('Directorio no encontrado.');
        }

        $nombreZip = 'informe_completo_' . time() . '.zip';
        $zipPath = WRITEPATH . $nombreZip;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            return $this->response->setStatusCode(500)->setBody('No se pudo crear el ZIP.');
        }

        $dir = new \RecursiveDirectoryIterator($rutaCompleta);
        $iterator = new \RecursiveIteratorIterator($dir);

        foreach ($iterator as $archivo) {
            if ($archivo->isFile()) {
                $rutaReal = $archivo->getRealPath();

                // Solo se incluye desde la carpeta final
                $rutaInterna = substr($rutaReal, strlen($rutaCompleta) + 1);

                $zip->addFile($rutaReal, $rutaInterna);
            }
        }

        $zip->close();

        return $this->response->download($zipPath, null)->setFileName($nombreZip);
    }





    /**
     * Crea un nuevo informe, genera un PDF y lo envía por correo.
     */

     public function postInforme()
     {
         try {
             echo "<h3>INICIANDO postInforme</h3>";
     
             // Obtener datos del request
             $fecha = $this->request->getPost('fecha');
             $tipoInforme = trim($this->request->getPost('tipo_informe'));
             $nombrePaciente = trim($this->request->getPost('nombre_paciente'));
             $fechaNacimiento = $this->request->getPost('fecha_nacimiento');
             $dniPaciente = trim($this->request->getPost('dni_paciente'));
             $idCobertura = $this->request->getPost('id_cobertura');
             $mailPaciente = trim($this->request->getPost('mail_paciente'));
             $medico = trim($this->request->getPost('medico'));
             $motivo = trim($this->request->getPost('motivo'));
             $informe = trim($this->request->getPost('informe'));
             $estomago = trim($this->request->getPost('estomago'));
             $duodeno = trim($this->request->getPost('duodeno'));
             $esofago = trim($this->request->getPost('esofago'));
             $conclusion = trim($this->request->getPost('conclusion'));
             $terapeutico = trim($this->request->getPost('terapeutico'));
             $cual = trim($this->request->getPost('cual'));
             $biopsia = trim($this->request->getPost('biopsia'));
             $frascos = $this->request->getPost('frascos');
             $edad = $this->request->getPost('edad');
             $afiliado = $this->request->getPost('afiliado');
     
             // Validar datos requeridos
             $requiredFields = ['nombre_paciente', 'dni_paciente', 'fecha', 'mail_paciente', 'tipo_informe', 'id_cobertura'];
             foreach ($requiredFields as $field) {
                 if (empty($this->request->getPost($field))) {
                     echo "Falta el campo: $field<br>";
                 }
             }
     
             // Crear carpetas (aunque no se guarden imágenes ahora)
             $nombreCarpetaBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nombrePaciente));
             $dniCarpeta = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dniPaciente);
             $carpetaPaciente = $nombreCarpetaBase . '_' . $dniCarpeta;
             $uploadPathBasePaciente = FCPATH . 'uploads/' . $carpetaPaciente . '/';
             $carpetaInforme = date('Ymd_His');
             $uploadPathInforme = $uploadPathBasePaciente . $carpetaInforme . '/';
     
             if (!is_dir($uploadPathInforme) && !mkdir($uploadPathInforme, 0777, true)) {
                 echo "No se pudo crear carpeta: $uploadPathInforme";
                 exit;
             }
     
             // Procesar imágenes
             $imagenesBase64 = [];
             $archivos = $this->request->getFileMultiple('archivo');
     
             foreach ($archivos as $archivo) {
                 if ($archivo->isValid() && !$archivo->hasMoved()) {
                     $allowedMimeTypes = ['image/jpeg', 'image/png'];
                     if (!in_array($archivo->getMimeType(), $allowedMimeTypes)) {
                         echo "Tipo de archivo no permitido: " . $archivo->getClientMimeType();
                         exit;
                     }
     
                     $contenido = file_get_contents($archivo->getTempName());
                     $base64 = base64_encode($contenido);
                     $mime = $archivo->getMimeType();
                     $imagenesBase64[] = 'data:' . $mime . ';base64,' . $base64;
                 } else {
                     echo "Error al procesar imagen: " . $archivo->getErrorString();
                     exit;
                 }
             }
     
             // Cobertura
             $coberturaModel = new \App\Models\CoberturasModel();
             $coberturaData = $coberturaModel->find($idCobertura);
     
             if (!$coberturaData) {
                 echo "Cobertura no encontrada para ID: $idCobertura";
                 exit;
             }
     
             $nombreCobertura = $coberturaData['nombre_cobertura'] ?? 'No especificada';
     
             // Datos para PDF
             $dataPdf = [
                 'fecha' => $fecha,
                 'tipo_informe' => $tipoInforme,
                 'nombre_paciente' => $nombrePaciente,
                 'fecha_nacimiento' => $fechaNacimiento,
                 'dni_paciente' => $dniPaciente,
                 'nombre_cobertura' => $nombreCobertura,
                 'mail_paciente' => $mailPaciente,
                 'medico' => $medico,
                 'motivo' => $motivo,
                 'informe' => $informe,
                 'estomago' => $estomago,
                 'duodeno' => $duodeno,
                 'esofago' => $esofago,
                 'conclusion' => $conclusion,
                 'terapeutico' => $terapeutico,
                 'cual' => $cual,
                 'biopsia' => $biopsia,
                 'frascos' => $frascos,
                 'edad' => $edad,
                 'afiliado' => $afiliado,
                 'imagenes' => $imagenesBase64,
             ];
     
             echo "<h3>Datos del PDF</h3>";
             echo "<pre>";
             print_r($dataPdf);
             echo "</pre>";
     
             // Generar PDF
             $pdfFileName = $this->generatePDF($dataPdf, $nombreCobertura, $uploadPathInforme);
             $pdfPath = $uploadPathInforme . $pdfFileName;
             echo "PDF generado en: $pdfPath<br>";
     
             if (!file_exists($pdfPath)) {
                 echo "El PDF no fue generado.";
                 exit;
             }
     
             // Insertar en base de datos
             $this->InformesModel->insert([
                 'nombre_paciente' => $nombrePaciente,
                 'dni_paciente' => $dniPaciente,
                 'fecha' => $fecha,
                 'url_archivo' => 'uploads/' . $carpetaPaciente . '/' . $carpetaInforme,
                 'mail_paciente' => $mailPaciente,
                 'tipo_informe' => $tipoInforme,
                 'id_cobertura' => $idCobertura,
             ]);
     
             // Enviar correo
             $asunto = 'Informe Médico - ' . $tipoInforme . ' - ' . $fecha;
             $mensaje = '<p>Estimado/a ' . $nombrePaciente . ',</p><p>Se adjunta su informe médico.</p>';
             $resultadoEnvio = $this->enviarCorreoPHPMailer($mailPaciente, $asunto, $mensaje, [$pdfPath]);
     
             echo "<h3>Resultado del envío de correo</h3>";
             echo $resultadoEnvio;
             exit;
     
         } catch (\Exception $e) {
             echo "ERROR en postInforme: " . $e->getMessage();
             exit;
         }
     }
     
     

     private function generatePDF($data, $cobertura, $outputPath)
     {
         $dompdf = new \Dompdf\Dompdf();
     
         // Armar bloque HTML de imágenes si existen
         $imagenesHtml = '';
         if (!empty($data['imagenes'])) {
             $imagenesHtml .= "<div class='section'><div class='section-title'>IMÁGENES DEL ESTUDIO</div>";
             foreach ($data['imagenes'] as $imgBase64) {
                 $imagenesHtml .= "<div style='margin: 10px 0; text-align: center;'>
                                     <img src='{$imgBase64}' style='max-width: 450px; max-height: 500px; border: 1px solid #ccc; padding: 4px;'>
                                   </div>";
             }
             $imagenesHtml .= "</div>";
         }
     
         // Armar HTML del PDF
         $html = "
         <html>
         <head>
             <style>
                 body { font-family: Arial, sans-serif; font-size: 12px; }
                 .header-box {
                     border: 2px solid #000;
                     padding: 15px;
                     text-align: center;
                     margin-bottom: 15px;
                 }
                 .header-box h1 { margin: 0; font-size: 20px; }
                 .header-box h2 { margin: 5px 0; font-size: 16px; }
                 .header-box h3 { margin: 5px 0; font-size: 14px; }
                 .section { margin-bottom: 10px; }
                 .section-title { font-weight: bold; margin-top: 10px; font-size: 14px; border-bottom: 1px solid #ccc; }
                 .field { margin: 5px 0; }
                 .footer-box {
                     margin-top: 20px;
                     padding: 10px;
                     border-top: 1px solid #000;
                     font-size: 11px;
                 }
             </style>
         </head>
         <body>
             <div class='header-box'>
                 <h1>CLINICA SANTA ISABEL</h1>
                 <h2>VIDEOENDOSCOPIAS DIGESTIVAS</h2>
                 <h3>DRA ESTRIN DIANA MN 84767 MP 334731</h3>
                 <p>MEDICA ESPECIALISTA EN GASTROENTEROLOGIA, ENDOSCOPIAS DIGESTIVAS DIAGNOSTICAS Y TERAPEUTICAS</p>
                 <p style='font-size:11px;'>
                     <strong>ANESTESIOLOGOS:</strong><br>
                     DR GARCIA ALBERTO DANIEL MN 58499 – DRA GARCIA MACCHI MARIANA – DR GIOVANETTI NICOLAS MN 140504<br>
                     <strong>ASISTENTES:</strong><br>
                     PALACIOS LAURA MN 3909 – POCZTER NADIA MN 8075 – MIRANDA ANDREA MN 10974 – GIRARDI MELISA MN 14342
                 </p>
             </div>
     
             <div class='section'>
                 <div class='field'><strong>FECHA:</strong> {$data['fecha']}</div>
                 <div class='field'><strong>TIPO DE ESTUDIO:</strong> {$data['tipo_informe']}</div>
             </div>
     
             <div class='section'>
                 <div class='section-title'>DATOS DEL PACIENTE</div>
                 <div class='field'><strong>NOMBRE Y APELLIDO:</strong> {$data['nombre_paciente']}</div>
                 <div class='field'><strong>FECHA DE NACIMIENTO:</strong> {$data['fecha_nacimiento']}</div>
                 <div class='field'><strong>EDAD:</strong> {$data['edad']}</div>
                 <div class='field'><strong>DNI:</strong> {$data['dni_paciente']}</div>
                 <div class='field'><strong>COBERTURA:</strong> {$cobertura}</div>
                 <div class='field'><strong>NUMERO DE AFILIADO:</strong> {$data['afiliado']}</div>
                 <div class='field'><strong>MAIL:</strong> {$data['mail_paciente']}</div>
                 <div class='field'><strong>MEDICO SOLICITANTE:</strong> {$data['medico']}</div>
                 <div class='field'><strong>MOTIVO DEL ESTUDIO:</strong> {$data['motivo']}</div>
             </div>
     
             <div class='section'>
                 <div class='section-title'>INFORME</div>
                 <div class='field'><strong>Informe general:</strong> {$data['informe']}</div>
                 <div class='field'><strong>Esófago:</strong> {$data['esofago']}</div>
                 <div class='field'><strong>Estómago:</strong> {$data['estomago']}</div>
                 <div class='field'><strong>Duodeno:</strong> {$data['duodeno']}</div>
             </div>
     
             <div class='section'>
                 <div class='section-title'>CONCLUSIÓN</div>
                 <div class='field'>{$data['conclusion']}</div>
             </div>
     
             <div class='section'>
                 <div class='section-title'>TERAPEUTICA / BIOPSIA</div>
                 <div class='field'><strong>¿Se efectuó terapéutica?</strong> {$data['terapeutico']}</div>
                 <div class='field'><strong>¿Cuál?</strong> {$data['cual']}</div>
                 <div class='field'><strong>¿Se efectuó biopsia?</strong> {$data['biopsia']}</div>
                 <div class='field'><strong>Cantidad de frascos:</strong> {$data['frascos']}</div>
             </div>
     
             <div class='section'>
                 <div class='section-title'>PATOLOGÍA</div>
                 <p><strong>Patóloga:</strong> Dra Polina Angélica. Los resultados se retiran a partir de 15 días hábiles en Clínica Santa Isabel, ingreso por calle Lautaro, 1er piso, consultorios externos, con nombre y apellido del paciente. El trámite no es personal.</p>
             </div>
         
                {$imagenesHtml}

             <div class='section'>
                 <div class='section-title'>INSTRUCCIONES POST ESTUDIO</div>
                 <ol style='font-size: 11px;'>
                     <li>El estudio de colon puede provocar retención de gases... (acá sigue el texto completo que ya incluimos)</li>
                     <li>Debe regresar acompañado a su domicilio...</li>
                     <li>Comience con su dieta habitual...</li>
                     <li>Comience con su medicación habitual...</li>
                     <li>Si se le ha efectuado terapéutica endoscópica...</li>
                     <li>Contactos: Dra Estrin Diana – 1134207000 / dianajudit@hotmail.com</li>
                     <li>Secretaría de Endoscopias: Belén Chapuis – 1151825634 / secretariaendoscopias@gmail.com</li>
                 </ol>
             </div>
     
       
     
             <div class='footer-box'>
                 <p style='text-align:center; font-weight:bold;'>FIRMA DIGITAL Y SELLO</p>
                 <p><strong>IMPORTANTE:</strong> Es imprescindible contar con este informe para la consulta con la Dra Estrin o con su médico enviador.</p>
             </div>
         </body>
         </html>
         ";
     
         // Renderizado y guardado
         $dompdf->loadHtml($html);
         $dompdf->setPaper('A4', 'portrait');
         $dompdf->render();
     
         $nombrePacienteSanitized = preg_replace('/[^A-Za-z0-9]/', '_', strtolower($data['nombre_paciente'] ?? 'sin_nombre'));
         $fechaSanitized = str_replace('-', '_', $data['fecha'] ?? 'sin_fecha');
         $timestamp = time();
         $fileName = "informe_{$fechaSanitized}_{$timestamp}.pdf";
         $filePath = $outputPath . $fileName;
     
         file_put_contents($filePath, $dompdf->output());
     
         return $fileName;
     }
     



     private function enviarCorreoPHPMailer($destinatario, $asunto, $mensaje, $adjuntos = [])
     {
         $mail = new PHPMailer(true);
        
         try {
             echo "<h3>INICIANDO ENVÍO DE CORREO</h3>";
             echo "Destinatario: $destinatario<br>";
             echo "Asunto: $asunto<br>";
             echo "Adjuntos:<br><pre>";
             print_r($adjuntos);
             echo "</pre>";
     
             // Configuración del servidor SMTP
             $mail->isSMTP();
             $mail->Host       = 'c0170053.ferozo.com';
             $mail->SMTPAuth   = true;
             $mail->Username   = 'estudio@dianaestrin.com';
             $mail->Password   = '@Wurst2024@';
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
             $mail->Port       = 465;
             $mail->CharSet = 'UTF-8';
             // Remitente y destinatario
             $mail->setFrom('estudio@dianaestrin.com', 'Estudio Diana Estrin');
             $mail->addAddress($destinatario);
     
             // Contenido del correo
             $mail->isHTML(true);
             $mail->Subject = $asunto;
             $mail->Body    = $mensaje;
     
             // Adjuntos
             foreach ($adjuntos as $adjunto) {
                 if (file_exists($adjunto)) {
                     $mail->addAttachment($adjunto);
                     echo "Adjuntando archivo: $adjunto<br>";
                 } else {
                     echo "⚠️ Archivo no encontrado: $adjunto<br>";
                 }
             }
     
             $mail->send();
             echo "<strong>✅ Correo enviado correctamente.</strong><br>";
             return 'Correo enviado correctamente.';
         } catch (Exception $e) {
             echo "<strong>❌ Error al enviar el correo:</strong> {$mail->ErrorInfo}<br>";
             return "Error al enviar el correo: {$mail->ErrorInfo}";
         }
     }
     

    // ... (función descargarCarpeta) ...



    public function descargarCarpeta()
    {
        $carpetaRelativa = $this->request->getGet('url');

        if (!$carpetaRelativa) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'No se proporcionó la URL de la carpeta.'
            ]);
        }

        $rutaAbsolutaCarpeta = FCPATH . str_replace(['\\', '//'], '/', $carpetaRelativa);

        if (!is_dir($rutaAbsolutaCarpeta)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'La carpeta no se encontró: ' . $rutaAbsolutaCarpeta
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
        $rutaZip = WRITEPATH . 'temp/' . $nombreZip;

        if ($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($archivosParaZip as $archivo) {
                $zip->addFile($archivo, basename($archivo));
            }
            $zip->close();

            // Forzar descarga
            return $this->response
                ->setHeader('Content-Type', 'application/zip')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $nombreZip . '"')
                ->setHeader('Content-Length', filesize($rutaZip))
                ->setBody(file_get_contents($rutaZip));

            // Opcional: eliminar ZIP temporalmente después de servirlo
            // unlink($rutaZip);
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
