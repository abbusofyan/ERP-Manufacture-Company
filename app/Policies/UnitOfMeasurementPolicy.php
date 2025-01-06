<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UnitOfMeasurement;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitOfMeasurementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view-uom');
    }

    public function view(User $user, UnitOfMeasurement $uom): bool
    {
        return $user->can('view-uom');
    }

    public function create(User $user): bool
    {
        return $user->can('create-uom');
    }

    public function update(User $user): bool
    {
        return $user->can('update-uom');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete-uom');
    }

    public function restore(User $user, UnitOfMeasurement $uom): void
    {
    }

    public function forceDelete(User $user, UnitOfMeasurement $uom): void
    {
    }
}
