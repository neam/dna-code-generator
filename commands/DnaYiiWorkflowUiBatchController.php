<?php

namespace app\commands;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use schmunk42\giiant\crud\providers\CallbackProvider;
use schmunk42\giiant\crud\providers\DateTimeProvider;
use schmunk42\giiant\crud\providers\EditorProvider;
use schmunk42\giiant\crud\providers\RangeProvider;
use schmunk42\giiant\crud\providers\RelationProvider;
use schmunk42\giiant\crud\providers\SelectProvider;

class DnaYiiWorkflowUiBatchController extends DnaYii2DbFrontendBatchController
{

    public $crudGenerator = 'gii/yii-workflow-ui-crud';

    public $crudControllerNamespace = '';
    public $crudBaseControllerClass = 'Controller';
    //public $crudControllerPath = '@app/modules/ywuicrud/controllers';
    public $crudViewPath = '@app/modules/ywuicrud/views';
    public $modelNamespace = '';

    public function actionIndex()
    {
        $cruds = \DataModel::workflowUiItemModels();

        foreach ($cruds AS $modelClass => $table) {
            require(DNA_PROJECT_PATH . "/dna/models/$modelClass.php");
            $this->tableNameMap[$table] = $modelClass;
            $this->tables[] = $table;
        }

        return $this->modifiedActionIndex();
    }

}