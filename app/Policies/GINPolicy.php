<?php

namespace App\Policies;

use App\Models\GIN;
use App\Models\Storage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GINPolicy
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
        return $user->can('view-gin');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param GIN $GIN
     * @return bool
     */
    public function view(User $user, GIN $GIN): bool
    {
        return $user->can('view-gin');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-gin');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-gin');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-gin');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param GIN $GIN
     * @return void
     */
    public function restore(User $user, GIN $GIN): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param GIN $GIN
     * @return void
     */
    public function forceDelete(User $user, GIN $GIN): void
    {
    }
}
