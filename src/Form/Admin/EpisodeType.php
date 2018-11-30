<?php

namespace App\Form\Admin;

use App\Form\CommandObject\Admin\EpisodeDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EpisodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', NumberType::class)
            ->add('title')
            ->add('subtitle')
            ->add('backgroundImage', FileType::class, [
                'required' => false,
            ])
            ->add('backgroundImageCreditBy')
            ->add('backgroundImageCreditUrl')
            ->add('backgroundImageCreditDescription')

            ->add('contentHtml', HiddenType::class, [
                'required' => false,
            ])
            ->add('itunesSummaryHtml', HiddenType::class, [
                'required' => false,
            ])
            ->add('transcriptHtml', TextareaType::class, [
                'required' => false,
            ])
            ->add('transcriptText', TextareaType::class, [
                'required' => false,
            ])
            ->add('published', DateTimeType::class, [
                'required' => false,
            ])

            ->add('pristineMedia', FileType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EpisodeDto::class,
        ]);
    }
}
