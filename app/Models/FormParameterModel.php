<?php

namespace App\Models;

use CodeIgniter\Model;

class FormParameterModel extends Model
{
    protected $table            = 'form_parameters';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Field bapak-anak dan pengaturan form
    protected $allowedFields    = [
        'revision_id', 
        'parent_id', 
        'nama_parameter', 
        'kolom_db', 
        'tipe_input', 
        'standar_nilai', 
        'urutan'
    ];
}