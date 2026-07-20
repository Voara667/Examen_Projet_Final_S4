<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AuthController extends BaseController
{
    public function form()
    {
        if (session()->has('admin')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/auth/login');
    }

    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $model = new AdminModel();
        $admin = $model->where('email', $email)->first();

        if ($admin && password_verify($password, $admin['password'])) {
            session()->set('admin', [
                'id' => $admin['id'],
                'nom' => $admin['nom'],
                'email' => $admin['email'],
            ]);
            return redirect()->to('/admin/dashboard')->with('success', 'Connexion opérateur réussie.');
        }

        return redirect()->back()->withInput()->with('error', 'Identifiants invalides.');
    }

    public function logout()
    {
        session()->remove('admin');
        return redirect()->to('/admin/login')->with('success', 'Vous avez été déconnecté.');
    }
}
