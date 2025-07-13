<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Vich\UploaderBundle\Form\Type\VichImageType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class,[
                'label' => 'registrationForm.firstname'
            ])
            ->add('lastname', TextType::class,[
                'label' => 'registrationForm.lastname'
            ])
            ->add('email', TextType::class, [
                'label' => 'registrationForm.email'])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'registrationForm.agreeTerms',
                'constraints' => [
                    new IsTrue([
                        //'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'label' => 'registrationForm.password',
                'constraints' => [
                    new NotBlank([
                        //'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        //'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('imageFile', VichImageType::class, [
        'required' => false,
        'label' => 'registrationForm.photoDeProfil',
        'download_uri' => false,
        'image_uri' => false,
    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
