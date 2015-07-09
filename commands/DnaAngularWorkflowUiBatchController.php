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
            $modelPath = DNA_PROJECT_PATH . "/dna/models/$modelClass.php";
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

         // Generate angularjstmp overview stuffs
        // states?
        // app-includes...
        foreach ($cruds AS $modelClass => $table) {
            echo "'crud-" . Inflector::camel2id($modelClass) . "-services',\n";
            echo "'crud-" . Inflector::camel2id($modelClass) . "-controllers',\n";
        }
        foreach ($cruds AS $modelClass => $table) {
            echo '<script src="crud/' . Inflector::camel2id($modelClass) . '/services.js"></script>' . "\n";
            echo '<script src="crud/' . Inflector::camel2id($modelClass) . '/controllers.js"></script>' . "\n";
        }
        foreach ($cruds AS $modelClass => $table) {
            echo '<div ng-controller="curate' . Inflector::pluralize($modelClass) . 'Controller" ng-include="\'crud/' . Inflector::camel2id($modelClass) . '/curate.html\'"></div>' . "\n";
        }



    }

}