<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class CompteController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $data['comptes'] = $db->query('SELECT * FROM vue_situation_comptes_clients ORDER BY created_at DESC')->getResultArray();

        return view('admin/comptes/index', $data);
    }
}
