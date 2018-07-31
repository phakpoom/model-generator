<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\NameResolver;
use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

class CollectionPropType implements PropTypeInterface, ConstructorAwareInterface, DoctrineMappingInterface
{
    private $name;
    private $fullInterfaceName;
    private $interfaceName;
    private $uses = [];

    private function __construct(string $name, ?string $interfaceName = null)
    {
        $this->name = $name;
        $this->fullInterfaceName = $interfaceName;
        $this->interfaceName = NameResolver::resolveOnlyInterfaceName($this->fullInterfaceName);
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'collection';
    }

    /**
     * {@inheritdoc}
     */
    public function addConstructor(Method $method)
    {
        $method->addBody('$this->' . $this->name . ' = new ArrayCollection();');
    }

    /**
     * {@inheritdoc}
     */
    public static function create(string $name, ?string $interfaceName = null): PropTypeInterface
    {
        return new self($name, $interfaceName);
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');

        $prop->setComment("\n@var Collection|$this->interfaceName[]\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public')
        ;

        $method->setReturnNullable(false);

        $method->setComment("\n@return Collection|$$this->interfaceName[]\n");

        $method->setReturnType(Collection::class);

        $method
            ->setBody('return $this->' . $this->name . ';');
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType $classType)
    {
        if ("s" !== substr($this->name, -1, 1)) {
            throw new \InvalidArgumentException(sprintf('"%s" must end of "s"', $this->name));
        }

        $this->uses[] = $this->fullInterfaceName;
        $this->uses[] = Collection::class;
        $this->uses[] = ArrayCollection::class;

        // has
        $singleName = substr($this->name, 0, strlen($this->name) - 1);
        $method = $classType
            ->addMethod('has' . ucfirst($singleName));
        $method
            ->setVisibility('public')
            ->setBody('return $this->' . $this->name . '->contains($' . $singleName . ');');

        $parameter = $method
            ->setReturnType('bool')
            ->addParameter($singleName);
        $method->setComment("\n @param " . $this->interfaceName . " $$singleName");
        $method->addComment("\n@return bool");
        $parameter->setTypeHint($this->fullInterfaceName);

        // add
        $method = $classType
            ->addMethod('add' . ucfirst($singleName));
        $method
            ->setVisibility('public')
            ->addBody('if (!$this->has' . ucfirst($singleName) . '($' . $singleName . ')) {')
            ->addBody("\t" . '$this->' . $this->name . '->add($' . $singleName . ');')
            ->addBody("\t" . '//$' . $singleName . '->setXXX($this);')
            ->addBody('}');
        $parameter = $method
            ->addParameter($singleName);
        $method
            ->setComment("\n @param " . $this->interfaceName . " $$singleName" . "\n");
        $parameter->setTypeHint($this->fullInterfaceName);

        // remove
        $method = $classType
            ->addMethod('remove' . ucfirst($singleName));
        $method
            ->setVisibility('public')
            ->addBody('if ($this->has' . ucfirst($singleName) . '($' . $singleName . ')) {')
            ->addBody("\t" . '$this->' . $this->name . '->removeElement($' . $singleName . ');')
            ->addBody("\t" . '//$' . $singleName . '->setXXX(null);')
            ->addBody('}');
        $parameter = $method
            ->addParameter($singleName);
        $method->setComment("\n @param " . $this->interfaceName . " $$singleName" . "\n");
        $parameter->setTypeHint($this->fullInterfaceName);
    }

    public function map(\SimpleXMLElement $XMLElement, CodeGeneratedStorageInterface $storage, array $options)
    {
        $onlyClassName = NameResolver::resolveOnlyClassName($options['class']);

        $field = $XMLElement->addChild('one-to-many');
        $field->addAttribute('field', $this->name);
        $field->addAttribute('target-entity', $this->fullInterfaceName);
        $field->addAttribute('mapped-by', $onlyClassName);
        $field->addAttribute('fetch', 'EXTRA_LAZY');
        $field->addAttribute('orphan-removal', 'true');
        $cascade = $field->addChild('cascade');
        $cascade->addChild('cascade-all');
    }

    /**
     * {@inheritdoc}
     */
    public function getUses(): array
    {
        return $this->uses;
    }
}
