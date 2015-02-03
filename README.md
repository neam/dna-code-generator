DNA Code Generator
====================

> Please note: This project is en early development stages

Code generator for generating various parts of a project using the [DNA project base](http://neamlabs.com/dna-project-base/).

Installation
-----------

You can install _Yii DNA Code Generator_ using [composer](https://getcomposer.org/download/)...

    mkdir -p yiiapps/code-generator
    composer global require "fxp/composer-asset-plugin:1.0.0-beta4"
    composer create-project --stability=dev neam/dna-code-generator yiiapps/code-generator

If you are starting from scratch, you will need to generate the dna project base and config:

    ./yii dna-project-base —projectPath=@project/dna/config/
    ./yii dna-project-base-config —gdocid=hfyaYTYTafhjkjhafkuqwf —dnaConfigPath=@project/dna/config/

When that is in place, run the code generator setup:
    
    yiiapps/code-generator/yii app/setup

You should then be able to start using the code generator.

## Generate database administration views (uses the default Giiant CRUD templates)

    ./yii dna-yii2-db-frontend-generator

Resources
---------

- [Project Source-Code](https://github.com/neam/dna-code-generator)
- [Website](http://neamlabs.com/dna-project-base/)
