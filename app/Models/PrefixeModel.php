<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['prefixe', 'actif', 'operateur_externe_id'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function trouverPourNumero(string $numeroTelephone)
    {
        $prefixe = substr($numeroTelephone, 0, 3);

        return $this->where('prefixe', $prefixe)
            ->where('actif', 1)
            ->first();
    }
}
