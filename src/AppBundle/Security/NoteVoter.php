<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Entity\Note;
use AppBundle\Enum\UserRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NoteVoter extends Voter
{
    const VIEW    = 'view';
    const EDIT    = 'edit';
    const DELETE  = 'delete';

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::DELETE))) {
            return false;
        }

        if (!($subject instanceof Note)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $this->canManage($subject, $user);
            default:
                throw new \LogicException('This code should not be reached!');
        }
    }

    private function canManage(Note $subject, User $user): bool
    {
        if ($user->getRole() == UserRole::ADMIN()) {
            return true;
        }

        return $user === $subject->getContact()->getOwner();
    }
}
