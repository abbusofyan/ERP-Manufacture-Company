<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
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
        return $user->can('view-product');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function view(User $user, Product $product): bool
    {
        return $user->can('view-product');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-product');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-product');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-product');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Product $product
     * @return void
     */
    public function restore(User $user, Product $product): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Product $product
     * @return void
     */
    public function forceDelete(User $user, Product $product): void
    {
    }
}
