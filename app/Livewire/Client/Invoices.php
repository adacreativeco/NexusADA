<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Invoices extends Component
{
    public function render()
    {
        $client = Auth::guard('client')->user()->client;
        $invoices = $client->invoices()->latest()->get();

        return view('livewire.client.invoices', [
            'invoices' => $invoices,
        ])->layout('layouts.client', ['title' => 'Faturalar']);
    }
}
