<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-location');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Location $location
     * @return bool
     */
    public function view(User $user, Location $location): bool
    {
        return $user->can('view-location');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-location');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-location');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-location');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Location $location
     * @return void
     */
    public function restore(User $user, Location $location): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Location $location
     * @return void
     */
    public function forceDelete(User $user, Location $location): void
    {
    }
}
