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

class DnaYii2DnaFrontendGeneratorController extends DnaYii2DbFrontendGeneratorController
{
    public $modelNamespace = '';

    public function actionIndex()
    {

        require(DNA_PROJECT_PATH . "/dna/config/AppBehaviorsConfigTrait.php");
        require(DNA_PROJECT_PATH . "/dna/config/DataModel.php");

        $crudModels = \DataModel::crudModels();
        $qaStateModels = \DataModel::qaStateModels();

        // merge
        $cruds = array_merge($crudModels, $qaStateModels);

        // init actions
        $actions = array();

        // generate hybrid CRUDs into application
        foreach ($cruds AS $model => $table) {
            require(DNA_PROJECT_PATH . "/dna/models/$model.php");
            $this->tables[] = $table;
        }

        if (false) {
            $this->tables = array( //"foo",
            );
        }

        var_dump($this->tables);
        //die();
        return $this->modifiedActionIndex();
    }

}