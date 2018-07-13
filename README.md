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

### services.xml
```xml
<service id="bonn.generator.command.generate_model_command" class="Bonn\Generator\Command\GenerateModelCommand">
     <tag name="console.command" />
</service>
```

### usage
1. interactive input
`$ php ./bin/console bonn:generate:model`
2. from string input
`$ php ./bin/console bonn:generate:model -s "your string"`
**(string format please see output comment in your class after 1. generated)**

options: 
`--with-timestramp`, `--with-code`

### custom type
```yml
parameters:
    _bonn_generator_custom_prop_types:
        - AppBundle\CustomPropType # implement Bonn\Generator\Model\Type\PropTypeInterface
```

TODOs
- [ ] Testing
- [ ] Support Translationable Sylius
