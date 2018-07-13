<?php

namespace Bonn\Generator\Model\Converter;

use Bonn\Generator\Model\Type\ArrayPropType;
use Bonn\Generator\Model\Type\BooleanPropType;
use Bonn\Generator\Model\Type\CollectionPropType;
use Bonn\Generator\Model\Type\DatetimePropType;
use Bonn\Generator\Model\Type\FloatPropType;
use Bonn\Generator\Model\Type\InterfacePropType;
use Bonn\Generator\Model\Type\IntPropType;
use Bonn\Generator\Model\Type\PropTypeInterface;
use Bonn\Generator\Model\Type\StringPropType;
use Bonn\Generator\Model\Type\TranslationPropType;

final class StringToPropTypeConverter implements StringToPropTypeConverterInterface
{
    /**
     * @var PropTypeInterface[]|array
     */
    private $types = [
        BooleanPropType::class,
        StringPropType::class,
        IntPropType::class,
        DatetimePropType::class,
        FloatPropType::class,
        StringPropType::class,
        InterfacePropType::class,
        TranslationPropType::class,
        ArrayPropType::class,
        CollectionPropType::class
    ];

    public function __construct(?array $types = null)
    {
        if (null !== $types) {
            $this->types = array_unique(array_merge($this->types, $types));
        }
    }

    /**
     * @return array
     */
    public function getSupportedType() :array
    {
        return $this->types;
    }

    /**
     * @param string $infosString
     * @return array|PropTypeInterface[]
     */
    public function convertMultiple(string $infosString): array
    {
        return array_map(function($info) {
            return $this->convert($info);
        }, explode('|', $infosString));
    }

    /**
     * @param string $infoString
     *
     * @return PropTypeInterface
     * @throws \InvalidArgumentException
     */
    public function convert(string $infoString): PropTypeInterface
    {
        $p = explode(':', $infoString);
        /** @var PropTypeInterface $typeClass */
        foreach ($this->getSupportedType() as $typeClass) {
            if ($typeClass::getTypeName() === $p[1]) {
                return $typeClass::create($p[0], $p[2] ?? null);
            }
        }

        throw new \InvalidArgumentException('Unsupported propType "' . $p[1] . '"');
    }
}
