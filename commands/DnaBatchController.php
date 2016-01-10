<?php

namespace app\commands;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class DnaBatchController extends \schmunk42\giiant\commands\BatchController
{

    public $crudGenerator = 'foo';
    public $providers = [];

    public $searchModelNamespace = 'app\\models';

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
        throw new Exception('Method should be overridden in child class');
        $this->tables = [];
        $this->providers = [];
        $this->generateModels();
        $this->generateCrud();
    }

    public function generateCrud()
    {
        echo "Running crud batch...\n";

        $config = $this->getYiiConfiguration();
        $config['id'] = 'temp';

        $exceptions = [];

        // create CRUDs
        foreach ($this->tables AS $table) {

            // Make sure output buffer is reset
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            try {

                $name = isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] : Inflector::camelize($table);
                $params = [
                    'overwrite' => $this->overwrite,
                    'interactive' => $this->interactive,
                    'template' => 'default',
                    'modelClass' => (!empty($this->modelNamespace) ? $this->modelNamespace . '\\' : '') . $name,
                    'searchModelClass' => $this->searchModelNamespace . '\\search\\' . $name . 'Search',
                    'controllerClass' => (!empty($this->modelNamespace) ? $this->crudControllerNamespace . '\\' : '') . $name . 'Controller',
                    'viewPath' => $this->crudViewPath,
                    'pathPrefix' => $this->crudPathPrefix,
                    'actionButtonClass' => 'yii\\grid\\ActionColumn',
                    'baseControllerClass' => $this->crudBaseControllerClass,
                    'providerList' => implode(',', $this->providers),
                ];
                echo "\n ===================== Generating CRUD for $table ======================== \n \n";
                var_dump($params);
                $route = $this->crudGenerator;
                $app = \Yii::$app;
                $temp = new \yii\console\Application($config);
                $temp->runAction(ltrim($route, '/'), $params);
                unset($temp);
                \Yii::$app = $app;

            } catch (\Exception $e) {
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                echo "Exception: " . $e->getMessage() . "\n";
                //throw $e;
                $exceptions[] = ["table" => $table, "e" => $e];
            }

        }

        // Print information about exceptions that has occurred
        if (!empty($exceptions)) {
            $summary = "";
            foreach ($exceptions as $exceptionInfo) {
                /** @var \Exception $exception */
                $exception = $exceptionInfo["e"];
                $summary .= "\n------\n{" . get_class($exception) . "} " . $exception->getMessage(
                    ) . " [" . $exception->getFile() . ", line " . $exception->getLine(
                    ) . "]\n\nTrace: \n\n" . $exception->getTraceAsString() . "\n\n";
            }
            throw new \Exception("Exceptions occurred during generation: \n$summary");
        }

    }

}