<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @IsGranted("ROLE_USER")
 * @Route("contact")
 */
class ContactController extends Controller
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @Route("/new", name="contact_new")
     */
    public function newAction(Request $request, UserInterface $user)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setOwner($user);
            $this->em->persist($contact);
            $this->em->flush();

            return $this->redirectToRoute('contact_show', [
                'id' => $contact->getId()
            ]);
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param User $user
     * @Route("/{id}", name="contact_show")
     * @IsGranted("view", subject="contact")
     */
    public function showAction(UserInterface $user, Contact $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);

        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @param User $user
     * @Route("/{id}/edit", name="contact_edit")
     * @IsGranted("edit", subject="contact")
     */
    public function editAction(Request $request, Contact $contact, UserInterface $user)
    {
        $editForm = $this->createForm(ContactType::class, $contact);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->em->persist($contact);
            $this->em->flush();

            return $this->redirectToRoute('contact_show', [
                'id' => $contact->getId()
            ]);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/recycle", name="contact_recycle")
     * @IsGranted("delete", subject="contact")
     */
    public function recycleAction(Contact $contact)
    {
        $contact->setInRecycleBin(true);
        $this->em->persist($contact);
        $this->em->flush();

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/{id}/delete", name="contact_delete")
     * @Method("DELETE")
     * @IsGranted("delete", subject="contact")
     */
    public function deleteAction(Request $request, Contact $contact)
    {
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($contact);
            $this->em->flush();
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param Contact $contact
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Contact $contact)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contact_delete', ['id' => $contact->getId()]))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
