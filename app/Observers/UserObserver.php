<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        if ($user->email !== 'admin@admin.com') {
            $user->is_salarie = true;
        }
    }
}
