<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view-vendor');
    }

    public function view(User $user, Vendor $vendor): bool
    {
        return $user->can('view-vendor');
    }

    public function create(User $user): bool
    {
        return $user->can('create-vendor');
    }

    public function update(User $user): bool
    {
        return $user->can('update-vendor');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete-vendor');
    }

    public function restore(User $user, Vendor $vendor): void
    {
    }

    public function forceDelete(User $user, Vendor $vendor): void
    {
    }
}
