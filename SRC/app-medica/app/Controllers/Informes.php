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
        $resultado = $this->InformesModel->getInformesWithCoberturas();
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
            log_message('info', 'Iniciando postInforme');

    


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

            // Crear directorios si no existen
            if (!is_dir($uploadPathInforme) && !mkdir($uploadPathInforme, 0777, true)) {
                log_message('error', 'Error al crear el directorio del informe: ' . $uploadPathInforme);
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Error al crear el directorio para el informe.'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            $archivosSubidosNombres = [];
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
                    $archivosSubidosNombres[] = $nuevoNombre;
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
            ];


            echo "<pre>";
            print_r($dataPdf);
            echo "</pre>";


            // Generar PDF y obtener el nombre del archivo
            $pdfFileName = $this->generatePDF($dataPdf, $nombreCobertura, $uploadPathInforme);
            $pdfRelativePath = 'uploads/' . $carpetaPaciente . '/' . $carpetaInforme;

            // Datos para insertar en la BD
            $dataDb = [
                'nombre_paciente' => $nombrePaciente,
                'dni_paciente' => $dniPaciente,
                'fecha' => $fecha,
                'url_archivo' => $pdfRelativePath,
                'mail_paciente' => $mailPaciente,
                'tipo_informe' => $tipoInforme,
                'id_cobertura' => $idCobertura,
            ];

            $this->InformesModel->insert($dataDb);
            $idInformeInsertado = $this->InformesModel->insertID();

            // Enviar correo electrónico con adjuntos
            $asunto = 'Informe Médico para ' . $nombrePaciente;
            $mensaje = '<p>Estimado/a ' . $nombrePaciente . ',</p><p>PUTO EL QUE LEE.</p>';
            $adjuntos = [];

            // Adjuntar PDF
            $rutaPdfAbsoluta = $uploadPathInforme . $pdfFileName;
            if (file_exists($rutaPdfAbsoluta)) {
                $adjuntos[] = $rutaPdfAbsoluta;
            } else {
                log_message('error', 'No se encontró el archivo PDF para adjuntar: ' . $rutaPdfAbsoluta);
            }

            // Adjuntar imágenes subidas
            foreach ($archivosSubidosNombres as $nombreArchivo) {
                $rutaArchivoAbsoluta = $uploadPathInforme . $nombreArchivo;
                if (file_exists($rutaArchivoAbsoluta)) {
                    $adjuntos[] = $rutaArchivoAbsoluta;
                } else {
                    log_message('warning', 'No se encontró el archivo subido para adjuntar: ' . $rutaArchivoAbsoluta);
                }
            }

            $resultadoEnvio = $this->enviarCorreoPHPMailer($mailPaciente, $asunto, $mensaje, $adjuntos);
            log_message('info', 'Resultado del envío de correo: ' . $resultadoEnvio);

            return redirect()->to('/reportes');
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
        $dompdf = new \Dompdf\Dompdf();
    
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
    
            <div class='section'>
                <div class='section-title'>INSTRUCCIONES POST ESTUDIO</div>
                <ol style='font-size: 11px;'>
                    <li>Si presenta dolor cólico tras colonoscopía, puede tomar antiespasmódico simple. Si persiste con fiebre, vómitos o sangrado, consultar.</li>
                    <li>Debe regresar acompañado y descansar. No debe consumir alcohol, conducir ni trabajar ese día.</li>
                    <li>Alimentación: dieta habitual con líquidos no gasificados y alimentos sencillos.</li>
                    <li>Retomar medicación habitual 1 hora después del estudio. Evitar sedantes por 24hs.</li>
                    <li>Si se realizó terapéutica, evitar aspirinas y anticoagulantes según indicación médica.</li>
                    <li>Contacto Dra Estrin Diana: 1134207000 / dianajudit@hotmail.com</li>
                    <li>Secretaría Endoscopias: Belén Chapuis 1151825634 / secretariaendoscopias@gmail.com</li>
                </ol>
            </div>
    
            <div class='footer-box'>
                <p style='text-align:center; font-weight:bold;'>FIRMA DIGITAL Y SELLO</p>
                <p><strong>IMPORTANTE:</strong> Es imprescindible contar con este informe para la consulta con la Dra Estrin o con su médico enviador.</p>
            </div>
        </body>
        </html>
        ";
    
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        $nombrePacienteSanitized = preg_replace('/[^A-Za-z0-9]/', '_', strtolower($data['nombre_paciente'] ?? 'sin_nombre'));
        $dniPacienteSanitized = preg_replace('/[^A-Za-z0-9]/', '_', $data['dni_paciente'] ?? 'sin_dni');
        $fechaSanitized = str_replace('-', '_', $data['fecha'] ?? 'sin_fecha');
        $timestamp = time();
        $fileName = "informe_{$fechaSanitized}_{$timestamp}.pdf";
        $filePath = $outputPath . $fileName;
    
        file_put_contents($filePath, $dompdf->output());
    
        return $fileName;
    }
    
    

    // Método para enviar correos electrónicos con PHPMailer
    private function enviarCorreoPHPMailer($destinatario, $asunto, $mensaje, $adjuntos = [])
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

            // Configuración del remitente y destinatario
            $mail->setFrom('estudio@dianaestrin.com', 'Your Name'); // Cambiar por tu correo y nombre
            $mail->addAddress($destinatario);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;

            // Adjuntar archivos
            foreach ($adjuntos as $adjunto) {
                $mail->addAttachment($adjunto);
            }

            $mail->send();
            return 'Correo enviado correctamente.';
        } catch (Exception $e) {
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
