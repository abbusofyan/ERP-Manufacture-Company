<?php

namespace App\Policies;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransferPolicy
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
        return $user->can('view-transfer');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Transfer $transfer
     * @return bool
     */
    public function view(User $user, Transfer $transfer): bool
    {
        return $user->can('view-transfer');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-transfer');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-transfer');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-transfer');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Transfer $transfer
     * @return void
     */
    public function restore(User $user, Transfer $transfer): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Transfer $transfer
     * @return void
     */
    public function forceDelete(User $user, Transfer $transfer): void
    {
    }
}
