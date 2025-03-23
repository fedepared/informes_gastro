<?php

namespace App\Controllers;

use App\Models\InformesModel;
use App\Controllers\Coberturas;

use Dompdf\Dompdf;
use Dompdf\Options;

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
        // Obtener la cobertura
        $coberturaModel = new Coberturas();
        $coberturaData = $coberturaModel->getByIdCoberturas(5); // Reemplaza con el ID correcto

        // Extraer el nombre de la cobertura si existe
        $nombreCobertura = $coberturaData['nombre_cobertura'] ?? 'No especificada';

        // Datos para guardar en la base de datos
        $data = [
            'nombre_paciente' => 'Puto el que lee', // <-- Cambia este valor antes de producción
            'fecha' => '2025-03-22',
            'url_archivo' => 'url',
            'mail_paciente' => 'fede@gmail.com',
            'id_cobertura' => '5', // Solo se guarda el ID
        ];

        // Guardar en la base de datos
        $this->InformesModel->insert($data);

        // Generar y descargar el PDF con el nombre de la cobertura
        return $this->generatePDF($data, $nombreCobertura);
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
