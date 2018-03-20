<?php

namespace AppBundle\Form;

use AppBundle\Entity\Contact;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Name'],
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Address'],
            ])
            ->add('phoneNumbers', CollectionType::class, [
                'label' => 'Phone numbers:',
                'label_attr' => ['class' => 'form-control-label'],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_data' => false,
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => ['class' => 'phoneNumber'],
                ],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $formData = $event->getData();
                if (array_key_exists('phoneNumbers', $formData)) {
                    $filteredNumbers = array();

                    foreach ($formData['phoneNumbers'] as $phoneNumber) {
                        $phoneNumber = preg_replace('/\-(/', '(', $phoneNumber);
                        $phoneNumber = preg_replace('/\)-/', ')', $phoneNumber);
                        // add space before "(" and after ")"
                        // we do this first because we don't want to bother checking if "(" is first character or ")" is last
                        // we wouldn't want to add spaces there if we didn't clear whitespaces few lines after
                        $phoneNumber = preg_replace('/\(/', ' (', $phoneNumber);
                        $phoneNumber = preg_replace('/\)/', ') ', $phoneNumber);
                        // remove whitespaces from ends of string
                        $phoneNumber = trim($phoneNumber);
                        // join multiple spaces into one
                        $phoneNumber = preg_replace('/\s+/', ' ', $phoneNumber);
                        // join multiple dashes into one
                        $phoneNumber = preg_replace('/-+/', '-', $phoneNumber);
                        $phoneNumber = preg_replace('/-(\s+)-/', '-', $phoneNumber);
                        // add space before ")" and after "("
                        $phoneNumber = preg_replace('/\( /', '(', $phoneNumber);
                        $phoneNumber = preg_replace('/ \)/', ')', $phoneNumber);

                        array_push($filteredNumbers, $phoneNumber);
                    }

                    $formData['phoneNumbers'] = $filteredNumbers;
                    $formData['phoneNumbers'] = array_unique($formData['phoneNumbers']);
                    $formData['phoneNumbers'] = array_values($formData['phoneNumbers']);
                    $event->setData($formData);
                }
            })
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'appbundle_contact';
    }
}
