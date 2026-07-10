<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Projects extends Component
{
    public function render()
    {
        $client = Auth::guard('client')->user()->client;
        $projects = $client->projects()->latest()->get();

        return view('livewire.client.projects', [
            'projects' => $projects,
        ])->layout('layouts.client', ['title' => 'Projeler']);
    }
}
