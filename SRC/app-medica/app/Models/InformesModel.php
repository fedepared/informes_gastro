<?php

namespace App\Models;


use CodeIgniter\Model;

class InformesModel extends Model
{
    protected $table      = 'informes';
    protected $primaryKey = 'id_informe';

    protected $returnType     = 'array';
    protected $allowedFields = ['nombre_paciente','fecha','url_archivo','mail_paciente','id_cobertura'];
    

}

?>