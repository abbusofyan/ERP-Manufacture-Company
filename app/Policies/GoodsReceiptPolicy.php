<?php

namespace App\Policies;

use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoodsReceiptPolicy
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
        return $user->can('view-goods-receipt');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param GoodsReceipt $goodsReceipt
     * @return bool
     */
    public function view(User $user, GoodsReceipt $goodsReceipt): bool
    {
        return $user->can('view-goods-receipt');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-goods-receipt');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-goods-receipt');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-goods-receipt');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param GoodsReceipt $goodsReceipt
     * @return void
     */
    public function restore(User $user, GoodsReceipt $goodsReceipt): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param GoodsReceipt $goodsReceipt
     * @return void
     */
    public function forceDelete(User $user, GoodsReceipt $goodsReceipt): void
    {
    }
}
