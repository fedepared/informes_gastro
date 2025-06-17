<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\InformesModel;
use App\Controllers\Coberturas;
use App\Models\CoberturasModel;
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use CodeIgniter\HTTP\ResponseInterface;
use ZipArchive;

class Informes extends BaseController
{
    protected $InformesModel;
    protected $CoberturasModel;

    public function __construct()
    {
        $this->InformesModel = new InformesModel();
        $this->CoberturasModel = new CoberturasModel();
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

    public function getInformesPaginado()
    {
        $nombre = $this->request->getGet('nombre');
        $fecha_desde = $this->request->getGet('fecha_desde');
        $fecha_hasta = $this->request->getGet('fecha_hasta');

        $page = (int) $this->request->getGet('page') ?: 1;
        $perPage = (int) $this->request->getGet('per_page') ?: 10;
        $offset = ($page - 1) * $perPage;

        // Obtener resultados y total de registros filtrados
        $resultado = $this->InformesModel->getInformesPaginado($nombre, $fecha_desde, $fecha_hasta, $perPage, $offset);
        $total = $this->InformesModel->countInformesFiltrados($nombre, $fecha_desde, $fecha_hasta);
        $totalPaginas = ceil($total / $perPage);

        return $this->response->setJSON([
            'data' => $resultado,
            'meta' => [
                'pagina_actual' => $page,
                'por_pagina' => $perPage,
                'total_paginas' => $totalPaginas,
                'total_registros' => $total,
            ]
        ]);
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
        echo "FCPATH: " . FCPATH . "<br>";

        $rutaRelativa = $this->request->getGet('ruta');
        echo "Ruta GET: " . $rutaRelativa . "<br>";

        log_message('debug', 'Ruta recibida: ' . $rutaRelativa);

        if (empty($rutaRelativa)) {
            log_message('error', 'Ruta no proporcionada');
            return $this->response->setStatusCode(400)->setBody('Falta la ruta del archivo');
        }

        $rutaSegura = str_replace(['..', './', '\\'], '', $rutaRelativa);
        $rutaCompleta = FCPATH . $rutaSegura;
        echo "Ruta completa generada: " . $rutaCompleta . "<br>";

        if (!file_exists($rutaCompleta)) {
            log_message('error', 'El archivo no existe en: ' . $rutaCompleta);
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado: ' . $rutaSegura);
        }

        $nombreArchivo = basename($rutaCompleta);

        try {
            return $this->response->download($rutaCompleta, null)->setFileName($nombreArchivo);
        } catch (\Exception $e) {
            log_message('critical', 'Error en descarga: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Error al descargar el archivo');
        }
    }




    /**
     * Crea un nuevo informe, genera un PDF y lo envía por correo.
     */
    public function postInforme()
    {
        try {
            // Obtener datos del request
            $nombrePaciente = trim($this->request->getPost('nombre_paciente')); //
            $fecha = $this->request->getPost('fecha'); //
            $tipoInforme = trim($this->request->getPost('tipo_informe')); //
            $mailPaciente = trim($this->request->getPost('mail_paciente')); //
            $dniPaciente = trim($this->request->getPost('dni_paciente')); //
            $idCobertura = $this->request->getPost('id_cobertura'); //
            $fechaNacimiento = $this->request->getPost('fecha_nacimiento_paciente');
            $afiliado = $this->request->getPost('numero_afiliado');
            $medico = trim($this->request->getPost('medico_envia_estudio'));
            $motivo = trim($this->request->getPost('motivo_estudio'));
            $estomago = trim($this->request->getPost('estomago'));
            $duodeno = trim($this->request->getPost('duodeno'));
            $esofago = trim($this->request->getPost('esofago'));
            $conclusion = trim($this->request->getPost('conclusion'));
            $terapeutico = trim($this->request->getPost('efectuo_terapeutica'));
            $cual = trim($this->request->getPost('tipo_terapeutica'));
            $biopsia = trim($this->request->getPost('efectuo_biopsia'));
            $frascos = $this->request->getPost('fracos_biopsia');
            $informe = trim($this->request->getPost('informe'));
            $edad = $this->request->getPost('edad');



            // Validar campos obligatorios
            $requiredFields = ['nombre_paciente', 'dni_paciente', 'fecha', 'mail_paciente', 'tipo_informe', 'id_cobertura'];
            foreach ($requiredFields as $field) {
                if (empty($this->request->getPost($field))) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Falta el campo: ' . $field]);
                }
            }

            // Crear carpeta base del paciente
            $nombreCarpetaBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nombrePaciente));
            $dniCarpeta = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dniPaciente);
            $carpetaPaciente = $nombreCarpetaBase . '_' . $dniCarpeta;
            $uploadPathBasePaciente = FCPATH . 'uploads/' . $carpetaPaciente . '/';

            // Crear la carpeta del paciente si no existe
            if (!is_dir($uploadPathBasePaciente) && !mkdir($uploadPathBasePaciente, 0777, true)) {
                return $this->response->setJSON(['success' => false, 'message' => 'No se pudo crear carpeta del paciente.']);
            }

            // Crear carpeta del informe con fecha y hora
            $carpetaInforme = date('Ymd_His');
            $uploadPathInforme = $uploadPathBasePaciente . $carpetaInforme . '/';

            if (!mkdir($uploadPathInforme, 0777, true)) {
                return $this->response->setJSON(['success' => false, 'message' => 'No se pudo crear carpeta para el informe.']);
            }

            // Procesar imágenes
            $imagenesBase64 = [];
            $archivos = $this->request->getFileMultiple('archivo');
            if ($archivos === null) {
                $archivos = []; // Si no hay archivos, inicializa como un array vacío para evitar el error de foreach
            }
            foreach ($archivos as $archivo) {
                if ($archivo->isValid() && !$archivo->hasMoved()) {
                    $allowedMimeTypes = ['image/jpeg', 'image/png'];
                    if (!in_array($archivo->getMimeType(), $allowedMimeTypes)) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Tipo de archivo no permitido: ' . $archivo->getClientMimeType()]);
                    }
                    $contenido = file_get_contents($archivo->getTempName());
                    $base64 = base64_encode($contenido);
                    $mime = $archivo->getMimeType();
                    $imagenesBase64[] = 'data:' . $mime . ';base64,' . $base64;
                } elseif ($archivo->getError() !== UPLOAD_ERR_NO_FILE) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Error al procesar imagen: ' . $archivo->getErrorString()]);
                }
            }

            // Obtener nombre de la cobertura
            $coberturaData = $this->CoberturasModel->find($idCobertura);
            if (!$coberturaData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Cobertura no encontrada para ID: ' . $idCobertura]);
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

            // Generar el PDF
            $pdfFileName = $this->generatePDF($dataPdf, $nombreCobertura, $uploadPathInforme);
            $pdfPath = $uploadPathInforme . $pdfFileName;

            if (!file_exists($pdfPath)) {
                return $this->response->setJSON(['success' => false, 'message' => 'El PDF no fue generado.']);
            }

            // Guardar en base de datos
            $this->InformesModel->insert([
                'nombre_paciente' => $nombrePaciente,
                'fecha' => $fecha,
                'tipo_informe' => $tipoInforme,
                'mail_paciente' => $mailPaciente,
                'dni_paciente' => $dniPaciente,
                'url_archivo' => 'uploads/' . $carpetaPaciente . '/' . $carpetaInforme . '/' . $pdfFileName,
                'id_cobertura' => $idCobertura,
                'fecha_nacimiento_paciente' => $fechaNacimiento,
                'numero_afiliado' => $afiliado,
                'medico_envia_estudio' => $medico,
                'motivo_estudio' => $motivo,
                'estomago' => $estomago,
                'duodeno' => $duodeno,
                'esofago' => $esofago,
                'conclusion' => $conclusion,
                'efectuo_terapeutica' => $terapeutico,
                'tipo_terapeutica' => $cual,
                'efectuo_biopsia'  => $biopsia,
                'fracos_biopsia' => $frascos,
                'informe' => $informe,
                'edad' => $edad,
            ]);
            $fechaFormateada = \DateTime::createFromFormat('Y-m-d', $fecha)->format('d-m-Y');


            // Enviar correo
            $asunto = 'Informe Médico - ' . $tipoInforme . ' - ' . $fechaFormateada;
            $mensaje = '<p>Estimado/a ' . $nombrePaciente . ',</p><p>Se adjunta su informe médico.</p>';
            $resultadoEnvio = $this->enviarCorreoPHPMailer($mailPaciente, $asunto, $mensaje, [$pdfPath]);

            if ($resultadoEnvio['success']) {
                return $this->response->setJSON([ 'data_post' => $this->request->getPost(), 'success' => true, 'message' => 'Informe guardado y correo enviado correctamente.']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Informe guardado, pero hubo un error al enviar el correo: ' . $resultadoEnvio['message']]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['data_files' => $archivo, 'data_post' => $this->request->getPost(), 'success' => false, 'message' => 'Error en postInforme: ' . $e->getMessage()]);
        }
    }


    private function generatePDF($data, $cobertura, $outputPath)
    {
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);

        $logo = base_url('images/logo.png');
        $firma = base_url('images/firma.png');

        $imagenesHtml = '';
        if (!empty($data['imagenes'])) {
            $imagenesHtml .= "<div class='section'><div class='section-title1'><br><br>IMÁGENES DEL ESTUDIO</div>";
            foreach ($data['imagenes'] as $imgBase64) {
                $imagenesHtml .= "<div style='margin: 10px 0; text-align: center;'>
                <img src='{$imgBase64}' style='max-width: 450px; max-height: 500px; border: 1px solid #ccc; padding: 4px;'>
            </div>";
            }
            $imagenesHtml .= "</div>";
        }

        // Armamos la sección de PATOLOGÍA solo si biopsia es "SI"
        $patologiaHtml = '';
        if (strtoupper(trim($data['biopsia'])) === 'SI') {
            $patologiaHtml = "
        <div class='section'>
            <div class='section-title1'>PATOLOGÍA</div>
            <p><strong>Patóloga: Dra Polina Angélica.</strong> Resultados disponibles a partir de 15 días hábiles en Clínica Santa Isabel. Ingreso por calle Lautaro, 1er piso. No es trámite personal.</p>
        </div>";
        }

        $html = "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; width: 100%; }
        .header-box { display: table; width: 100%; border: 2px solid #007bff; table-layout: fixed; padding: 20px; height: 10rem; }
        .header-logo { display: table-cell; width: 15%; text-align: left; vertical-align: top; position: relative; height: 10rem; }
        .header-info { margin-top:40px; display: table-cell; width: 85%; text-align: center; vertical-align: middle; font-size: 11px; }
        .logo-img { margin-top: 10px; width: 50px; height: auto; }
        .logo-caption { position: absolute; bottom: 0; left: 0; font-size: 8px; color: #888; text-align: left; line-height: 1.2; width: 100px; }
        .titulo-principal { font-size: 20px; text-align: center; font-weight: bold; margin: 20px 0; text-decoration: underline; }
        .section { margin-bottom: 15px; }
        .section-title, .section-title1 { font-size: 15px; font-weight: bold; margin-top: 10px; margin-bottom: 5px; text-decoration: underline; }
        .section-title { color: #004085; }
        .section-title1 { color: black; }
        .field { margin: 5px 0; }
        .instrucciones { color: red; font-size: 13px; font-weight: bold; margin-top: 15px; }
        .footer-box { margin-top: 30px; padding-top: 10px; border-top: 1px solid #000; font-size: 11px; text-align: center; }
        .doctor-name { color: #004085; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class='header-box'>
        <div class='header-logo'>
            <img src='{$logo}' class='logo-img' alt='Logo Clínica Santa Isabel'>
            <div class='logo-caption'>
                CLÍNICA SANTA ISABEL<br>
                VIDEOENDOSCOPIAS DIGESTIVAS
            </div>
        </div>
        <div class='header-info'>
            <div class='doctor-name'>DRA. ESTRIN DIANA</div>
            MN 84767 MP 334731<br>
            MEDICA ESPECIALISTA EN GASTROENTEROLOGIA, ENDOSCOPIAS DIGESTIVAS DIAGNOSTICAS Y TERAPEUTICAS<br>
            <strong>ANESTESIOLOGOS:</strong><br>
            DR GARCIA ALBERTO DANIEL MN 58499 – DRA GARCIA MACCHI MARIANA – <br> DR GIOVANETTI NICOLAS MN 140504<br>
            <strong>ASISTENTES:</strong><br>
            PALACIOS LAURA MN 3909 – POCZTER NADIA MN 8075 – MIRANDA ANDREA MN 10974 
        </div>
    </div>

    <div class='titulo-principal'><br>INFORME MÉDICO<br><br></div>

    <div class='section'>
        <div class='field'><strong>FECHA:</strong> " . date('d/m/Y', strtotime($data['fecha'])) . "</div>
        <div class='field'><strong>TIPO DE ESTUDIO:</strong> {$data['tipo_informe']}</div>
    </div>

    <div class='section'>
        <div class='section-title1'>DATOS DEL PACIENTE</div>
        <div class='field'><strong>NOMBRE:</strong> {$data['nombre_paciente']}</div>
        <div class='field'><strong>FECHA DE NACIMIENTO:</strong>" . date('d/m/Y', strtotime($data['fecha_nacimiento'])) . " </div>
        <div class='field'><strong>EDAD:</strong> {$data['edad']}</div>
        <div class='field'><strong>DNI:</strong> {$data['dni_paciente']}</div>
        <div class='field'><strong>COBERTURA:</strong> {$cobertura}</div>
        <div class='field'><strong>AFILIADO N°:</strong> {$data['afiliado']}</div>
        <div class='field'><strong>MAIL:</strong> {$data['mail_paciente']}</div>
        <div class='field'><strong>MÉDICO SOLICITANTE:</strong> {$data['medico']}</div>
        <div class='field'><strong>MOTIVO DEL ESTUDIO:</strong> {$data['motivo']}</div>
    </div>

    <div class='section'>
        <div class='section-title'>INFORME</div>" .
            (strtoupper($data['tipo_informe']) === 'VEDA' ? "
            <div class='field'><strong>Esófago:</strong> {$data['esofago']}</div>
            <div class='field'><strong>Estómago:</strong> {$data['estomago']}</div>
            <div class='field'><strong>Duodeno:</strong> {$data['duodeno']}</div>" :
                "<div class='field'><strong>Informe general:</strong> {$data['informe']}</div>") .
            "</div>

    <div class='section'>
        <div class='section-title'>CONCLUSIÓN</div>
        <div class='field'>{$data['conclusion']}</div>
    </div>

    <div class='section'>
        <div class='section-title1'>TERAPÉUTICA Y BIOPSIA</div>
        <div class='field'><strong>¿Se efectuó terapéutica?:</strong> {$data['terapeutico']}</div>
        <div class='field'><strong>¿Cuál?:</strong> {$data['cual']}</div>
        <div class='field'><strong>¿Se efectuó biopsia?:</strong> {$data['biopsia']}</div>
        <div class='field'><strong>Frascos:</strong> {$data['frascos']}</div>
    </div>

    {$patologiaHtml}
    <br><br>
    {$imagenesHtml}

    <div class='instrucciones'><br><br>INSTRUCCIONES POSTERIORES AL ESTUDIO</div>
    <ol style='font-size: 11px;'>
        <li>El estudio de colon puede provocar retención de gases...</li>
        <li>Debe regresar acompañado a su domicilio...</li>
        <li>Comience con su dieta habitual...</li>
        <li>Comience con su medicación habitual...</li>
        <li>Si se le ha efectuado terapéutica endoscópica...</li>
    </ol>

    <div class='section'>
        <div class='section-title1'>CONTACTOS</div>
        <ul style='font-size: 11px;'>
            <li>Dra Estrin Diana – 1134207000 – dianajudit@hotmail.com</li>
            <li>Secretaría – Belén Chapuis – 1151825634 – secretariaendoscopias@gmail.com</li>
        </ul>
    </div>

    <div class='footer-box'>
        <img src='{$firma}' style='width: 150px;' alt='Firma digital'><br>
        <p><strong>FIRMA DIGITAL Y SELLO</strong></p>
        <p><strong class='instrucciones'>IMPORTANTE:</strong> Lleve este informe a su próxima consulta médica.</p>
    </div>
</body>
</html>";

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
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'c0170053.ferozo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'estudio@dianaestrin.com';
            $mail->Password   = '@Wurst2024@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Remitente y destinatario
            $mail->setFrom('estudio@dianaestrin.com', 'Estudio Diana Estrin');
            $mail->addCC('dianajudit@hotmail.com', "Se envió a: $destinatario");
            $mail->addCC('adege2000@yahoo.com.ar', "Se envió a: $destinatario");
            $mail->addCC('quirofanosi@santaisabel.com.ar', "Se envió a: $destinatario");
            $mail->addAddress($destinatario); // Este es el destinatario principal dinámico

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;

            // Adjuntos
            foreach ($adjuntos as $adjunto) {
                if (file_exists($adjunto)) {
                    $mail->addAttachment($adjunto);
                }
            }

            $mail->send();
            return ['success' => true, 'message' => 'Correo enviado correctamente.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo];
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

    public function updateInforme($id)
    {
        // Obtener datos del body JSON
        $data = $this->request->getJSON(true); // true para obtener un array asociativo

        // Verificar si se recibieron datos
        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se recibieron datos para actualizar. El cuerpo de la solicitud está vacío o no es JSON válido.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Crear array con los datos a actualizar, solo incluyendo los que se reciben
        $updateData = [];

        // Campos existentes en tu función original
        if (isset($data['nombre_paciente'])) {
            $updateData['nombre_paciente'] = trim($data['nombre_paciente']);
        }
        if (isset($data['dni_paciente'])) {
            $updateData['dni_paciente'] = trim($data['dni_paciente']);
        }
        if (isset($data['fecha'])) {
            $updateData['fecha'] = $data['fecha'];
        }
        if (isset($data['url_archivo'])) { // Ten cuidado si esto cambia al actualizar un archivo real
            $updateData['url_archivo'] = $data['url_archivo'];
        }
        if (isset($data['mail_paciente'])) {
            $updateData['mail_paciente'] = trim($data['mail_paciente']); // Añadido trim por consistencia
        }
        if (isset($data['tipo_informe'])) {
            $updateData['tipo_informe'] = trim($data['tipo_informe']);
        }
        if (isset($data['id_cobertura'])) {
            $updateData['id_cobertura'] = $data['id_cobertura'];
        }

        // --- CAMPOS NUEVOS/ADICIONALES ---
        if (isset($data['fecha_nacimiento_paciente'])) {
            $updateData['fecha_nacimiento_paciente'] = $data['fecha_nacimiento_paciente'];
        }
        if (isset($data['numero_afiliado'])) {
            $updateData['numero_afiliado'] = trim($data['numero_afiliado']);
        }
        if (isset($data['medico_envia_estudio'])) {
            $updateData['medico_envia_estudio'] = trim($data['medico_envia_estudio']);
        }
        if (isset($data['motivo_estudio'])) {
            $updateData['motivo_estudio'] = trim($data['motivo_estudio']);
        }
        if (isset($data['estomago'])) {
            $updateData['estomago'] = trim($data['estomago']);
        }
        if (isset($data['duodeno'])) {
            $updateData['duodeno'] = trim($data['duodeno']);
        }
        if (isset($data['esofago'])) {
            $updateData['esofago'] = trim($data['esofago']);
        }
        if (isset($data['conclusion'])) {
            $updateData['conclusion'] = trim($data['conclusion']);
        }
        if (isset($data['efectuo_terapeutica'])) {
            $updateData['efectuo_terapeutica'] = trim($data['efectuo_terapeutica']);
        }
        if (isset($data['tipo_terapeutica'])) {
            $updateData['tipo_terapeutica'] = trim($data['tipo_terapeutica']);
        }
        if (isset($data['efectuo_biopsia'])) {
            $updateData['efectuo_biopsia'] = trim($data['efectuo_biopsia']);
        }
        if (isset($data['fracos_biopsia'])) {
            $updateData['fracos_biopsia'] = $data['fracos_biopsia'];
        }
        if (isset($data['informe'])) {
            $updateData['informe'] = trim($data['informe']);
        }
        if (isset($data['edad'])) {
            $updateData['edad'] = $data['edad'];
        }

        // Si no hay datos válidos para actualizar, retornar un error
        if (empty($updateData)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se proporcionaron campos válidos para actualizar.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Intentar actualizar el informe
        // Asegúrate de que $this->InformesModel esté disponible.
        // Si no estás usando ResourceController de la forma más estricta con $modelName,
        // o si tienes lógica personalizada, podrías necesitar instanciarlo.
        $informeModel = $this->model ?? new \App\Models\InformesModel(); // Usa $this->model si está configurado, sino instancialo

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
                'data_updated' => $updateData // Renombrado de 'data' a 'data_updated' para mayor claridad
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } else {
            // Error al actualizar (puede ser por reglas de validación en el modelo, etc.)
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al actualizar el informe',
                'errors' => $informeModel->errors() // Esto es muy útil para depurar errores de validación del modelo
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    public function reenviarInformePorId($idInforme)
    {
        $informe = $this->InformesModel->find($idInforme);

        if (!$informe) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Informe no encontrado con el ID: ' . $idInforme,
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $mailPaciente = trim($informe['mail_paciente']);
        $nombrePaciente = trim($informe['nombre_paciente']);

        // ✅ Usar ruta exacta desde la base de datos
        $rutaPdfAbsoluta = FCPATH . str_replace('\\', '/', $informe['url_archivo']);

        if (!file_exists($rutaPdfAbsoluta)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se encontró el archivo PDF en la ruta esperada.',
                'ruta_pdf' => $rutaPdfAbsoluta,
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $asunto = 'Reenvío de Informe Médico para ' . $nombrePaciente;
        $mensaje = '<p>Estimado/a ' . $nombrePaciente . ',</p><p>Se le reenvía su informe médico.</p>';

        $resultadoEnvio = $this->enviarCorreoPHPMailer($mailPaciente, $asunto, $mensaje, [$rutaPdfAbsoluta]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Correo reenviado para el informe con ID: ' . $idInforme,
            'ruta_pdf' => $rutaPdfAbsoluta,
            'email_status' => $resultadoEnvio,
        ]);
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
