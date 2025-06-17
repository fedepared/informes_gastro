<?php

namespace App\Models;


use CodeIgniter\Model;

class InformesModel extends Model
{
    protected $table      = 'informes';
    protected $primaryKey = 'id_informe';

    protected $returnType     = 'array';
    protected $allowedFields = ['nombre_paciente','fecha','url_archivo','mail_paciente','id_cobertura',
    'id_informe','dni_paciente','tipo_informe','fecha_nacimiento_paciente','numero_afiliado',
    'medico_envia_estudio','motivo_estudio','estomago','duodeno','esofago','conclusion','efectuo_terapeutica',
    'tipo_terapeutica','efectuo_biopsia','fracos_biopsia','informe','edad'];
    


    public function getInformesWithFiltros($nombre = null, $fecha_desde = null, $fecha_hasta = null)
    {
        $builder = $this->db->table($this->table);
    
        // Unimos con la tabla de coberturas si es necesario (asumo que ya lo haces en getInformesWithCoberturas)
        $builder->join('coberturas', 'informes.id_cobertura = coberturas.id_cobertura'); // Ajusta el nombre de la tabla y las claves si es diferente
    
        if ($nombre) {
            $builder->like('nombre_paciente', $nombre, 'both'); // 'both' para buscar coincidencias en cualquier parte del nombre
        }
    
        if ($fecha_desde && $fecha_hasta) {
            $builder->where('fecha >=', $fecha_desde);
            $builder->where('fecha <=', $fecha_hasta);
        } elseif ($fecha_desde) {
            $builder->where('fecha >=', $fecha_desde);
        } elseif ($fecha_hasta) {
            $builder->where('fecha <=', $fecha_hasta);
        }
    
        return $builder->get()->getResultArray();
    }


    public function countInformesFiltrados($nombre = null, $desde = null, $hasta = null)
    {
        $builder = $this->select('COUNT(*) as total');
    
        if ($nombre) {
            $builder->like('nombre_paciente', $nombre);
        }
    
        if ($desde) {
            $builder->where('fecha >=', $desde);
        }
    
        if ($hasta) {
            $builder->where('fecha <=', $hasta);
        }
    
        return $builder->get()->getRow()->total;
    }
    

    public function getInformesPaginado($nombre = null, $desde = null, $hasta = null, $limit = 10, $offset = 0)
    {
        $builder = $this->select('informes.*, coberturas.nombre_cobertura')
            ->join('coberturas', 'informes.id_cobertura = coberturas.id_cobertura', 'left');
    
        if ($nombre) {
            $builder->like('nombre_paciente', $nombre);
        }
    
        if ($desde) {
            $builder->where('fecha >=', $desde);
        }
    
        if ($hasta) {
            $builder->where('fecha <=', $hasta);
        }
    
        // Asegurarse de que limit y offset sean enteros
        $limit = (int) $limit;
        $offset = (int) $offset;
    
        return $builder
            ->orderBy('fecha', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->getResult();
    }
    
    







    
    public function getInformesWithCoberturas()
    {
        return $this->db->table('informes')
            ->select('informes.*, coberturas.nombre_cobertura')
            ->join('coberturas', 'coberturas.id_cobertura = informes.id_cobertura', 'left')
            ->get()
            ->getResultArray();
    }

    public function getInformeByIdWithCobertura($id)
    {
        return $this->db->table('informes')
            ->select('informes.*, coberturas.nombre_cobertura')
            ->join('coberturas', 'coberturas.id_cobertura = informes.id_cobertura', 'left')
            ->where('informes.id_informe', $id)
            ->get()
            ->getRowArray();
    }

}

?>