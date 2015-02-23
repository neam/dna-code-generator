<?php

namespace app\commands;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;

class DnaContentModelMetadataJsonController extends \yii\console\Controller
{

    public $configId;

    /**
     * @inheritdoc
     */
    public function options($id)
    {
        return array_merge(
            parent::options($id),
            [
                'configId',
            ]
        );
    }

    public function actionIndex()
    {
        require(DNA_PROJECT_PATH . "/dna/models/ContentModelMetadata.php");
        $cmm = \ContentModelMetadata::model()->findByPk($this->configId);
        if (empty($cmm)) {
            throw new ErrorException("There is no ContentModelMetadata record with id {$this->configId}");
        }
        echo Json::encode($cmm->export());
    }

}