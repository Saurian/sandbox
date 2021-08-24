<?php


namespace App\Security;

use Nette;

class AuthorizatorFactory
{

    public static function create(): Nette\Security\Permission
    {
        $acl = new Nette\Security\Permission;
        $acl->addRole('guest');
        $acl->addRole('member', 'guest');
        $acl->addRole('admin', 'guest');

        $acl->addResource('Homepage');
        $acl->addResource('Admin');
        $acl->addResource('User');

        $acl->allow('guest', 'User');
        $acl->allow('admin', 'Admin');
        $acl->allow('admin', 'Homepage');

        return $acl;
    }
}
