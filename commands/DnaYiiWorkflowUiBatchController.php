<?php

namespace app\commands;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use neam\yii_workflow_ui_giiant_generator\crud\Generator;
use yii\base\Exception;

class DnaYiiWorkflowUiBatchController extends DnaBatchController
{

    public $crudGenerator = 'gii/yii-workflow-ui-crud';

    public $crudBaseControllerClass = 'Controller';
    public $modelNamespace = '';
    public $crudControllerNamespace = '';
    //public $crudControllerPath = '@app/modules/ywuicrud/controllers';
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
            require(DNA_PROJECT_PATH . "/dna/models/$modelClass.php");
            $this->tableNameMap[$table] = $modelClass;
            $this->tables[] = $table;
        }

        $this->providers = Generator::getCoreProviders();

        $this->generateCrud();
    }

}