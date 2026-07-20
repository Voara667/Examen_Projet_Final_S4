<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'baremes_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['type_transaction_id', 'montant_min', 'montant_max', 'frais'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function calculerFrais(int $typeTransactionId, int $montant): int
    {
        $bareme = $this->where('type_transaction_id', $typeTransactionId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();

        return $bareme ? (int) $bareme['frais'] : 0;
    }
}
