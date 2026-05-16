<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('full_name', TextType::class, [
                'label'       => 'Full Name',
                'constraints' => [new NotBlank(message: 'Please enter your full name')],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'Email Address',
                'constraints' => [
                    new NotBlank(message: 'Please enter your email'),
                    new Email(message: 'Invalid email format'),
                ],
            ])
            ->add('role', ChoiceType::class, [
                'label'   => 'Choose your role',
                'choices' => ['Patient' => 'patient', 'Doctor' => 'doctor'],
                'constraints' => [new NotBlank(message: 'Please select a role')],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'first_options'   => ['label' => 'Password (min. 8 characters)'],
                'second_options'  => ['label' => 'Confirm Password'],
                'invalid_message' => 'Passwords do not match',
                'constraints'     => [
                    new NotBlank(message: 'Please enter a password'),
                    new Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters',),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
