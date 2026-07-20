<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\TransactionModel;
use App\Models\TypeTransactionModel;

class OperationController extends BaseController
{
    public function formDepot()
    {
        return view('client/depot');
    }

    public function depot()
    {
        $clientId = session()->get('client')['id'];
        $montant = (int) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
        }

        $clientModel = new ClientModel();
        $client = $clientModel->find($clientId);
        $nouveauSolde = $client['solde'] + $montant;

        $clientModel->update($clientId, ['solde' => $nouveauSolde]);

        $transactionModel = new TransactionModel();
        $typeModel = new TypeTransactionModel();
        $type = $typeModel->where('code', 'depot')->first();

        $transactionModel->insert([
            'client_id' => $clientId,
            'client_destinataire_id' => null,
            'type_transaction_id' => $type['id'],
            'montant' => $montant,
            'frais' => 0,
            'nouveau_solde' => $nouveauSolde,
        ]);

        return redirect()->to('/client/accueil')->with('success', 'Dépôt effectué avec succès.');
    }

    public function formRetrait()
    {
        return view('client/retrait');
    }

    public function retrait()
    {
        $clientId = session()->get('client')['id'];
        $montant = (int) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
        }

        $clientModel = new ClientModel();
        $client = $clientModel->find($clientId);
        $baremeModel = new BaremeFraisModel();
        $typeModel = new TypeTransactionModel();
        $type = $typeModel->where('code', 'retrait')->first();
        $frais = $baremeModel->calculerFrais($type['id'], $montant);

        if ($frais === 0 && $montant > 0) {
            return redirect()->back()->with('error', 'Montant hors barème, opération impossible.');
        }

        if ($client['solde'] < $montant + $frais) {
            return redirect()->back()->with('error', 'Solde insuffisant pour effectuer ce retrait.');
        }

        $nouveauSolde = $client['solde'] - $montant - $frais;
        $clientModel->update($clientId, ['solde' => $nouveauSolde]);

        $transactionModel = new TransactionModel();
        $transactionModel->insert([
            'client_id' => $clientId,
            'client_destinataire_id' => null,
            'type_transaction_id' => $type['id'],
            'montant' => $montant,
            'frais' => $frais,
            'nouveau_solde' => $nouveauSolde,
        ]);

        return redirect()->to('/client/accueil')->with('success', 'Retrait effectué.');
    }

    public function formTransfert()
    {
        return view('client/transfert');
    }

    public function transfert()
    {
        $emetteurId = session()->get('client')['id'];
        $destinataireTelephone = trim($this->request->getPost('numero_destinataire'));
        $montant = (int) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
        }

        $clientModel = new ClientModel();
        $emetteur = $clientModel->find($emetteurId);
        $destinataire = $clientModel->where('numero_telephone', $destinataireTelephone)->first();

        if (! $destinataire) {
            return redirect()->back()->with('error', 'Le destinataire n\'existe pas.');
        }

        if ($destinataire['id'] === $emetteurId) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
        }

        $typeModel = new TypeTransactionModel();
        $type = $typeModel->where('code', 'transfert')->first();
        $baremeModel = new BaremeFraisModel();
        $frais = $baremeModel->calculerFrais($type['id'], $montant);

        if ($frais === 0 && $montant > 0) {
            return redirect()->back()->with('error', 'Montant hors barème, opération impossible.');
        }

        if ($emetteur['solde'] < $montant + $frais) {
            return redirect()->back()->with('error', 'Solde insuffisant pour effectuer ce transfert.');
        }

        $db = db_connect();
        $db->transStart();

        $nouveauSoldeEmetteur = $emetteur['solde'] - $montant - $frais;
        $nouveauSoldeDestinataire = $destinataire['solde'] + $montant;

        $clientModel->update($emetteurId, ['solde' => $nouveauSoldeEmetteur]);
        $clientModel->update($destinataire['id'], ['solde' => $nouveauSoldeDestinataire]);

        $transactionModel = new TransactionModel();
        $transactionModel->insert([
            'client_id' => $emetteurId,
            'client_destinataire_id' => $destinataire['id'],
            'type_transaction_id' => $type['id'],
            'montant' => $montant,
            'frais' => $frais,
            'nouveau_solde' => $nouveauSoldeEmetteur,
        ]);

        $transactionModel->insert([
            'client_id' => $destinataire['id'],
            'client_destinataire_id' => $emetteurId,
            'type_transaction_id' => $type['id'],
            'montant' => $montant,
            'frais' => 0,
            'nouveau_solde' => $nouveauSoldeDestinataire,
        ]);

        $db->transComplete();

        return redirect()->to('/client/accueil')->with('success', 'Transfert effectué.');
    }

    public function historique()
    {
        $clientId = session()->get('client')['id'];
        $transactionModel = new TransactionModel();
        $data['transactions'] = $transactionModel->where('client_id', $clientId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('client/historique', $data);
    }
}
