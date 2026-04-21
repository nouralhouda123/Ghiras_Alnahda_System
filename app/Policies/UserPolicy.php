<?php

// app/Policies/UserPolicy.php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function create(User $authenticatedUser, string $role, int $departmentId): bool
    {
        if ($authenticatedUser->hasRole('Super Admin')) {
            return true;
        }

        if ($authenticatedUser->hasRole('Campaign Manager')) {
            $allowedRoles = ['Campaign Employee', 'Volunteer Manager'];

            return in_array($role, $allowedRoles);
               // && $departmentId === $authenticatedUser->department_id;
        }

        if ($authenticatedUser->hasRole('Evaluation Manager')) {
            $allowedRoles = ['Evaluation Officer'];

            return in_array($role, $allowedRoles)
                && $departmentId === $authenticatedUser->department_id;
        }

        return false;
    }
    public function update(User $authUser, User $user): bool
    {
        if ($authUser->hasRole('Super Admin')) {
            return true;
        }

        if ($authUser->hasRole('Campaign Manager')) {

            return in_array(
                    $user->getRoleNames()->first(),
                    ['Campaign Employee', 'Volunteer Manager']
                ) ;
                //&& $authUser->department_id == $user->department_id;
        }

        if ($authUser->hasRole('Evaluation Manager')) {

            return $user->getRoleNames()->first() === 'Evaluation Officer';
             //   && $authUser->department_id == $user->department_id;
        }

        return false;
    }
    public function view(User $authUser, User $user): bool
    {
        if ($authUser->hasRole('Super Admin')) {
            return true;
        }

        if ($authUser->hasRole('Campaign Manager')) {

            return in_array(
                $user->getRoleNames()->first(),
                ['Campaign Employee', 'Volunteer Manager']
            ) ;
            //&& $authUser->department_id == $user->department_id;
        }

        if ($authUser->hasRole('Evaluation Manager')) {

            return $user->getRoleNames()->first() === 'Evaluation Officer';
            //   && $authUser->department_id == $user->department_id;
        }

        return false;
    }
}
