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
            
            $data['coberturas'] =  $this->coberturasModel->findAll();
            return view('coberturas', $data);
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

            return redirect()->to(site_url('coberturas'))->with('success', 'Cobertura creada exitosamente.');

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
            echo "orueba";
            $antes = $this->getByIdCoberturas($id);

            if ($antes['status'] === 'error') {
                return $antes; // Retorna el error si no se encuentra la cobertura
            }

            $this->coberturasModel->delete($id);

            // $despues = $this->getByIdCoberturas($id);

            return redirect()->to(site_url('coberturas'))->with('success', 'Cobertura eliminada exitosamente.');
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
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
    
            return redirect()->to(site_url('coberturas'))->with('success', 'Cobertura modificada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}