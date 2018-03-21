<?php

namespace AppBundle\Controller;

use AppBundle\Repository\ContactRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController extends Controller
{
    private $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @param User $user
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, UserInterface $user = null)
    {
        if ($user) {
            $contacts = $this->contactRepository->findBy(['owner' => $user]);

            return $this->render('default/main.html.twig', [
                'contacts' => $contacts,
            ]);
        }

        return $this->render('default/main.html.twig');
    }

    /**
     * @param User $user
     * @Route("/recycle-bin", name="contact_recycle_index")
     */
    public function indexBinAction(UserInterface $user)
    {
        $contacts = $this->contactRepository->findBy([
            'owner' => $user,
            'inRecycleBin' => true
        ]);

        return $this->render('contact/recycleBin.html.twig', [
            'contacts' => $contacts,
        ]);
    }
}
