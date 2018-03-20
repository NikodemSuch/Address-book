<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use AppBundle\Entity\User;
use AppBundle\Form\NoteType;
use AppBundle\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @IsGranted("ROLE_USER")
 * @Route("note")
 */
class NoteController extends Controller
{
    private $em;
    private $contactRepository;

    public function __construct(EntityManagerInterface $em, ContactRepository $contactRepository)
    {
        $this->em = $em;
        $this->contactRepository = $contactRepository;
    }

    /**
     * @param User $user
     * @Route("/new", name="note_new")
     */
    public function newAction(Request $request, UserInterface $user)
    {
        $note = new Note();
        $contacts = $this->contactRepository->findBy(['owner' => $user]);

        if ($request->query->get('contact')) {
            $contact = $this->contactRepository->find($request->query->get('contact'));
            $note->setContact($contact);
        }

        $form = $this->createForm(NoteType::class, $note, ['contacts' => $contacts]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($note);
            $this->em->flush();

            return $this->redirectToRoute('note_show', [
                'id' => $note->getId()
            ]);
        }

        return $this->render('note/new.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="note_show")
     * @IsGranted("view", subject="note")
     */
    public function showAction(Note $note)
    {
        $deleteForm = $this->createDeleteForm($note);

        return $this->render('note/show.html.twig', [
            'note' => $note,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @param User $user
     * @Route("/{id}/edit", name="note_edit")
     * @IsGranted("edit", subject="note")
     */
    public function editAction(Request $request, Note $note, UserInterface $user)
    {
        $contacts = $this->contactRepository->findBy(['owner' => $user]);
        $editForm = $this->createForm(NoteType::class, $note, ['contacts' => $contacts]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('note_show', [
                'id' => $note->getId()
            ]);
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="note_delete")
     * @Method("DELETE")
     * @IsGranted("delete", subject="note")
     */
    public function deleteAction(Request $request, Note $note)
    {
        $form = $this->createDeleteForm($note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($note);
            $this->em->flush();
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param Note $note
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Note $note)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('note_delete', ['id' => $note->getId()]))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
