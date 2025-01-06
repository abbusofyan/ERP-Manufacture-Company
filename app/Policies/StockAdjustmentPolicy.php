<?php

namespace App\Policies;

use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockAdjustmentPolicy
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
        return $user->can('view-stock');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param StockAdjustment $stockAdjustment
     * @return bool
     */
    public function view(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->can('view-stock');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-stock');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-stock');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-stock');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param StockAdjustment $stockAdjustment
     * @return void
     */
    public function restore(User $user, StockAdjustment $stockAdjustment): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param StockAdjustment $stockAdjustment
     * @return void
     */
    public function forceDelete(User $user, StockAdjustment $stockAdjustment): void
    {
    }
}
