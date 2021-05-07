<?php

namespace App\Security\Voter;

use App\Entity\Duck;
use App\Entity\Quack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class QuackVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['CREATE_QUACK', 'COMMENT_QUACK', 'EDIT_QUACK', 'DELETE_QUACK']);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof Duck) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'CREATE_QUACK':
                return true;
            case 'COMMENT_QUACK':
                return true;
            case 'EDIT_QUACK':
                return $subject instanceof Quack && ($user->getId() === $subject->getAuthor()->getId());
            case 'DELETE_QUACK':
                return $subject instanceof Quack && ($user->getId() === $subject->getAuthor()->getId() || $user->isAdmin());
        }

        return false;
    }
}
