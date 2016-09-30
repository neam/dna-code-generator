<?php

namespace app\commands;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use neam\yii_workflow_ui_giiant_generator\crud\Generator;

class DnaModelBatchController extends DnaBatchController
{

    public $modelGenerator = 'gii/dna-project-base-yii1-model';
    public $modelNamespace = 'app\\modules\\dnamodels\\models';

    public function actionIndex()
    {

        $crudModels = \ItemTypes::where('generate_phundament_crud');
        $qaModels = \ItemTypes::where('is_preparable');
        $qaStateModels = array();
        foreach (\ItemTypes::where('is_preparable') as $model => $table) {
            $qaStateModels[$model . "QaState"] = $table . "_qa_state";
        }

        // merge
        $models = array_merge($crudModels, $qaModels, $qaStateModels);

        foreach ($models AS $modelClass => $table) {
            $this->tableNameMap[$table] = $modelClass;
            $this->tables[] = $table;
        }

        $this->generateModels();

    }

    /**
     * This command echoes what you have entered as the message.
     *
     * @param string $message the message to be echoed.
     */
    public function generateModels()
    {
        echo "Running models batch...\n";

        //var_dump($this->tables);
        //die();

        $config = $this->getYiiConfiguration();
        $config['id'] = 'temp';

        // create models
        //foreach ($this->tables AS $table) {
        #var_dump($this->tableNameMap, $table);exit;
        $table = '*';
        $params = [
            'overwrite' => $this->overwrite,
            'interactive' => $this->interactive,
            'template' => 'default',
            'ns' => $this->modelNamespace,
            'db' => $this->modelDb,
            'tableName' => $table,
            'tables' => $this->tables,
            'tablePrefix' => $this->tablePrefix,
            'generateModelClass' => $this->extendedModels,
            'modelClass' => isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] :
                Inflector::camelize($table), // TODO: setting is not recognized in giiant
            'baseClass' => $this->modelBaseClass,
            "useMetadataClass" => true,
            'generateLabelsFromComments' => '1',
            'tableNameMap' => $this->tableNameMap
        ];
        var_dump($params);
        $route = $this->modelGenerator;

        $app = \Yii::$app;
        $temp = new \yii\console\Application($config);
        $temp->runAction(ltrim($route, '/'), $params);
        unset($temp);
        \Yii::$app = $app;
        //}
    }

}