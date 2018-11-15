<?php

namespace App\Form;

use App\FlysystemAssetManager\FlysystemAssetManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPath;

class ImageType extends FileType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['image_uri'] = $view->vars['download_uri'];
    }

    public function getBlockPrefix(): string
    {
        return 'astrocasts_image';
    }
}
