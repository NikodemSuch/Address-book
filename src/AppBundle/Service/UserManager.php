<?php

namespace AppBundle\Service;

use AppBundle\Enum\UserRole;
use AppBundle\Repository\UserRepository;
use AppBundle\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    private $em;
    private $passwordEncoder;
    private $userRepository;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function changePassword(string $username, string $newPlainPassword)
    {
        $user = $this->userRepository->loadUserByUsername($username);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $newPassword = $this->passwordEncoder->encodePassword($user, $newPlainPassword);
        $user->setPassword($newPassword);

        $this->em->persist($user);
        $this->em->flush();
    }

    public function changeRole(string $username, UserRole $newRole)
    {
        $user = $this->userRepository->loadUserByUsername($username);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $user->setRole($newRole);

        $this->em->persist($user);
        $this->em->flush();
    }
}
