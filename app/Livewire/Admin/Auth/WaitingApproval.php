<?php

namespace App\Livewire\Admin\Auth;

use Livewire\Component;

class WaitingApproval extends Component
{
    public function mount()
    {
        $user = auth()->user();
        
        // Eğer kullanıcı onaylanmışsa veya webmaster ise direkt dashboard'a gönder
        if ($user->isWebmaster() || ($user->tenant && $user->tenant->status === 'active')) {
            return redirect()->route('admin.dashboard');
        }
    }

    public function render()
    {
        return view('livewire.admin.auth.waiting-approval')
            ->layout('livewire.admin.auth.login-layout');
    }
}
