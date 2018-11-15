<?php

namespace App\Form;

use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPath;

class FileType extends AbstractType
{
    /**
     * @var FlysystemAssetManager
     */
    private $flysystemAssetManager;

    /**
     * FileType constructor.
     * @param FlysystemAssetManager $flysystemAssetManager
     */
    public function __construct(FlysystemAssetManager $flysystemAssetManager)
    {
        $this->flysystemAssetManager = $flysystemAssetManager;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_delete' => true,
            'download_uri' => true,
            'download_label' => 'download',
            'delete_label' => 'form.label.delete',
            'error_bubbling' => false,
        ]);

        $resolver->setAllowedTypes('allow_delete', 'bool');
        $resolver->setAllowedTypes('download_uri', ['bool', 'string', 'callable']);
        $resolver->setAllowedTypes('download_label', ['bool', 'string', 'callable', PropertyPath::class]);
        $resolver->setAllowedTypes('error_bubbling', 'bool');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', Type\FileType::class, [
            'required' => $options['required'],
            'label' => $options['download_label'],
            'label' => $options['label'],
            'attr' => $options['attr'],
            'data_class' => null,
        ]);

        $builder->addModelTransformer(new FileTransformer());
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $object = $form->getParent()->getData();
        $value = $form->getData();
        $view->vars['object'] = $object;

        $view->vars['download_uri'] = null;
        if ($options['download_uri'] && $object && $value) {
            $view->vars['download_uri'] = $this->flysystemAssetManager->getUrl($value);
        }

        $view->vars['download_label'] = $options['download_label'];
    }

    public function getBlockPrefix(): string
    {
        return 'astrocasts_file';
    }
}
