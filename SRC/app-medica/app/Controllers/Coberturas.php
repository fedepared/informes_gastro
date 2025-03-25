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
            $resultado = $this->coberturasModel->findAll();
            return [
                'status' => 'success',
                'data' => $resultado
            ];
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
                'nombre_cobertura' => 'iosfa'
            ];

            $this->coberturasModel->insert($data);

            return [
                'status' => 'success',
                'message' => 'Cobertura creada exitosamente.',
                'data' => $data
            ];
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
            $antes = $this->getByIdCoberturas($id);

            if ($antes['status'] === 'error') {
                return $antes; // Retorna el error si no se encuentra la cobertura
            }

            $this->coberturasModel->delete($id);

            $despues = $this->getByIdCoberturas($id);

            return [
                'status' => 'success',
                'message' => 'Cobertura eliminada exitosamente.',
                'antes' => $antes['data'],
                'despues' => $despues['data']
            ];
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
            $antes = $this->getByIdCoberturas($id);

            if ($antes['status'] === 'error') {
                return $antes; // Retorna el error si no se encuentra la cobertura
            }

            $data = [
                'nombre_cobertura' => 'iosfa modificado'
            ];

            $this->coberturasModel->update($id, $data);

            $despues = $this->getByIdCoberturas($id);

            return [
                'status' => 'success',
                'message' => 'Cobertura actualizada exitosamente.',
                'antes' => $antes['data'],
                'despues' => $despues['data']
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}