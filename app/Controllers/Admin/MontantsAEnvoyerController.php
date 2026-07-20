<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class MontantsAEnvoyerController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $data['montants'] = $db->query(
            'SELECT * FROM vue_montants_a_envoyer_operateurs ORDER BY operateur_externe_nom ASC'
        )->getResultArray();

        return view('admin/montants_a_envoyer/index', $data);
    }
}
