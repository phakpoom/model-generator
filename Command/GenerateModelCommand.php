<?php

declare(strict_types = 1);

namespace Bonn\Generator\Command;

use Bonn\Generator\Model\ClassGeneratedStorage;
use Bonn\Generator\Model\Converter\ClassConverter;
use Bonn\Generator\Model\Converter\StringToPropTypeConverter;
use Bonn\Generator\Model\ModelGenerator;
use Bonn\Generator\Model\Type\CollectionPropType;
use Bonn\Generator\Model\Type\InterfacePropType;
use Bonn\Generator\Model\Type\PropTypeInterface;
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

    protected function configure(): void
    {
        $this
            ->setName('bonn:generate:model')
            ->addOption('from-string', '-s', InputOption::VALUE_REQUIRED)
            ->addOption('with-timestamp', null, InputOption::VALUE_NONE)
            ->addOption('with-code', null, InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classConverter = new ClassConverter();
        $supportedProps = null;
        try {
            $supportedProps = $this->getContainer()->getParameter('_bonn_generator_custom_prop_types');
        } catch (\Exception $e) {};

        $stringToPropConverter = new StringToPropTypeConverter($supportedProps);
        $generator = new ModelGenerator(new ClassGeneratedStorage(), $stringToPropConverter);

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
            ];

            $dataForReGenerate = [
                'generatorOption' => $generatorOption,
                'distDir' => $distDir,
                'className' => $className
            ];
        } else {
            $dataForReGenerate = \json_decode($input->getOption('from-string'), true);
            if ($input->getOption('with-timestamp')) {
                $dataForReGenerate['generatorOption'] = array_replace_recursive($dataForReGenerate['generatorOption'], [
                    'with_timestamp_able' => $input->getOption('with-timestamp')
                ]);
            }
        }

        $generator->generate($dataForReGenerate['generatorOption']);
        $generator->getStorage();

        /** @var ClassType $class */
        foreach ($generator->getStorage()->getClasses() as $name => $class) {
            $class->addComment('Remove me if u need');
            $class->addComment('Generated from: \''. \json_encode($dataForReGenerate) .'\'');
            $classOutputPath = $dataForReGenerate['distDir'] . $name . '.php';
            file_put_contents($classOutputPath, $classConverter->getClassAsString($class));
            $output->writeln('<info>' . $classOutputPath . ' has been created</info>');
        }

        /** @var ClassType $class */
        foreach ($generator->getStorage()->getInterfaces() as $name => $interface) {
            $interfaceOutputPath = $dataForReGenerate['distDir'] . $name . '.php';
            file_put_contents($interfaceOutputPath, $classConverter->getInterfaceAsString($interface));
            $output->writeln('<info>' . $interfaceOutputPath . ' has been created</info>');
        }
    }

    private function askForProperty(array $typesClass, InputInterface $input, OutputInterface $output)
    {
        $outputInfoString = '';
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the property name: ');
        $propertyName = $helper->ask($input, $output, $question);

        if (empty($propertyName)) {
            return true;
        }

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
        } else {
            $question = new Question('Enter default value (can skip)');
            $defaultValue = $helper->ask($input, $output, $question);
            if ($defaultValue) {
                $outputInfoString .= ':' . $defaultValue;
            }
        }

        $this->infos[] = $outputInfoString;
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
