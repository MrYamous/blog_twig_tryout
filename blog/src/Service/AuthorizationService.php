<?php


namespace App\Service;


use Symfony\Component\Security\Core\User\UserInterface;

class AuthorizationService
{

    public function isUserAllowedToDeleteArticle(UserInterface $user): bool
    {
        return false !== array_search('ROLE_ADMIN', $user->getRoles());
    }

}