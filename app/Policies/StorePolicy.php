<?php

namespace App\Policies;

use App\Models\job;
use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class StorePolicy
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
        return $user->can('view-store');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Store $store
     * @return bool
     */
    public function view(User $user, Store $store): bool
    {
        return $user->can('view-store');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-store');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-store');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-store');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Store $store
     * @return void
     */
    public function restore(User $user, Store $store): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Store $store
     * @return void
     */
    public function forceDelete(User $user, Store $store): void
    {
    }
}
