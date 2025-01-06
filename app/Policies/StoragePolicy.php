<?php

namespace App\Policies;

use App\Models\Storage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoragePolicy
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
        return $user->can('view-storage');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Storage $storage
     * @return bool
     */
    public function view(User $user, Storage $storage): bool
    {
        return $user->can('view-storage');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-storage');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-storage');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-storage');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Storage $storage
     * @return void
     */
    public function restore(User $user, Storage $storage): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Storage $storage
     * @return void
     */
    public function forceDelete(User $user, Storage $storage): void
    {
    }
}
