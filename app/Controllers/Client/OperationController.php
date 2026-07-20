<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\OperateurExterneModel;
use App\Models\PrefixeModel;
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
        $fraisDu = $baremeModel->calculerFrais($type['id'], $montant);

        if ($fraisDu === 0 && $montant > 0) {
            return redirect()->back()->with('error', 'Montant hors barème, opération impossible.');
        }

        if ($client['solde'] < $montant + $fraisDu) {
            return redirect()->back()->with('error', 'Solde insuffisant pour effectuer ce retrait.');
        }

        $nouveauSolde = $client['solde'] - $montant - $fraisDu;

        $clientModel->update($clientId, ['solde' => $nouveauSolde]);

        $transactionModel = new TransactionModel();
        $transactionModel->insert([
            'client_id' => $clientId,
            'client_destinataire_id' => null,
            'type_transaction_id' => $type['id'],
            'montant' => $montant,
            'frais' => $fraisDu,
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
        $inclureFraisRetrait = (bool) $this->request->getPost('inclure_frais_retrait');

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
        }

        $clientModel = new ClientModel();
        $emetteur = $clientModel->find($emetteurId);
        $prefixeModel = new PrefixeModel();
        $prefixe = $prefixeModel->trouverPourNumero($destinataireTelephone);

        if ($prefixe === null) {
            return redirect()->back()->with('error', 'Numéro non pris en charge.');
        }

        $typeModel = new TypeTransactionModel();
        $typeTransfert = $typeModel->where('code', 'transfert')->first();
        $typeRetrait = $typeModel->where('code', 'retrait')->first();
        $baremeModel = new BaremeFraisModel();
        $fraisBareme = $baremeModel->calculerFrais($typeTransfert['id'], $montant);

        if ($fraisBareme === 0 && $montant > 0) {
            return redirect()->back()->with('error', 'Montant hors barème, opération impossible.');
        }

        $db = db_connect();

        if (empty($prefixe['operateur_externe_id'])) {
            $destinataire = $clientModel->where('numero_telephone', $destinataireTelephone)->first();

            if (! $destinataire) {
                return redirect()->back()->with('error', 'Ce numéro n\'a pas encore de compte.');
            }

            if ($destinataire['id'] === $emetteurId) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
            }

            $fraisRetraitEstime = 0;
            $totalADebiter = $montant + $fraisBareme;
            $fraisRetraitInclus = 0;
            if ($inclureFraisRetrait) {
                $fraisRetraitEstime = $baremeModel->calculerFrais($typeRetrait['id'], $montant);
                $totalADebiter = $montant + $fraisBareme + $fraisRetraitEstime;
                $fraisRetraitInclus = 1;
            }

            if ($emetteur['solde'] < $totalADebiter) {
                return redirect()->back()->with('error', 'Solde insuffisant pour effectuer ce transfert.');
            }

            $db->transStart();

            $nouveauSoldeEmetteur = $emetteur['solde'] - $totalADebiter;
            $nouveauSoldeDestinataire = $destinataire['solde'] + $montant + $fraisRetraitEstime;

            $clientModel->update($emetteurId, ['solde' => $nouveauSoldeEmetteur]);
            $clientModel->update($destinataire['id'], ['solde' => $nouveauSoldeDestinataire]);

            $transactionModel = new TransactionModel();
            $transactionModel->insert([
                'client_id' => $emetteurId,
                'client_destinataire_id' => $destinataire['id'],
                'type_transaction_id' => $typeTransfert['id'],
                'montant' => $montant,
                'frais' => $fraisBareme,
                'nouveau_solde' => $nouveauSoldeEmetteur,
                'frais_retrait_inclus' => $fraisRetraitInclus,
            ]);

            $transactionModel->insert([
                'client_id' => $destinataire['id'],
                'client_destinataire_id' => $emetteurId,
                'type_transaction_id' => $typeTransfert['id'],
                'montant' => $montant,
                'frais' => 0,
                'nouveau_solde' => $nouveauSoldeDestinataire,
            ]);

            $db->transComplete();

            return redirect()->to('/client/accueil')->with('success', 'Transfert effectué.');
        }

        $operateurModel = new OperateurExterneModel();
        $operateur = $operateurModel->find($prefixe['operateur_externe_id']);
        $commission = (int) round($montant * $operateur['commission_pourcentage'] / 100);
        $totalADebiter = $montant + $fraisBareme + $commission;

        if ($emetteur['solde'] < $totalADebiter) {
            return redirect()->back()->with('error', 'Solde insuffisant pour effectuer ce transfert.');
        }

        $db->transStart();

        $nouveauSoldeEmetteur = $emetteur['solde'] - $totalADebiter;
        $clientModel->update($emetteurId, ['solde' => $nouveauSoldeEmetteur]);

        $transactionModel = new TransactionModel();
        $transactionModel->insert([
            'client_id' => $emetteurId,
            'client_destinataire_id' => null,
            'type_transaction_id' => $typeTransfert['id'],
            'montant' => $montant,
            'frais' => $fraisBareme,
            'nouveau_solde' => $nouveauSoldeEmetteur,
            'operateur_externe_id' => $operateur['id'],
            'numero_destinataire_externe' => $destinataireTelephone,
            'commission' => $commission,
            'frais_retrait_inclus' => 0,
        ]);

        $db->transComplete();

        return redirect()->to('/client/accueil')->with('success', 'Transfert effectué.');
    }

    public function formTransfertMultiple()
    {
        return view('client/transfert_multiple');
    }

    public function transfertMultiple()
    {
        $emetteurId = session()->get('client')['id'];
        $montantTotal = (int) $this->request->getPost('montant_total');
        $numeros = $this->request->getPost('numeros');
        $inclureFraisRetrait = (bool) $this->request->getPost('inclure_frais_retrait');

        if ($montantTotal <= 0) {
            return redirect()->back()->with('error', 'Le montant total doit être supérieur à 0.');
        }

        if (! is_array($numeros)) {
            $numeros = [$numeros];
        }

        $numerosNet = [];
        foreach ($numeros as $numero) {
            $numeroNet = trim((string) $numero);
            if ($numeroNet !== '') {
                $numerosNet[] = $numeroNet;
            }
        }

        if (count($numerosNet) < 2) {
            return redirect()->back()->with('error', 'Veuillez saisir au moins 2 numéros destinataires.');
        }

        if (count(array_unique($numerosNet)) !== count($numerosNet)) {
            return redirect()->back()->with('error', 'La liste contient des doublons.');
        }

        $nombreParts = count($numerosNet);
        $montantBase = intdiv($montantTotal, $nombreParts);
        $reste = $montantTotal % $nombreParts;

        $parts = [];
        foreach ($numerosNet as $index => $numero) {
            $montantPart = $montantBase + ($index === $nombreParts - 1 ? $reste : 0);
            $parts[] = ['numero' => $numero, 'montant' => $montantPart];
        }

        $clientModel = new ClientModel();
        $emetteur = $clientModel->find($emetteurId);
        $prefixeModel = new PrefixeModel();
        $typeModel = new TypeTransactionModel();
        $typeTransfert = $typeModel->where('code', 'transfert')->first();
        $typeRetrait = $typeModel->where('code', 'retrait')->first();
        $baremeModel = new BaremeFraisModel();
        $operateurModel = new OperateurExterneModel();

        $operations = [];
        $totalCost = 0;

        foreach ($parts as $part) {
            $prefixe = $prefixeModel->trouverPourNumero($part['numero']);
            if ($prefixe === null) {
                return redirect()->back()->with('error', 'Numéro non pris en charge.');
            }

            if (empty($prefixe['operateur_externe_id'])) {
                $destinataire = $clientModel->where('numero_telephone', $part['numero'])->first();
                if (! $destinataire) {
                    return redirect()->back()->with('error', 'Ce numéro n\'a pas encore de compte.');
                }
                if ($destinataire['id'] === $emetteurId) {
                    return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
                }

                $fraisBareme = $baremeModel->calculerFrais($typeTransfert['id'], $part['montant']);
                $fraisRetraitEstime = 0;
                if ($inclureFraisRetrait) {
                    $fraisRetraitEstime = $baremeModel->calculerFrais($typeRetrait['id'], $part['montant']);
                }
                $totalCost += $part['montant'] + $fraisBareme + $fraisRetraitEstime;
                $operations[] = [
                    'type' => 'interne',
                    'numero' => $part['numero'],
                    'destinataire' => $destinataire,
                    'montant' => $part['montant'],
                    'frais_bareme' => $fraisBareme,
                    'frais_retrait_estime' => $fraisRetraitEstime,
                    'frais_retrait_inclus' => $inclureFraisRetrait ? 1 : 0,
                ];
                continue;
            }

            $operateur = $operateurModel->find($prefixe['operateur_externe_id']);
            $fraisBareme = $baremeModel->calculerFrais($typeTransfert['id'], $part['montant']);
            $commission = (int) round($part['montant'] * $operateur['commission_pourcentage'] / 100);
            $totalCost += $part['montant'] + $fraisBareme + $commission;
            $operations[] = [
                'type' => 'externe',
                'numero' => $part['numero'],
                'operateur' => $operateur,
                'montant' => $part['montant'],
                'frais_bareme' => $fraisBareme,
                'commission' => $commission,
            ];
        }

        if ($emetteur['solde'] < $totalCost) {
            return redirect()->back()->with('error', 'Solde insuffisant pour effectuer cet envoi multiple.');
        }

        $db = db_connect();
        $db->transStart();

        try {
            $nouveauSoldeEmetteur = $emetteur['solde'] - $totalCost;
            $clientModel->update($emetteurId, ['solde' => $nouveauSoldeEmetteur]);

            $groupeEnvoi = bin2hex(random_bytes(8));
            $transactionModel = new TransactionModel();

            foreach ($operations as $operation) {
                if ($operation['type'] === 'interne') {
                    $destinataireActuel = $clientModel->find($operation['destinataire']['id']);
                    $nouveauSoldeDestinataire = $destinataireActuel['solde'] + $operation['montant'] + $operation['frais_retrait_estime'];

                    $clientModel->update($operation['destinataire']['id'], ['solde' => $nouveauSoldeDestinataire]);

                    $transactionModel->insert([
                        'client_id' => $emetteurId,
                        'client_destinataire_id' => $operation['destinataire']['id'],
                        'type_transaction_id' => $typeTransfert['id'],
                        'montant' => $operation['montant'],
                        'frais' => $operation['frais_bareme'],
                        'nouveau_solde' => $nouveauSoldeEmetteur,
                        'frais_retrait_inclus' => $operation['frais_retrait_inclus'],
                        'groupe_envoi' => $groupeEnvoi,
                    ]);

                    $transactionModel->insert([
                        'client_id' => $operation['destinataire']['id'],
                        'client_destinataire_id' => $emetteurId,
                        'type_transaction_id' => $typeTransfert['id'],
                        'montant' => $operation['montant'],
                        'frais' => 0,
                        'nouveau_solde' => $nouveauSoldeDestinataire,
                        'groupe_envoi' => $groupeEnvoi,
                    ]);
                    continue;
                }

                $transactionModel->insert([
                    'client_id' => $emetteurId,
                    'client_destinataire_id' => null,
                    'type_transaction_id' => $typeTransfert['id'],
                    'montant' => $operation['montant'],
                    'frais' => $operation['frais_bareme'],
                    'nouveau_solde' => $nouveauSoldeEmetteur,
                    'operateur_externe_id' => $operation['operateur']['id'],
                    'numero_destinataire_externe' => $operation['numero'],
                    'commission' => $operation['commission'],
                    'frais_retrait_inclus' => 0,
                    'groupe_envoi' => $groupeEnvoi,
                ]);
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();

            return redirect()->back()->with('error', 'Échec de l\'opération.');
        }

        return redirect()->to('/client/accueil')->with('success', 'Envoi multiple effectué.');
    }

    public function historique()
    {
        $clientId = session()->get('client')['id'];
        $transactionModel = new TransactionModel();
        $data['transactions'] = $transactionModel
            ->select('transactions.*, type_transaction.libelle AS type_transaction_libelle, type_transaction.code AS type_transaction_code, operateurs_externes.nom AS operateur_externe_nom, clients_destinataire.numero_telephone AS client_destinataire_numero')
            ->join('type_transaction', 'type_transaction.id = transactions.type_transaction_id')
            ->join('operateurs_externes', 'operateurs_externes.id = transactions.operateur_externe_id', 'left')
            ->join('clients clients_destinataire', 'clients_destinataire.id = transactions.client_destinataire_id', 'left')
            ->where('transactions.client_id', $clientId)
            ->orderBy('transactions.created_at', 'DESC')
            ->findAll();

        return view('client/historique', $data);
    }
}
