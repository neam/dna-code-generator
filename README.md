DNA Code Generator
====================

> Please note: This project is in early development stages

Code generator for generating various parts of a web project using the [DNA project base](http://neamlabs.com/dna-project-base/).

Featured Generators
------------------

All of the below are generated into the project's 12-factor app and deployed side by side with each other at different nginx locations, sharing project codebase.

### Content Model

* Yii 1 models based on your database schema
* Yii 1 behavior/traits/rules/relations configuration, as well as labels, hints etc based on the metadata defined in a Google Spreadsheet or your account at [http://codegeneration.io]()
* Yii 2 models based on your database schema

### User Interfaces

* Yii 1 workflow/wizard-based CMS UI for producing rich translatable content based on your content model metadata
* Yii 2 CRUD for all database tables (thanks to [Phundament](http://phundament.com/))

### Javascript-based Rich Web Applications

* (TODO) RESTful Content Delivery and Management API
* (TODO) AngularJS CRUD Modules for content item types

Installation
-----------

You can install _DNA Code Generator_ using [composer](https://getcomposer.org/download/)...

    mkdir -p tools/code-generator
    composer global require "fxp/composer-asset-plugin:1.0.0-beta4"
    composer create-project --stability=dev neam/dna-code-generator tools/code-generator

If you are starting from scratch, you will need to generate the dna project base and config (TODO):

    ./yii dna-project-base --projectPath=@project/dna/config/
    ./yii dna-project-base-config  --dnaConfigPath=@project/dna/config/

When that is in place, run the code generator setup:
    
    tools/code-generator/yii app/setup

You should then be able to start using the code generator.

Usage
-----

### Generating Content Model Metadata

This is the metadata about item types, attributes, their labels hints, workflow/wizard steps, if they are translatable etc.

Requires an account at [http://codegeneration.io]() for visually managing the content model metadata. In the example below, Content Model Metadata with id 1 is used as an example. Update the links with the id of the content model you are using in your project.

It is possible to maintain the metadata in a Google spreadsheet and copy paste back and forth between Codegeneration.io and Google Spreadsheets in order to keep them in sync. This is useful when collaboratively defining/enriching the metadata.

The metadata is used when generating or rendering user interfaces and can be accessed in two ways:
   1. From the PHP code it to use the public methods found in model methods and the static methods of the ItemTypes helper class
   2. By parsing dna/content-model-metadata.json

The behaviors/traits/rules/relations configuration for specific extensions commonly used dna-based projects is generated into each model's metadata trait. This makes it easy to see what extensions a specific model is actually using as well as to customize the configuration post code-generation.

Main content model code metadata generation workflow goes as follows:

1. Discuss content model and collaborate by editing in the google spreadsheet with the metadata
2. Perform db schema changes (adding migrations as necessary)
3. Update list of item types [http://codegeneration.io/contentModelMetadata/edit/1?step=item-types](here) and save changes
4. Copy relevant parts from the google spreadsheet to [http://codegeneration.io/contentModelMetadata/edit/1?step=item-type-attributes](here) and save changes
5. Optionally tweak metadata for item type attributes and item types using the producer pages (don't forget to copy back to google spreadsheet for changes to be reflected there as well)
6. Generate `dna/content-model-metadata.json`
7. Generate item types helper class and model traits
8. Generate models

From here, keep generating migrations, models and/or UI based on the current content model.

#### Generating `dna/content-model-metadata.json`

    tools/code-generator/yii dna-content-model-metadata-json --configId=1 | jq '.' > dna/content-model-metadata.json

#### Generating item types helper class and model traits

Requires an up to date `dna/content-model-metadata.json`.

Updating the pristine generated helper class:

    tools/code-generator/yii gii/content-model-metadata-helper --template=yii --jsonPathAlias=@project/dna/content-model-metadata.json --overwrite=1 --interactive=0

Move generated helper class to dna:

    mv tools/code-generator/helpers/*.php dna/config/

Updating the pristine generated model traits:

    tools/code-generator/yii gii/content-model-metadata-model-trait --template=yii --jsonPathAlias=@project/dna/content-model-metadata.json --itemType='*' --interactive=0 --overwrite=1

Move generated model traits to dna:

    mv tools/code-generator/models/metadata/traits/*Trait.php dna/models/metadata/traits/

Before committing, make sure to autoformat all code in dna/models directory.

#### Generating models

Requires an up to date generated item types helper class. Note: Uses giic installed and configured in the dna folder. Sample configuration: [https://gist.github.com/motin/2785bdfec2c9e1b3012c]()

    mkdir -p dna/code-generation/giic/
    git clone https://gist.github.com/motin/2785bdfec2c9e1b3012c dna/code-generation/giic/models

Updating the pristine generated models and copying base and metadata models to dna:

    php dna/vendor/schmunk42/giic/giic.php giic generate dna.code-generation.giic.models
    cp models/base/Base*.php ../models/base/
    cp models/metadata/Metadata*.php ../models/metadata/

If new tables have been added, the generated non-base model needs to be manually copied since only base models are copied automatically.

Before committing, make sure to autoformat all code in dna/models directory.

### Generating UI

#### Generating workflow ui controllers and views

Requires up to date content model metadata helper class and model traits.

Updating the pristine generated files:

    export CODE_GENERATOR_BOOTSTRAP_INCLUDE_ALIAS=@project/ui/yii-dna-cms/app/config/code-generation/provider-bootstrap.php
    tools/code-generator/yii dna-yii-workflow-ui-batch

Move generated controllers to internal yii frontend:

    mv tools/code-generator/modules/ywuicrud/controllers/* ui/yii-dna-cms/app/controllers/

Move generated views to internal yii frontend:

    cp -r tools/code-generator/modules/ywuicrud/views/* ui/yii-dna-cms/app/views/
    rm -r tools/code-generator/modules/ywuicrud/views/*

Now use git (SourceTree recommended) to stage the relevant generated changes and discard the changes that overwrote customly crafted parts that is not generated.

Updating code-generation logic is done by adding/tweaking/enhancing providers and configure what providers is used where by modifying `ui/yii-dna-cms/app/config/code-generation/provider-bootstrap.php`.

#### Generating database administration views (uses the default Giiant CRUD templates)

Updating the pristine generated files:

    export CODE_GENERATOR_BOOTSTRAP_INCLUDE_ALIAS=@project/ui/yii2-phundament/config/code-generation/provider-bootstrap.php
    tools/code-generator/yii dna-yii2-db-frontend-batch

Move generated controllers to internal db frontend:

    mv tools/code-generator/modules/crud/controllers/* ui/yii2-phundament/modules/crud/controllers/

Move generated yii2 models to internal db frontend:

    mv tools/code-generator/models/*.php ui/yii2-phundament/models/

Move generated views to internal db frontend:

    cp -r tools/code-generator/modules/crud/views/* ui/yii-dna-cms/app/views/
    rm -r tools/code-generator/modules/crud/views/*

Now use git (SourceTree recommended) to stage the relevant generated changes and discard the changes that overwrote customly crafted parts that is not generated.

Updating code-generation logic is done by adding/tweaking/enhancing providers and configure what providers is used where by modifying `ui/yii2-phundament/config/code-generation/provider-bootstrap.php`.

Resources
---------

- [Project Source-Code](https://github.com/neam/dna-code-generator)
- [Website](http://neamlabs.com/dna-project-base/)
