<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\ClientModel;

class CompteController extends BaseController
{
    public function index()
    {
        $clientId = session()->get('client')['id'];
        $clientModel = new ClientModel();
        $data['client'] = $clientModel->find($clientId);

        return view('client/compte', $data);
    }
}
