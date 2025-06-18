<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Length;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        /*
            ->add('title', TextType::class
            ,['label'=>'Titre', // замена имени поля
                'constraints' => new Length(min: 10, minMessage: "Valeur trop petite, 10 minimum")]
               )
                
            ->add('slug', HiddenType::class,[
                'required'=>false
            ])
            ->add('content', TextareaType::class)
           ->add('createdAt', null, ['widget' => 'single_text',])
            ->add('updatedAt', null, ['widget' => 'single_text',])
            ->add('duration')
            ->add('imageName'
            , TextType::class, ['label' => 'Image (url)','required' => false, // поле не обязательно для заполнения]
            )
            ->add('save', SubmitType::class
            , ['label'=>'Envoyer']
            ) кнопка для формы, по умолчанию название Save, при 'label'=>'Envoyer' меняется название на Envoyer
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))*/

            ->add('title', TextType::class, [
                'label' => 'recipeForm.title'])
            ->add('slug', HiddenType::class)
            ->add('content', TextareaType::class, [
                'label' => 'recipeForm.content']) 
            ->add('duration', TextType::class, [
                'label' => 'recipeForm.duration'])
            ->add('imageName', TextType::class, [
                'label' => 'recipeForm.imageName'])   
            ->add('save', SubmitType::class, [
                'label' => 'recipeForm.save']) // кнопка для формы, по умолчанию название Save, при 'label'=>'Envoyer' меняется название на Envoyer
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))    
            ;
    }

    public function autoSlug(PreSubmitEvent $event): void{
        $data = $event->getData();
        $slugger = new AsciiSlugger();
        if(empty($data['slug']) || $data['slug'] != strtolower($slugger->slug($data['title']))){
            $data['slug'] = strtolower($slugger->slug($data['title']));
            $event->setData($data);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
