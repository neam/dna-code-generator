<?php

namespace app\commands;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\base\Exception;
use neam\gii2_restful_api_generators\yii1_rest_crud\Generator;
use Yii;

class DnaRestApiBatchController extends DnaBatchController
{

    /**
     * @var array the item types to generate models for
     */
    public $modelItemTypes = [];

    public $crudGenerator = 'gii/yii1-rest-crud';

    public $modelGenerator = 'gii/yii1-rest-model';
    public $modelNamespace = '';

    public $crudBaseControllerClass = 'AppRestController';
    public $crudControllerNamespace = '';
    //public $crudControllerPath = '@app/modules/yiirestapi/controllers';
    public $crudViewPath = '@app/modules/yiirestapi/views';

    public function actionIndex()
    {

        $cruds = \ItemTypes::where('generate_yii_rest_api_crud');

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
        $this->modelItemTypes = [];
        foreach ($cruds AS $modelClass => $table) {
            $modelPath = DNA_PROJECT_PATH . "/dna/legacy-yii-models/$modelClass.php";
            if (!is_readable($modelPath)) {
                echo "No model exists at $modelPath\n";
                continue;
            }
            require($modelPath);
            // models
            $this->modelItemTypes[$table] = $modelClass;
        }

        $this->generateModels();

        $this->tableNameMap = [];
        $this->tables = [];
        foreach ($this->modelItemTypes AS $table => $modelClass) {
            // workaround since yii2 can't find files without namespaces
            require_once(Yii::getAlias('@app/modules/yiirestapi/models/base/') . 'BaseRestApi' . $modelClass . '.php');
            require_once(Yii::getAlias('@app/modules/yiirestapi/models/') . 'RestApi' . $modelClass . '.php');
            // crud
            $this->tableNameMap[$table] = $modelClass;
            $this->tables[] = $table;
        }


        $this->providers = Generator::getCoreProviders();
        $this->generateCrud();

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
            'ns' => 'app\\models', // to workaround irrelevant validation "Namespace cannot be blank"
            'db' => $this->modelDb,
            'modelItemTypes' => $this->modelItemTypes,
            'modelPath' => '@app/modules/yiirestapi/models',
            'tableName' => $table,
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

    public function generateCrud()
    {
        echo "Running crud batch...\n";

        $config = $this->getYiiConfiguration();
        $config['id'] = 'temp';

        // create CRUDs
        foreach ($this->tables AS $table) {
            $name = isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] : Inflector::camelize($table);
            $params = [
                'overwrite' => $this->overwrite,
                'interactive' => $this->interactive,
                'template' => 'default',
                'modelClass' => (!empty($this->modelNamespace) ? $this->modelNamespace . '\\' : '') . "RestApi" . $name,
                'searchModelClass' => $this->searchModelNamespace . '\\search\\' . "RestApi" . $name . 'Search',
                'controllerClass' => (!empty($this->modelNamespace) ? $this->crudControllerNamespace . '\\' : '') . $name . 'Controller',
                'viewPath' => $this->crudViewPath,
                'pathPrefix' => $this->crudPathPrefix,
                'actionButtonClass' => 'yii\\grid\\ActionColumn',
                'baseControllerClass' => $this->crudBaseControllerClass,
                'providerList' => implode(',', $this->providers),
            ];
            var_dump($params);
            $route = $this->crudGenerator;;
            $app = \Yii::$app;
            $temp = new \yii\console\Application($config);
            $temp->runAction(ltrim($route, '/'), $params);
            unset($temp);
            \Yii::$app = $app;
        }
    }

}