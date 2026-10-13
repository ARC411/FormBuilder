<?php

namespace App\Models;

use CodeIgniter\Model;

class FormRevisionModel extends Model
{
    protected $table            = 'form_revisions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Field yang diizinkan untuk diisi
    protected $allowedFields    = ['process_code', 'revisi', 'created_at'];
}