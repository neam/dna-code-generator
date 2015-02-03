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

    public $modelNamespace = '';

    public function actionIndex()
    {
        $crudModels = \DataModel::crudModels();
        $qaStateModels = \DataModel::qaStateModels();
        $cruds = array_merge($crudModels, $qaStateModels);

        foreach ($cruds AS $model => $table) {
            require(DNA_PROJECT_PATH . "/dna/models/$model.php");
        }

        return parent::actionIndex();

    }

}