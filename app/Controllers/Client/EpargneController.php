<?php
namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\TransactionModel;
use App\Models\TypeTransactionModel;

class EpargneController extends BaseController {
 public function formConfigurer()
    {
        
     $clientId = session()->get('client')['id'];
        $clientModel = new ClientModel();
        $data['client'] = $clientModel->find($clientId);

        return view('client/epargne/config', $data);

    }

  public function Configurer()
    {
        $clientId = session()->get('client')['id'];
        $pourcentagePost = $this->require->$_POST;

    

    

}

  ?>