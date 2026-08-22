<?php

namespace App\Livewire\Client;

use App\Models\BrandAsset;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssetReviewer extends Component
{
    public int $assetId;
    public ?BrandAsset $asset = null;
    public string $commentText = '';
    public ?float $pinX = null;
    public ?float $pinY = null;

    public function mount(int $assetId)
    {
        $this->assetId = $assetId;
        $user = Auth::guard('client')->user();
        $this->asset = BrandAsset::where('id', $assetId)
            ->where('tenant_id', $user->client->tenant_id ?? 1)
            ->firstOrFail();
    }

    public function addPinComment()
    {
        $this->validate([
            'commentText' => 'required|min:3',
        ]);

        $user = Auth::guard('client')->user();

        Comment::create([
            'tenant_id' => $this->asset->tenant_id ?? 1,
            'commentable_type' => BrandAsset::class,
            'commentable_id' => $this->asset->id,
            'user_id' => null,
            'body' => $this->commentText,
            'metadata' => [
                'pin_x' => $this->pinX,
                'pin_y' => $this->pinY,
                'author_name' => $user->name,
                'author_role' => 'client',
            ],
        ]);

        $this->commentText = '';
        $this->pinX = null;
        $this->pinY = null;
        session()->flash('success', 'Geri bildiriminiz başarıyla iletildi.');
    }

    public function render()
    {
        $comments = Comment::where('commentable_type', BrandAsset::class)
            ->where('commentable_id', $this->assetId)
            ->latest()
            ->get();

        return view('livewire.client.asset-reviewer', [
            'comments' => $comments,
        ])->layout('layouts.client', ['title' => 'Tasarım İnceleme']);
    }
}
