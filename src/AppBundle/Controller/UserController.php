<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\UserPasswordType;
use AppBundle\Form\UserSettingsType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("user")
 */
class UserController extends Controller
{
    private $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $this->em->persist($user);

            try {
                $this->em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Username already taken.');

                return $this->render(
                    'user/register.html.twig',
                    ['form' => $form->createView()]
                );
            }

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/register.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @param User $user
     * @Route("/settings", name="user_settings")
     */
    public function changeSettingsAction(Request $request, UserInterface $user)
    {
        $settingsForm = $this->createForm(UserSettingsType::class, $user);
        $settingsForm->handleRequest($request);

        if ($settingsForm->isSubmitted() && $settingsForm->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/changeSettings.html.twig',
            ['settings_form' => $settingsForm->createView()]
        );
    }

    /**
     * @param User $user
     * @Route("/change-password", name="user_change_password")
     */
    public function changePasswordAction(Request $request, UserInterface $user)
    {
        $passwordForm = $this->createForm(UserPasswordType::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $this->em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/changePassword.html.twig',
            ['password_form' => $passwordForm->createView()]
        );
    }
}
