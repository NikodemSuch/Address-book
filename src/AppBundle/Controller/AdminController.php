<?php

namespace AppBundle\Controller;

use AppBundle\Repository\ContactRepository;
use AppBundle\Service\UserManager;
use AppBundle\Exception\UserNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("admin")
 */
class AdminController extends Controller
{
    private $userManager;
    private $contactRepository;

    public function __construct(UserManager $userManager, ContactRepository $contactRepository)
    {
        $this->userManager = $userManager;
        $this->contactRepository = $contactRepository;
    }

    /**
     * @Route("/change-password", name="admin_change_password")
     */
    public function changePasswordAdminAction(Request $request)
    {
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('name', TextType::class)
            ->add('password', PasswordType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $username = $data['name'];
            $newPlainPassword = $data['password'];

            try {
                $this->userManager->changePassword($username, $newPlainPassword);
            } catch (UserNotFoundException $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/changePassword.html.twig',
            ['password_form' => $form->createView()]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/index", name="admin_contact_index")
     */
    public function indexAdminAction()
    {
        $contacts = $this->contactRepository->findAll();

        return $this->render('default/main.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * @Route("/recycle-bin", name="admin_contact_recycle_index")
     */
    public function indexBinAdminAction()
    {
        $contacts = $this->contactRepository->findBy([
            'inRecycleBin' => true
        ]);

        return $this->render('contact/recycleBin.html.twig', [
            'contacts' => $contacts,
        ]);
    }
}
