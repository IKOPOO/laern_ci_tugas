<?php

namespace App\Controllers;

use App\Models\DiscountModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $user;
    public function __construct()
    {
        helper('form');
        $this->user = new UserModel();
    }

    public function login()
    {

        if ($this->request->getPost()) {
            $rules = [
              'username' => 'required|min_length[6]',
              'password' => 'required|min_length[7]|numeric',
            ];

            if ($this->validate($rules)) {
                $username = $this->request->getVar('username');
                $password = $this->request->getVar('password');

                $dataUser = $this->user->where(['username' => $username])->first();

                if ($dataUser) {
                    if (password_verify($password, $dataUser['password'])) {
                        $sessionData = [
                          'username'   => $dataUser['username'],
                          'role'       => $dataUser['role'],
                          'isLoggedIn' => true
                        ];

                        $discountModel = new DiscountModel();
                        $today        = date('Y-m-d');
                        $discount     = $discountModel->where('tanggal', $today)->first();
                        
                        if($discount){
                            $sessionData['diskon'] = $discount['nominal'];
                        }

                        session()->set($sessionData);
                        return redirect()->to(base_url('/'));
                    } else {
                        session()->setFlashdata('failed', 'Kombinasi Username & Password Salah');

                        return redirect()->back();
                    }
                } else {
                    session()->setFlashdata('failed', 'Username Tidak Ditemukan');

                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', $this->validator->listErrors());

                return redirect()->back();
            }
        }

        return view('v_login');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('login');
    }
}
