<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\Editor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\BookStatus;
use Symfony\Component\Form\Extension\Core\Type as CoreType;
use Symfony\Component\Validator\Constraints as Assert;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', CoreType\TextType::class)
            ->add('isbn', CoreType\TextType::class, [
                'required' => false,
            ])
            ->add('cover', CoreType\UrlType::class, [
                'required' => false,
            ])
            ->add('editedAt', CoreType\DateType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('plot', CoreType\TextareaType::class, [
                'required' => false,
            ])
            ->add('pageNumber', CoreType\IntegerType::class, [
                'required' => false,
            ])
            ->add('status', CoreType\EnumType::class, [
                'class' => BookStatus::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'id',
                'multiple' => true,
                'by_reference' => false,
                'required' => false,
            ])
            ->add('editor', EntityType::class, [
                'class' => Editor::class,
                'required' => false,
                'choice_label' => 'id',
            ])
            ->add('comments', EntityType::class, [
                'class' => Comment::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false,
            ])
            ->add('certification', CoreType\CheckboxType::class, [
                'mapped' => false, // indique de ne pas sauvegarder ce champ en bdd
                'label' => "Je certifie l'exactitude des informations fournies",
                'constraints' => [
                    new Assert\IsTrue(message: "Vous devez cocher la case pour ajouter un livre."),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
