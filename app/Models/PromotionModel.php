<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'promotion_transfert';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allawsFields = ['libelle', 'pourcentage', 'actif'];

    
}
