<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OperateurExterneModel;
use App\Models\PrefixeModel;

class PrefixeController extends BaseController
{
    public function index()
    {
        $model = new PrefixeModel();
        $data['prefixes'] = $model
            ->select('prefixes.*, operateurs_externes.nom AS operateur_externe_nom')
            ->join('operateurs_externes', 'operateurs_externes.id = prefixes.operateur_externe_id', 'left')
            ->orderBy('prefixes.prefixe', 'ASC')
            ->findAll();

        $operatorModel = new OperateurExterneModel();
        $data['operateurs'] = $operatorModel->orderBy('nom', 'ASC')->findAll();

        return view('admin/prefixes/index', $data);
    }

    public function store()
    {
        $model = new PrefixeModel();
        $prefixe = trim($this->request->getPost('prefixe'));
        $operateurExterneId = $this->request->getPost('operateur_externe_id');

        if ($prefixe === '') {
            return redirect()->back()->with('error', 'Le préfixe est obligatoire.');
        }

        if ($operateurExterneId === '') {
            $operateurExterneId = null;
        }

        $model->save([
            'prefixe' => $prefixe,
            'actif' => 1,
            'operateur_externe_id' => $operateurExterneId,
        ]);

        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe ajouté.');
    }

    public function toggle($id)
    {
        $model = new PrefixeModel();
        $prefixe = $model->find($id);

        if (! $prefixe) {
            return redirect()->back()->with('error', 'Préfixe introuvable.');
        }

        $model->update($id, ['actif' => (int) ! (bool) $prefixe['actif']]);

        return redirect()->to('/admin/prefixes')->with('success', 'État du préfixe mis à jour.');
    }
}
