<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[20]|is_unique[users.username]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];

            if ($this->validate($rules)) {
                $data = [
                    'username' => $this->request->getPost('username'),
                    'email'    => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
                ];

                try {
                    $this->userModel->insert($data);
                    return redirect()->to('/auth/login')->with('success', 'Registration successful! Please login.');
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'An error occurred while registering. Please try again.');
                }
            } else {
                return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
            }
        }

        return view('auth/register');
    }

    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]'
            ];

            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                $user = $this->userModel->where('email', $email)->first();

                if ($user && password_verify($password, $user['password'])) {
                    // Set session data
                    $sessionData = [
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'isLoggedIn' => true
                    ];
                    session()->set($sessionData);

                    return redirect()->to('/songs')->with('success', 'Welcome back, ' . $user['username'] . '!');
                } else {
                    return redirect()->back()->with('error', 'Invalid email or password');
                }
            } else {
                return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
            }
        }

        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'You have been logged out successfully');
    }
} 