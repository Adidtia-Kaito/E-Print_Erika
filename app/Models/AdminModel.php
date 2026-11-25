<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table            = 'admin';
    protected $primaryKey       = 'id_admin';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama_lengkap',
        'email',
        'no_wa',
        'kata_sandi',
        'username',
        'role',
        'status_aktif',
    ];
}