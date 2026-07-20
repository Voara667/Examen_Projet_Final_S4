<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OperateurExterneModel;

class OperateurExterneController extends BaseController
{
    public function index()
    {
        $model = new OperateurExterneModel();
        $data['operateurs'] = $model->orderBy('nom', 'ASC')->findAll();

        return view('admin/operateurs/index', $data);
    }

    public function store()
    {
        $model = new OperateurExterneModel();
        $nom = trim($this->request->getPost('nom'));
        $commission = $this->request->getPost('commission_pourcentage');

        if ($nom === '') {
            return redirect()->back()->with('error', 'Le nom de l\'opérateur est obligatoire.');
        }

        $model->insert([
            'nom' => $nom,
            'commission_pourcentage' => $commission !== '' ? (float) $commission : 0,
        ]);

        return redirect()->to('/admin/operateurs')->with('success', 'Opérateur ajouté.');
    }

    public function update($id)
    {
        $model = new OperateurExterneModel();
        $operateur = $model->find($id);

        if (! $operateur) {
            return redirect()->back()->with('error', 'Opérateur introuvable.');
        }

        $nom = trim($this->request->getPost('nom'));
        $commission = $this->request->getPost('commission_pourcentage');

        if ($nom === '') {
            return redirect()->back()->with('error', 'Le nom de l\'opérateur est obligatoire.');
        }

        $model->update($id, [
            'nom' => $nom,
            'commission_pourcentage' => $commission !== '' ? (float) $commission : 0,
        ]);

        return redirect()->to('/admin/operateurs')->with('success', 'Opérateur modifié.');
    }

    public function delete($id)
    {
        $model = new OperateurExterneModel();
        $operateur = $model->find($id);

        if (! $operateur) {
            return redirect()->back()->with('error', 'Opérateur introuvable.');
        }

        $model->delete($id);

        return redirect()->to('/admin/operateurs')->with('success', 'Opérateur supprimé.');
    }
}
