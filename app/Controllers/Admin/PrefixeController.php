<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrefixeModel;

class PrefixeController extends BaseController
{
    public function index()
    {
        $model = new PrefixeModel();
        $data['prefixes'] = $model->orderBy('prefixe', 'ASC')->findAll();

        return view('admin/prefixes/index', $data);
    }

    public function store()
    {
        $model = new PrefixeModel();
        $prefixe = trim($this->request->getPost('prefixe'));

        if ($prefixe === '') {
            return redirect()->back()->with('error', 'Le préfixe est obligatoire.');
        }

        $model->save(['prefixe' => $prefixe, 'actif' => 1]);

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
