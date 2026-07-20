<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class GainController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $data['gains'] = $db->query('SELECT * FROM vue_situation_gains')->getResultArray();

        return view('admin/gains/index', $data);
    }
}
