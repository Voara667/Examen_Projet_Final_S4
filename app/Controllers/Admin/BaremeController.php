<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BaremeFraisModel;
use App\Models\TypeTransactionModel;

class BaremeController extends BaseController
{
    public function index()
    {
        $baremeModel = new BaremeFraisModel();
        $typeModel = new TypeTransactionModel();

        $data['baremes'] = $baremeModel->orderBy('type_transaction_id', 'ASC')->orderBy('montant_min', 'ASC')->findAll();
        $data['types'] = $typeModel->findAll();

        return view('admin/baremes/index', $data);
    }

    public function store()
    {
        $model = new BaremeFraisModel();
        $data = [
            'type_transaction_id' => (int) $this->request->getPost('type_transaction_id'),
            'montant_min' => (int) $this->request->getPost('montant_min'),
            'montant_max' => (int) $this->request->getPost('montant_max'),
            'frais' => (int) $this->request->getPost('frais'),
        ];

        $model->insert($data);
        return redirect()->to('/admin/baremes')->with('success', 'Barème ajouté.');
    }

    public function update($id)
    {
        $model = new BaremeFraisModel();
        $data = [
            'type_transaction_id' => (int) $this->request->getPost('type_transaction_id'),
            'montant_min' => (int) $this->request->getPost('montant_min'),
            'montant_max' => (int) $this->request->getPost('montant_max'),
            'frais' => (int) $this->request->getPost('frais'),
        ];

        $model->update($id, $data);
        return redirect()->to('/admin/baremes')->with('success', 'Barème modifié.');
    }

    public function delete($id)
    {
        $model = new BaremeFraisModel();
        $model->delete($id);
        return redirect()->to('/admin/baremes')->with('success', 'Barème supprimé.');
    }
}
