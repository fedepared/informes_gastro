<?php

namespace App\Controllers;

use App\Models\CoberturasModel;

class Coberturas extends BaseController
{
    private $coberturasModel;

    public function __construct()
    {
        $this->coberturasModel = new CoberturasModel();
    }

    public function getCoberturas()
    {
        try {

            // $data['coberturas'] =  $this->coberturasModel->findAll();
            // return view('coberturas', $data);
            $coberturas = $this->coberturasModel->findAll(); // Obtiene todas las coberturas
            return $this->response->setJSON($coberturas);
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getByIdCoberturas($id)
    {
        try {
            $resultado = $this->coberturasModel->find($id);

            if (!empty($resultado)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $resultado
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No se encontró la cobertura con el ID proporcionado.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function postCobertura()
    {
        try {
            $data = [
                'nombre_cobertura' => $this->request->getPost('nombre_cobertura')
            ];

            $this->coberturasModel->insert($data);

            return redirect()->to(site_url('coberturas_view'))->with('success', 'Cobertura creada exitosamente.');
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteCobertura($id)
    {
        try {
            log_message('debug', 'ID recibido en deleteCobertura: ' . print_r($id, true));

            // Verificar si la cobertura existe antes de eliminar
            $antes = $this->getByIdCoberturas($id);

            if ($antes['status'] === 'error') {
                return $this->response->setJSON($antes);
            }

            // Eliminar la cobertura
            $resultado = $this->coberturasModel->delete($id);

            log_message('debug', 'Resultado de la eliminación: ' . print_r($resultado, true));

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Cobertura eliminada exitosamente.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }



    public function updateCobertura($id)
    {
        try {
            // Verifica si la cobertura existe
            $antes = $this->coberturasModel->find($id);
            if (!$antes) {
                return redirect()->back()->with('error', 'Cobertura no encontrada.');
            }

            // Recibe los datos del formulario
            $data = [
                'nombre_cobertura' => $this->request->getPost('nombre_cobertura')
            ];

            // Actualiza la cobertura
            $this->coberturasModel->update($id, $data);

            return redirect()->to(site_url('coberturas_view'))->with('success', 'Cobertura modificada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
