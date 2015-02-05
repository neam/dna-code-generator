<?php

namespace app\commands;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use neam\yii_workflow_ui_giiant_generator\crud\Generator;

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
        $cruds = \DataModel::workflowUiItemModels();

        foreach ($cruds AS $modelClass => $table) {
            require(DNA_PROJECT_PATH . "/dna/models/$modelClass.php");
            $this->tableNameMap[$table] = $modelClass;
            $this->tables[] = $table;
        }

        $this->providers = Generator::getCoreProviders();

        $this->generateCrud();
    }

}