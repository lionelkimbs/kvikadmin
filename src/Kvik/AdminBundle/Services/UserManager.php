<?php

namespace Kvik\AdminBundle\Services;

use Kvik\AdminBundle\Entity\User;

class UserManager{

    /**
     * Give the good role to $user
     * @param User $user
     */
    public function giveMeRole(User $user){
        //Remove all roles to still get only one role by user
        foreach ($user->getRoles() as $role){ $user->removeRole($role); }

        switch ($user->getDisplayedRole()){
            case 'editeur':
                $user->addRole('ROLE_EDITOR');
                break;
            case 'admin':
                $user->addRole('ROLE_ADMIN');
                break;
            case 'super-admin':
                $user->addRole('ROLE_SUPER_ADMIN');
                break;
            default:
                $user->addRole('ROLE_USER');
                break;
        }
    }
}