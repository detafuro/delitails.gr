<?php

namespace App\Policies;

use App\Models\User;

abstract class AdminPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(?User $user): bool { return $user?->isAdmin() ?? false; }
    public function view(?User $user, $model): bool { return $user?->isAdmin() ?? false; }
    public function create(?User $user): bool { return $user?->isAdmin() ?? false; }
    public function update(?User $user, $model): bool { return $user?->isAdmin() ?? false; }
    public function delete(?User $user, $model): bool { return $user?->isAdmin() ?? false; }
}
