<?php

namespace App\Controllers;

use App\Models\InformesModel;

class Informes extends BaseController
{
    private  $InformesModel;
    public function __construct()
    {
        $this->InformesModel = new InformesModel();
    }



    public function getInformes()
    {

        $resultado = $this->InformesModel->findAll();

        echo 'get all';
        echo '<pre>';
        print_r($resultado);
        echo '</pre>';
    }

    public function getByIdInformes($id)
    {
        $resultado = $this->InformesModel->find($id);

        if (!empty($resultado)) {
            echo 'getbyid';
            echo '<pre>';
            print_r($resultado);
            echo '</pre>';
            return $resultado;
        } else {
            echo 'no se encontro cobertura  ';
            return null;
        }
    }

    public function postInforme()
    {


        $data = [
            'nombre_paciente' => 'fede',
            'fecha' => '2025-03-22',
            'url_archivo' => 'url',
            'mail_paciente' => 'fede@gmail.com',
            'id_cobertura' => '5',

        ];

        $this->InformesModel->insert($data);

        echo 'Post';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
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


    public function deleteInforme($id)
    {

        $antes = $this->getByIdInformes($id);


        $this->InformesModel->delete($id);

        $despues = $this->getByIdInformes($id);

        echo '<pre>';
        echo 'Delete';
        echo '<pre>';
        echo 'antes';
        print_r($antes);

        echo 'despues';
        print_r($despues);
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
        print_r($antes);

        echo 'despues';
        print_r($despues);
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
