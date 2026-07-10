<?php

namespace App\Livewire\Admin\Auth;

use Livewire\Component;

class EmailVerifyNotice extends Component
{
    public bool $sent = false;

    public function resend()
    {
        // Force synchronous sending — no queue worker dependency
        $originalConnection = config('queue.default');
        config(['queue.default' => 'sync']);

        auth()->user()->sendEmailVerificationNotification();

        config(['queue.default' => $originalConnection]);

        $this->sent = true;
    }

    public function render()
    {
        // Zaten doğrulanmışsa dashboard'a yönlendir
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard');
        }

        return view('livewire.admin.auth.email-verify-notice')
            ->layout('livewire.admin.auth.login-layout');
    }
}
