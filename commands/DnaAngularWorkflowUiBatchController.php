<?php

namespace app\commands;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\base\Exception;
use neam\gii2_workflow_ui_generators\angular_crud\Generator;

class DnaAngularWorkflowUiBatchController extends DnaBatchController
{

    public $crudGenerator = 'gii/workflow-ui-angular-crud';

    public $crudBaseControllerClass = 'Controller';
    public $modelNamespace = '';
    public $crudControllerNamespace = '';
    //public $crudControllerPath = '@app/modules/wuingcrud/controllers';
    public $crudViewPath = '@app/modules/wuingcrud/views';

    public function actionIndex()
    {

        // Require a config directive about what bootstrap include we should include (this script is used to activate providers for code generation)
        $alias = getenv('CODE_GENERATOR_BOOTSTRAP_INCLUDE_ALIAS');
        if (empty($alias)) {
            throw new Exception("CODE_GENERATOR_BOOTSTRAP_INCLUDE_ALIAS not set");
        }

        $cruds = \ItemTypes::where('generate_angular_crud_module');

        foreach ($cruds AS $modelClass => $table) {
            $modelPath = DNA_PROJECT_PATH . "/dna/legacy-yii-models/base/Base$modelClass.php";
            if (!is_readable($modelPath)) {
                echo "No base class exists at $modelPath\n";
                continue;
            }
            require($modelPath);
        }
        foreach ($cruds AS $modelClass => $table) {
            $modelPath = DNA_PROJECT_PATH . "/dna/legacy-yii-models/metadata/Metadata$modelClass.php";
            if (!is_readable($modelPath)) {
                echo "No metadata class exists at $modelPath\n";
                continue;
            }
            require($modelPath);
        }
        foreach ($cruds AS $modelClass => $table) {
            $modelPath = DNA_PROJECT_PATH . "/dna/legacy-yii-models/$modelClass.php";
            if (!is_readable($modelPath)) {
                echo "No model exists at $modelPath\n";
                continue;
            }
            require($modelPath);
            $this->tableNameMap[$table] = $modelClass;
            $this->tables[] = $table;
        }

        $this->providers = Generator::getCoreProviders();

        $this->generateCrud();

        echo "\n\n ============ AngularJS include statements / markup ============ \n\n";
        foreach ($cruds AS $modelClass => $table) {
            $modelClassSingularId = Inflector::camel2id($modelClass);
            echo <<<EOJS
                load{$modelClass}Crud: /*@ngInject*/ (\$q, \$ocLazyLoad) => {
                    return \$q((resolve) => {
                        require.ensure([], () => {
                            let module = require('crud/$modelClassSingularId/components.js');
                            \$ocLazyLoad.load({name: module.default.name});
                            resolve(module);
                        }, "crud-$modelClassSingularId")
                    });
                },

EOJS;
        }
        foreach ($cruds AS $modelClass => $table) {
            $tag = 'crud-' . Inflector::camel2id($modelClass) . '-curate';
            echo "<$tag></$tag>" . "\n";
        }

    }

}