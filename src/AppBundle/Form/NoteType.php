<?php

namespace AppBundle\Form;

use AppBundle\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contacts = $options['contacts'];
        $builder
            ->add('content', TextAreaType::class)
            ->add('createdOn', DateTimeType::class, [
                'label' => 'Date and Time:',
                'label_attr' => ['class' => 'form-control-label'],
            ])
            ->add('contact', EntityType::class, [
                'class' => 'AppBundle:Contact',
                'choices' => $contacts,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('contacts');
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'appbundle_note';
    }
}
