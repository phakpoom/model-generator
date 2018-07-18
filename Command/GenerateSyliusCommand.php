<?php

declare(strict_types = 1);

namespace Bonn\Generator\Command;

use Bonn\Generator\CodeManager;
use Bonn\Generator\CodeWriter;
use Bonn\Generator\DummyWriter;
use Bonn\Generator\Storage\CodeGeneratedStorage;
use Bonn\Generator\Sylius\FactoryGenerator;
use Bonn\Generator\Sylius\FormGenerator;
use Bonn\Generator\Sylius\GridGenerator;
use Bonn\Generator\Sylius\RepositoryGenerator;
use Bonn\Generator\Sylius\RoutingGenerator;
use Bonn\Generator\Sylius\SyliusResourceGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateSyliusCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
            ->setName('bonn:generate:sylius')
            ->addArgument('class', InputArgument::REQUIRED)
            ->addArgument('resource_name', InputArgument::REQUIRED)
            ->addOption('with-repo', '-r', InputOption::VALUE_NONE)
            ->addOption('with-factory', '-fac', InputOption::VALUE_NONE)
            ->addOption('with-form', '-form', InputOption::VALUE_NONE)
            ->addOption('with-routing', '-routing', InputOption::VALUE_NONE)
            ->addOption('with-grid', '-g', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storage = new CodeGeneratedStorage();
        $manager = new CodeManager($storage);

        $codeDir = $this->getContainer()->getParameter('kernel.project_dir') . '/src/';

        $options = [
            'class' => $input->getArgument('class'),
            'resource_name' => $input->getArgument('resource_name'),
            'resource_dir' => $codeDir . str_replace('\\', '/', explode('Model', $input->getArgument('class'))[0]) . 'Resources/'
        ];

        if ($input->getOption('with-factory')) {
            (new FactoryGenerator($storage))->generate($options);
            $manager->persist(['class_start_dir' => $codeDir])->flush();
        }

        if ($input->getOption('with-form')) {
            (new FormGenerator($storage))->generate($options);
            $manager->persist(['class_start_dir' => $codeDir, 'with_interface' => false])->flush();
        }

        if ($input->getOption('with-repo')) {
            (new RepositoryGenerator($storage))->generate($options);
            $manager->persist(['class_start_dir' => $codeDir])->flush();
        }

        if ($input->getOption('with-routing')) {
            (new RoutingGenerator($storage))->generate(array_merge($options, [
                'with_grid' => $input->getOption('with-grid'),
            ]));
            $manager->persist()->flush();
        }

        if ($input->getOption('with-grid')) {
            (new GridGenerator($storage))->generate(array_merge($options, [
                'with_repo' => $input->getOption('with-repo'),
            ]));
            $manager->persist()->flush();
        }

        (new SyliusResourceGenerator($storage))->generate(array_merge($options, [
            'with_repo' => $input->getOption('with-repo'),
            'with_factory' => $input->getOption('with-factory'),
            'with_form' => $input->getOption('with-form')
        ]));
        $manager->persist()->flush();
    }
}
