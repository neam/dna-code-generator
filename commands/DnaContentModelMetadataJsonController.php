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
        require(DNA_PROJECT_PATH . "/dna/legacy-yii-models/ContentModelMetadata.php");

        // Allow comma-separated list of config ids to include in export
        if (strpos($this->configId, ",") !== false) {
            $configIds = explode(",", $this->configId);
        } else {
            $configIds = [$this->configId];
        }

        // First content model metadata exports as usual
        $configId = array_shift($configIds);
        $firstCmm = \ContentModelMetadata::model()->findByPk($configId);
        if (empty($firstCmm)) {
            throw new ErrorException("There is no ContentModelMetadata record with id {$configId}");
        }

        // The rest are superimposed on the first content model metadata to create a joint export of the correct format
        if (!empty($configIds)) {
            foreach ($configIds as $configId) {
                $cmm = \ContentModelMetadata::model()->findByPk($configId);
                if (empty($cmm)) {
                    throw new ErrorException("There is no ContentModelMetadata record with id {$configId}");
                }
                // Union item types and attributes, which only should be specified in one cmm at once
                $firstCmm->itemTypes = array_merge($firstCmm->itemTypes, $cmm->itemTypes);
                $firstCmm->itemTypeAttributes = array_merge($firstCmm->itemTypeAttributes, $cmm->itemTypeAttributes);
            }
        }

        echo Json::encode($firstCmm->export());
    }

}