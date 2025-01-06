<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
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
        return $user->can('view-customer');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function view(User $user, Customer $customer): bool
    {
        return $user->can('view-customer');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-customer');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update-customer');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-customer');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Customer $customer
     * @return void
     */
    public function restore(User $user, Customer $customer): void
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Customer $customer
     * @return void
     */
    public function forceDelete(User $user, Customer $customer): void
    {
    }
}
