<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeTransactionModel extends Model
{
    protected $table            = 'type_transaction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['code', 'libelle'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
