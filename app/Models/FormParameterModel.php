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
        'table_index',
        'nama_parameter', 
        'kolom_db', 
        'tipe_input', 
        'batas_min',
        'batas_max',
        'posisi_grid',
        'satuan_opsi',
        'is_readonly',
        'is_exclusive',
        'rumus_otomatis',
        'standar_dinamis',
        'opsi_dropdown',
        'step_value',
        'rentang_ganda',
        'target_evaluasi',
        'standar_nilai', 
        'urutan'
    ];
}