<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountDeletionService
{
    public function deleteAccount(User $user)
    {
        // 1. Owner kontrolü
        if ($user->tenant) {
            $owner = $user->tenant->users()->oldest()->first();
            if ($owner && $owner->id === $user->id) {
                throw new \Exception("Önce başka bir owner atayın veya tenant'ı silin");
            }
        }

        DB::transaction(function () use ($user) {
            // 2. Anonimleştirme: Görev atamaları
            Task::where('assigned_to', $user->id)->update(['assigned_to' => null]);

            // 3. Anonimleştirme: Yorumlar
            Comment::where('user_id', $user->id)->update(['user_id' => null]);

            // 4. Kullanıcı profilini anonimleştir
            $user->name = '[Silinmiş Kullanıcı]';
            $user->email = 'deleted_' . $user->id . '_' . time() . '@nexus.local';
            $user->password = bcrypt(Str::random(40));
            
            $user->save();

            // 5. Soft Delete
            $user->delete();
        });
    }
}
