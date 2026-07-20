<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\PrefixeModel;

class LoginController extends BaseController
{
    public function form()
    {
        if (session()->has('client')) {
            return redirect()->to('/client/accueil');
        }

        return view('client/login');
    }

    public function login()
    {
        $telephone = trim($this->request->getPost('numero_telephone'));

        if ($telephone === '') {
            return redirect()->back()->with('error', 'Le numéro de téléphone est obligatoire.');
        }

        $prefixeModel = new PrefixeModel();
        $prefixeValide = $prefixeModel->where('actif', 1)
            ->where('prefixe', substr($telephone, 0, 3))
            ->first();

        if (! $prefixeValide) {
            return redirect()->back()->with('error', 'Le préfixe n\'est pas autorisé.');
        }

        $clientModel = new ClientModel();
        $client = $clientModel->where('numero_telephone', $telephone)->first();

        if (! $client) {
            $clientModel->insert(['numero_telephone' => $telephone, 'solde' => 0]);
            $client = $clientModel->where('numero_telephone', $telephone)->first();
        }

        session()->set('client', [
            'id' => $client['id'],
            'numero_telephone' => $client['numero_telephone'],
        ]);

        return redirect()->to('/client/accueil')->with('success', 'Connexion réussie.');
    }

    public function logout()
    {
        session()->remove('client');
        return redirect()->to('/')->with('success', 'Vous avez été déconnecté.');
    }
}
