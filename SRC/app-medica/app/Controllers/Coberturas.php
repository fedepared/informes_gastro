<?php

namespace App\Controllers;

use App\Models\CoberturasModel;
use CodeIgniter\HTTP\ResponseInterface;

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
                return [
                    'status' => 'success',
                    'data' => $resultado
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'No se encontró la cobertura con el ID proporcionado.'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function postCobertura()
    {
        try {
            $data = [
                'nombre_cobertura'=> $this->request->getPost('nombre_cobertura')
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
           
            // Validar que el ID sea válido
            if (empty($id) || !is_numeric($id)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID inválido proporcionado.'
                    ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
            
            // Verificar si la cobertura existe antes de eliminar
            $cobertura = $this->coberturasModel->find($id);
            
            if (!$cobertura) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cobertura no encontrada.'
                    ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }
            
            // Intentar eliminar la cobertura
            $resultado = $this->coberturasModel->delete($id);
            
            log_message('debug', 'Resultado de la eliminación: ' . print_r($resultado, true));
            
            // Verificar si la eliminación fue exitosa
            if ($resultado) {
                return redirect()->to(site_url('coberturas_view'))->with('success', 'Cobertura eliminada exitosamente.');
               
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'No se pudo eliminar la cobertura.'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            // Capturar cualquier error inesperado y devolverlo
            log_message('error', 'Error al intentar eliminar cobertura: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hubo un error al procesar la solicitud: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
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