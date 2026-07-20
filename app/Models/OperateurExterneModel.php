<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurExterneModel extends Model
{
    protected $table            = 'operateurs_externes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['nom', 'commission_pourcentage'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
