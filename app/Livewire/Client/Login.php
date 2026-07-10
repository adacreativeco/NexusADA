<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('client')->attempt(
            ['email' => $this->email, 'password' => $this->password, 'is_active' => true],
            $this->remember
        )) {
            $user = Auth::guard('client')->user();
            $user->update(['last_login_at' => now()]);

            session()->regenerate();
            return redirect()->route('client.dashboard');
        }

        $this->addError('email', 'Bu kimlik bilgileri kayıtlarımızla eşleşmiyor.');
    }

    public function render()
    {
        return view('livewire.client.login')
            ->layout('layouts.client-auth', ['title' => 'Müşteri Girişi']);
    }
}
