<?php

namespace app\commands;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\base\Exception;
use neam\gii2_workflow_ui_generators\yii1_crud\Generator;

class DnaYiiWorkflowUiBatchController extends DnaBatchController
{

    public $crudGenerator = 'gii/workflow-ui-yii1-crud';

    public $crudBaseControllerClass = 'Controller';
    public $modelNamespace = '';
    public $crudControllerNamespace = '';
    public $crudControllerPath = '@app/modules/ywuicrud/controllers';
    public $crudViewPath = '@app/modules/ywuicrud/views';

    public function actionIndex()
    {

        // Require a config directive about what bootstrap include we should include (this script is used to activate providers for code generation)
        $alias = getenv('CODE_GENERATOR_BOOTSTRAP_INCLUDE_ALIAS');
        if (empty($alias)) {
            throw new Exception("CODE_GENERATOR_BOOTSTRAP_INCLUDE_ALIAS not set");
        }

        $cruds = \ItemTypes::where('generate_yii_workflow_ui_crud');

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

    }

}