### composer.json
```json
"require-dev": {     
   "phakpoom/model-generator": "dev-master"
},
"repositories": [
   {
      "type": "vcs",
      "url": "https://github.com/phakpoom/model-generator.git"
   }
],
```

### In Symfony Extension
```php
    private function registerCommands(ContainerBuilder $container)
    {
        if (true !== $container->getParameter('kernel.debug')) {
            return;
        }
        
      
        $def = new Definition("Bonn\\Generator\\Command\\GenerateModelCommand");
        $def->addTag('console.command');
        $container->setDefinition('bonn.generator.command.generate_model_command', $def);
        
        
        $def = new Definition(Bonn\\Generator\\Command\\GenerateSyliusCommand);
        $def->addTag('console.command');
        $container->setDefinition('bonn.generator.command.generate_sylius_command', $def);
    }
```

### usage model generate
1. interactive input
`$ php ./bin/console bonn:generate:model`
2. from string input
`$ php ./bin/console bonn:generate:model -s "your string"`
**(string format please see output comment in your class after 1. generated)**

### usage sylius generate
1. interactive input
`$ php ./bin/console bonn:generate:sylius`

### custom type
```yml
parameters:
    _bonn_generator_custom_prop_types:
        - AppBundle\CustomPropType # implement Bonn\Generator\Model\Type\PropTypeInterface
```

TODOs
- [ ] Testing
- [x] Support Translationable Sylius
