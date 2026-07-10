<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function logout()
    {
        Auth::guard('client')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('client.login');
    }

    public function approveProposal(int $proposalId)
    {
        $user = Auth::guard('client')->user();
        $proposal = \App\Models\Proposal::where('id', $proposalId)
            ->where('client_id', $user->client_id)
            ->firstOrFail();

        $proposal->approve($user, 'Müşteri portal üzerinden onayladı.');
        session()->flash('message', __('Teklif başarıyla onaylandı.'));
    }

    public function approveContract(int $contractId)
    {
        $user = Auth::guard('client')->user();
        $contract = \App\Models\Contract::where('id', $contractId)
            ->where('client_id', $user->client_id)
            ->firstOrFail();

        $contract->approve($user, 'Müşteri portal üzerinden imzaladı.');
        session()->flash('message', __('Sözleşme başarıyla imzalandı.'));
    }

    public function render()
    {
        $user = Auth::guard('client')->user();
        $client = $user->client;

        $activeProjects = $client->projects()->where('status', '!=', 'completed')->count();
        $totalProjects = $client->projects()->count();
        $recentInvoices = $client->invoices()->latest()->limit(5)->get();

        // Pending proposals and contracts for client signature
        $pendingProposals = $client->proposals()->where('status', 'sent')->get();
        $pendingContracts = $client->contracts()->where('status', 'sent')->get();

        return view('livewire.client.dashboard', [
            'clientUser' => $user,
            'client' => $client,
            'activeProjects' => $activeProjects,
            'totalProjects' => $totalProjects,
            'recentInvoices' => $recentInvoices,
            'pendingProposals' => $pendingProposals,
            'pendingContracts' => $pendingContracts,
        ])->layout('layouts.client', ['title' => 'Portal']);
    }
}
