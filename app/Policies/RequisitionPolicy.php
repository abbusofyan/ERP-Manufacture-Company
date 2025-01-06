<?php

namespace App\Policies;

use App\Models\Requisition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequisitionPolicy
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
        return $user->can('view-requisition');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Requisition $requisition
     * @return bool
     */
    public function view(User $user, Requisition $requisition): bool
    {
        return $user->can('view-requisition');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-requisition');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-requisition');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-requisition');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Requisition $requisition
     * @return void
     */
    public function restore(User $user, Requisition $requisition): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Requisition $requisition
     * @return void
     */
    public function forceDelete(User $user, Requisition $requisition): void
    {
    }
}
