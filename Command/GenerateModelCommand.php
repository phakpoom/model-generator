<?php

declare(strict_types = 1);

namespace Bonn\Generator\Command;

use Bonn\Generator\CodeManager;
use Bonn\Generator\CodeWriter;
use Bonn\Generator\DummyWriter;
use Bonn\Generator\Model\Converter\StringToPropTypeConverter;
use Bonn\Generator\Model\DoctrineMappingGenerator;
use Bonn\Generator\Model\ModelGenerator;
use Bonn\Generator\Model\Type\CollectionPropType;
use Bonn\Generator\Model\Type\InterfacePropType;
use Bonn\Generator\Model\Type\PropTypeInterface;
use Bonn\Generator\Model\Type\TranslationPropType;
use Bonn\Generator\Storage\CodeGeneratedStorage;
use Bonn\Generator\Transformer\ClassTransformer;
use Bonn\Generator\Transformer\InterfaceTransformer;
use Bonn\Generator\Transformer\Transformer;
use Nette\PhpGenerator\ClassType;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class GenerateModelCommand extends ContainerAwareCommand
{
    private $infos = [];
    private $props = [];

    protected function configure(): void
    {
        $this
            ->setName('bonn:generate:model')
            ->addOption('from-string', '-s', InputOption::VALUE_REQUIRED)
            ->addOption('with-timestamp', null, InputOption::VALUE_NONE)
            ->addOption('with-code', null, InputOption::VALUE_NONE)
            ->addOption('with-toggle', null, InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $supportedProps = null;
        try {
            $supportedProps = $this->getContainer()->getParameter('_bonn_generator_custom_prop_types');
        } catch (\Exception $e) {};

        $stringToPropConverter = new StringToPropTypeConverter($supportedProps);
        $generator = new ModelGenerator(new CodeGeneratedStorage(), $stringToPropConverter);
        $doctrineMappingGenerator = new DoctrineMappingGenerator(new CodeGeneratedStorage(), $stringToPropConverter);
        $manager = new CodeManager($generator->getStorage());
        $doctrineMappingManager = new CodeManager($doctrineMappingGenerator->getStorage());

        if (null === $input->getOption('from-string')) {
            $helper = $this->getHelper('question');
            $question = new Question('Please enter the class name: ');
            $question->setValidator($this->notEmptyValidate())->setMaxAttempts(5);
            $className = $helper->ask($input, $output, $question);

            $question = new Question('Please enter the name of the bundle: ');
            $question->setValidator($this->bundleNameValidate())->setMaxAttempts(5);
            $distDir = $helper->ask($input, $output, $question);

            while (true !== $this->askForProperty($stringToPropConverter->getSupportedType(), $input, $output)) {}

            $generatorOption = [
                'class' => str_replace('/', '\\', explode("/src/", $distDir)[1] . $className),
                'info' => implode('|', $this->infos),
                'with_timestamp_able' => $input->getOption('with-timestamp') ?: false,
                'with_code' => $input->getOption('with-code') ?: false,
                'with_toggle' => $input->getOption('with-toggle') ?: false,
            ];

            $dataForReGenerate = [
                'generatorOption' => $generatorOption,
                'distDir' => $distDir,
                'className' => $className
            ];
        } else {
            $dataForReGenerate = \json_decode($input->getOption('from-string'), true);
        }

        $output->writeln(\json_encode($dataForReGenerate));

        $generator->generate($dataForReGenerate['generatorOption']);
        $doctrineMappingGenerator->generate(array_merge($dataForReGenerate['generatorOption'], [
            'doctrine_resource_mapping_dir' => str_replace('/Model/', '/Resources/config/doctrine/model/', $dataForReGenerate['distDir'])
        ]));

        // Add string to class comment
        foreach ($manager->getStorage()->all() as $name => $code) {
            if (!$code instanceof ClassType) {
                continue;
            }

            $code->addComment('Remove me if u need');
            $code->addComment('Generated from: \''. \json_encode($dataForReGenerate) .'\'');
        }

        $manager->persist(['class_start_dir' => $this->getContainer()->getParameter('kernel.project_dir') . '/src/']);
        $manager->flush();

        $doctrineMappingManager->persist();
        $doctrineMappingManager->flush();
    }

    private function askForProperty(array $typesClass, InputInterface $input, OutputInterface $output)
    {
        $outputInfoString = '';
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the property name: ');
        $question->setValidator($this->propertyNameValidate())->setMaxAttempts(5);
        $propertyName = $helper->ask($input, $output, $question);

        if (empty($propertyName)) {
            return true;
        }

        $this->props[] = $propertyName;

        $outputInfoString .= $propertyName;

        $question = new ChoiceQuestion(
            "Please select your property:$propertyName type (defaults to string) ",
            array_map(function ($propTypeClass) {
                /** @var PropTypeInterface $propTypeClass */
                return $propTypeClass::getTypeName();
            }, $typesClass),
            0
        );

        $propertyType = $helper->ask($input, $output, $question);

        $outputInfoString .= ':' . $propertyType;

        if ($propertyType === CollectionPropType::getTypeName()) {
            $question = new Question('Please enter Interface of Collection: ');
            $question->setValidator($this->notEmptyValidate())->setMaxAttempts(5);
            $interfaceChild = $helper->ask($input, $output, $question);
            $outputInfoString .= ':' . $interfaceChild;
        } elseif ($propertyType === InterfacePropType::getTypeName()) {
            $question = new Question('Please enter Interface: ');
            $question->setValidator($this->notEmptyValidate())->setMaxAttempts(5);
            $interface = $helper->ask($input, $output, $question);
            $outputInfoString .= ':' . $interface;
        } elseif (!in_array($propertyType, [
            TranslationPropType::getTypeName()
        ])) {
            $question = new Question('Enter default value (enter for skip)');
            $defaultValue = $helper->ask($input, $output, $question);
            if ($defaultValue) {
                $outputInfoString .= ':' . $defaultValue;
            }
        }

        $this->infos[] = $outputInfoString;

        return false;
    }

    private function notEmptyValidate()
    {
        return function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException(
                    'Value cannot be empty'
                );
            }

            return $answer;
        };
    }

    private function propertyNameValidate()
    {
        return function ($answer) {
            if (empty($answer)) {
                return $answer;
            }

            if (in_array($answer, $this->props)) {
                throw new \RuntimeException(
                    "$answer is already added"
                );
            }

            return $answer;
        };
    }

    private function bundleNameValidate()
    {
        return function ($answer) {
            if (empty($answer) || 'Bundle' !== substr($answer, -6)) {
                throw new \RuntimeException(
                    'The name of the bundle should be suffixed with \'Bundle\''
                );
            }

            $bundleDir = $this->getBundleLocate($answer);

            return $bundleDir . 'Model/';
        };
    }

    private function getBundleLocate($bundleName)
    {
        return $this->getContainer()->get('kernel')->locateResource('@' . $bundleName);
    }
}
