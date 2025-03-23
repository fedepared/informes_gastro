<?php

namespace App\Models;


use CodeIgniter\Model;

class InformesModel extends Model
{
    protected $table      = 'informes';
    protected $primaryKey = 'id_informe';

    protected $returnType     = 'array';
    protected $allowedFields = ['nombre_paciente','fecha','url_archivo','mail_paciente','id_cobertura'];
    

    
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