<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use AppBundle\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $password = $this->passwordEncoder->encodePassword($user, 'admin');

        $user->setUsername("Admin");
        $user->setEmail("admin@gmail.com");
        $user->setPassword($password);
        $user->setRole(UserRole::ADMIN());

        $manager->persist($user);
        $manager->flush();
    }
}
