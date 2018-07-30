<?php

namespace Bonn\Generator\Sylius;

use Bonn\Generator\NameResolver;
use Nette\PhpGenerator\PhpNamespace;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

final class FormGenerator extends AbstractSyliusGenerator
{
    public function generate(array $options)
    {
        $options = $this->optionResolver->resolve($options);

        $class = $options['class'];
        $this->ensureClassExists($class);

        $classNamespace = new PhpNamespace($this->getFormTypeNameSpace($class));
        $className = NameResolver::resolveOnlyClassName($class);

        $formClass = $classNamespace->addClass($className. 'Type');
        $classNamespace->addUse(AbstractResourceType::class);
        $classNamespace->addUse(FormBuilderInterface::class);
        $formClass->addExtend(AbstractResourceType::class);
        $buildForm = $formClass->addMethod('buildForm');
        $buildForm->addParameter('builder')->setTypeHint(FormBuilderInterface::class);
        $buildForm->addParameter('options')->setTypeHint('array');
        $buildForm->setReturnType('void');
        $buildForm->setBody('//$builder');

        $service = new \SimpleXmlElement('<service />');
        $service->addAttribute('id', $options['resource_name'] . '.form_type.' . NameResolver::camelToUnderScore($className));
        $service->addAttribute('class', $formClass->getNamespace()->getName() . '\\' . $formClass->getName());

        $service->addChild('argument', '%' . sprintf('%s.model.%s.class', $options['resource_name'], NameResolver::camelToUnderScore($className)) . '%');
        $validationGroup = $service->addChild('argument');
        $validationGroup->addAttribute('type', 'collection');
        $validationGroup->addChild('argument', $options['resource_name']);

        $tag = $service->addChild('tag');
        $tag->addAttribute('name', 'form.type');

        $this->storage->add($formClass, $formClass->getName());
        $this->storage->add($service, $formClass->getName() . '-services');
    }

    /**
     * @param string $modelClass
     * @return mixed
     */
    public static function getFormTypeNameSpace(string $modelClass)
    {
        $namespace = NameResolver::resolveNamespace($modelClass);
        return str_replace('Model', 'Form\\Type', $namespace);
    }
}
