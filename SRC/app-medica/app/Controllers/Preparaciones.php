<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PreparacionesModel; // Importa el modelo que acabas de crear
use CodeIgniter\API\ResponseTrait; // Para los métodos de respuesta JSON

class Preparaciones extends BaseController // O ResourceController si lo usas
{
    use ResponseTrait; // Habilita los métodos de respuesta JSON

    protected $preparacionesModel; // Declara la propiedad para el modelo

    public function __construct()
    {

        $this->preparacionesModel = new PreparacionesModel(); // Instancia el modelo
    }

    /**
     * Obtiene una preparación por su ID y la devuelve como JSON.
     *
     * @param int $id El ID de la preparación a buscar.
     * @return ResponseInterface
     */
    public function getByIdPreparaciones($id)
    {
        try {
            $preparacion = $this->preparacionesModel->find($id);

            if (!$preparacion) {
                return $this->failNotFound('Preparación no encontrada.');
            }

            return $this->respond([
                'status' => 'success',
                'data' => $preparacion
            ], ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            // Log the error for debugging in development environment
            log_message('error', 'Error en getByIdPreparaciones: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error interno al obtener la preparación. Detalles en logs.');
        }
    }


    public function getByTipoPreparacion($tipo)
    {

        // 1. Validación del parámetro de tipo
        if (empty($tipo) || !is_string($tipo)) {
            return $this->failValidationErrors(['tipo' => 'El tipo de preparación es requerido y debe ser una cadena válida.']);
        }
        try {
            // 2. Llama al método del modelo para obtener las preparaciones filtradas y ordenadas
            // Asumimos que el campo en la DB se llama 'tipo_preparacion'
            $preparaciones = $this->preparacionesModel
                ->where('tipo_preparacion', $tipo)
                ->orderBy('id_preparacion', 'ASC') // Ordena por el ID de preparación de forma ascendente
                ->findAll(); // Trae todos los resultados que coincidan

            // 3. Manejo de resultados: ¿se encontraron preparaciones o no?
            if (empty($preparaciones)) {
                return $this->failNotFound('No se encontraron preparaciones para el tipo "' . esc($tipo) . '".');
            }

            // 4. Si se encontraron preparaciones, devuelve una respuesta JSON exitosa
            return $this->respond([
                'status' => 'success',
                'error' => null,
                'messages' => ['success' => 'Preparaciones encontradas exitosamente.'],
                'data' => $preparaciones // Aquí van los datos de las preparaciones
            ], ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            // Captura cualquier error inesperado (ej. problema de DB)
            log_message('error', 'Error en getByTipoPreparacion: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error interno al obtener las preparaciones por tipo. Detalles en logs.');
        }
    }



    public function updateTexto($id)
    {
        try {
            // 1. Verificar si la preparación existe
            $preparacionExistente = $this->preparacionesModel->find($id);

            if (!$preparacionExistente) {
                return $this->failNotFound('Preparación no encontrada con ID: ' . $id);
            }

            // 2. Obtener los datos del cuerpo de la solicitud (JSON)
            // request->getJSON() lee el cuerpo de la solicitud como JSON
            $input = $this->request->getJSON();

            // 3. Validar el campo 'texto'
            if (!isset($input->texto)) {
                return $this->failValidationErrors('El campo "texto" es requerido para la actualización.');
            }

            $nuevoTexto = $input->texto;

            // Opcional: Puedes agregar validación extra para el 'texto' aquí,
            // por ejemplo, longitud mínima/máxima si no está en el modelo
            // if (strlen($nuevoTexto) < 5) {
            //     return $this->failValidationError('El texto debe tener al menos 5 caracteres.');
            // }

            // 4. Preparar los datos para la actualización
            $dataToUpdate = [
                'texto' => $nuevoTexto
            ];

            // 5. Actualizar la preparación en la base de datos
            // El método update() devuelve true en éxito, false en fallo (ej. validación).
            // Si quieres el número de filas afectadas, puedes usar $this->preparacionesModel->db->affectedRows();
            $updated = $this->preparacionesModel->update($id, $dataToUpdate);

            if ($updated) {
                // Recuperar la preparación actualizada para devolverla en la respuesta (opcional)
                $preparacionActualizada = $this->preparacionesModel->find($id);

                return $this->respond([
                    'status' => 'success',
                    'messages' => ['success' => 'Texto de preparación actualizado exitosamente.'],
                    'data' => $preparacionActualizada
                ], ResponseInterface::HTTP_OK);
            } else {
                // Esto podría ocurrir si la validación del modelo falla internamente
                // Aunque aquí solo actualizamos 'texto', si el modelo tiene reglas más complejas.
                return $this->failServerError('Fallo al actualizar la preparación.');
            }
        } catch (\ReflectionException $e) {
            // Error si el modelo no puede mapear los campos, generalmente por error en $allowedFields
            log_message('error', 'ReflectionException en updateTexto: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor (ReflectionException): ' . $e->getMessage());
        } catch (\Exception $e) {
            // Captura cualquier otro error inesperado (ej. problemas de DB)
            log_message('error', 'Error general en updateTexto: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error inesperado al actualizar la preparación.');
        }
    }
}
