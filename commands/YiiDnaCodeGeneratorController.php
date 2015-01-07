<?php

namespace console\controllers;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use schmunk42\giiant\crud\providers\CallbackProvider;
use schmunk42\giiant\crud\providers\DateTimeProvider;
use schmunk42\giiant\crud\providers\EditorProvider;
use schmunk42\giiant\crud\providers\RangeProvider;
use schmunk42\giiant\crud\providers\RelationProvider;
use schmunk42\giiant\crud\providers\SelectProvider;

class YiiDnaCodeGeneratorController extends \schmunk42\giiant\commands\BatchController
{

    //public $dataModelClassPath;

    public $interactive = "0";
    public $overwrite = "1";
    public $extendedModels = "1";

    /**
     * @inheritdoc
     */
    public function options($id)
    {
        return array_merge(
            parent::options($id),
            [
                'dataModelClassPath',
                'extendedModels',
                'overwrite',
            ]
        );
    }

    public function actionIndex()
    {

        require(dirname(__FILE__) . "/../../../../dna/components/AppBehaviorsConfigTrait.php");
        require(dirname(__FILE__) . "/../../../../dna/components/DataModel.php");

        $crudModels = \DataModel::crudModels();
        $qaModels = \DataModel::qaModels();
        $qaStateModels = \DataModel::qaStateModels();
        $internalModels = \DataModel::internalModels();

        // merge
        $cruds = array_merge($crudModels, $qaStateModels);

        // init actions
        $actions = array();
        //var_dump(__LINE__, $this->interactive);die();
        // generate hybrid CRUDs into application
        foreach ($cruds AS $model => $table) {
            $this->tables[] = $table;
            /*
            $action = array(
                "codeModel" => "FullCrudCode",
                "generator" => 'vendor.phundament.gii-template-collection.fullCrud.FullCrudGenerator',
                "templates" => array(
                    'hybrid' => dirname(__FILE__) . '/../../../vendor/phundament/gii-template-collection/fullCrud/templates/hybrid',
                ),
                "model" => array(
                    "model" => "application.models." . $model,
                    "controller" => lcfirst($model),
                    "template" => "hybrid",
                    "internalModels" => array_keys($internalModels),
                )
            );

            if (in_array($model, array(
                "ExamQuestion",
                "ExamQuestionAlternative",
                "HtmlChunk",
            ))
            ) {
                $action["model"]["textEditor"] = "html5Editor";
            }

            $actions[] = $action;
        }
            */
            /*
        return array(
            "actions" => $actions
        );
            */

        }

        if (false) {
            $this->tables = array(
                //"account",
                "profile",
                "social_link",
                "contribution",
                "composition_type",
                "nav_tree_to_use_option",
                "route",
                "route_type",
                //"p3_media",
            );
        }

        var_dump($this->tables);
        //die();
        return $this->modifiedActionIndex();
    }

    /**
     * This command echoes what you have entered as the message.
     *
     * @param string $message the message to be echoed.
     */
    public function modifiedActionIndex()
    {
        echo "Running batch...\n";

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
            'tablePrefix' => $this->tablePrefix,
            'generateModelClass' => $this->extendedModels,
            'modelClass' => isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] :
                    Inflector::camelize($table), // TODO: setting is not recognized in giiant
            'baseClass' => $this->modelBaseClass,
            'generateLabelsFromComments' => '1',
            'tableNameMap' => $this->tableNameMap
        ];
        //var_dump($params);
        $route = 'gii/giiant-model';

        $app = \Yii::$app;
        $temp = new \yii\console\Application($config);
        $temp->runAction(ltrim($route, '/'), $params);
        unset($temp);
        \Yii::$app = $app;
        //}

        // create CRUDs
        $providers = [
            CallbackProvider::className(),
            EditorProvider::className(),
            DateTimeProvider::className(),
            //RangeProvider::className(),
            //SelectProvider::className(),
            RelationProvider::className()
        ];
        foreach ($this->tables AS $table) {
            $name = isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] : Inflector::camelize($table);
            $params = [
                'overwrite' => $this->overwrite,
                'interactive' => $this->interactive,
                'template' => 'default',
                'modelClass' => $this->modelNamespace . '\\' . $name,
                'searchModelClass' => $this->modelNamespace . '\\search\\' . $name . 'Search',
                'controllerClass' => $this->crudControllerNamespace . '\\' . $name . 'Controller',
                'viewPath' => $this->crudViewPath,
                'pathPrefix' => $this->crudPathPrefix,
                'actionButtonClass' => 'yii\\grid\\ActionColumn',
                'baseControllerClass' => $this->crudBaseControllerClass,
                'providerList' => implode(',', $providers),
            ];
            //var_dump($params, $this->generate);
            $route = 'gii/giiant-crud';
            $app = \Yii::$app;
            $temp = new \yii\console\Application($config);
            $temp->runAction(ltrim($route, '/'), $params);
            unset($temp);
            \Yii::$app = $app;
        }
    }

    protected function getYiiConfiguration()
    {
        $config = \yii\helpers\ArrayHelper::merge(
            require(\Yii::getAlias('@app') . '/../common/config/main.php'),
            require(\Yii::getAlias('@app') . '/../common/config/main-local.php'),
            require(\Yii::getAlias('@app') . '/../console/config/main.php'),
            require(\Yii::getAlias('@app') . '/../console/config/main-local.php')
        );
        return $config;
    }
}