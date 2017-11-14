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

        $start = microtime(true);

        // Allow comma-separated list of config ids to include in export
        if (strpos($this->configId, ",") !== false) {
            $configIds = explode(",", $this->configId);
        } else {
            $configIds = [$this->configId];
        }

        $fasterArrayMerge = function (&$array1, $array2) {
            foreach ($array2 as $i) {
                $array1[] = $i;
            }
        };

        $merged = (object) [
            'itemTypes' => [],
            'itemTypeAttributes' => [],
        ];

        // First content model metadata exports as usual
        $configId = array_shift($configIds);
        $firstCmm = \ContentModelMetadata::model()->findByPk($configId);
        if (empty($firstCmm)) {
            throw new ErrorException("There is no ContentModelMetadata record with id {$configId}");
        }
        $duration = round(microtime(true) - $start, 1);
        echo "* {$duration}s - Fetching related items for {$firstCmm->getItemLabel()} \n";
        $merged->itemTypes = $firstCmm->itemTypes;
        $merged->itemTypeAttributes = $firstCmm->itemTypeAttributes;
        echo "* {$duration}s - Starting with {$firstCmm->getItemLabel()} \n";

        // The rest are superimposed on the first content model metadata to create a joint export of the correct format
        if (!empty($configIds)) {
            foreach ($configIds as $configId) {
                $duration = round(microtime(true) - $start, 1);
                echo "* {$duration}s - Fetching config with id {$configId} \n";
                $cmm = \ContentModelMetadata::model()->findByPk($configId);
                if (empty($cmm)) {
                    throw new ErrorException("There is no ContentModelMetadata record with id {$configId}");
                }
                $duration = round(microtime(true) - $start, 1);
                echo "* {$duration}s - Fetching related items for {$cmm->getItemLabel()} \n";
                $itemTypes = $cmm->itemTypes;
                $itemTypeAttributes = $cmm->itemTypeAttributes;
                echo "* {$duration}s - Merging in {$cmm->getItemLabel()} \n";
                // Union item types and attributes, which only should be specified in one cmm at once
                $fasterArrayMerge($merged->itemTypes, $itemTypes);
                $fasterArrayMerge($merged->itemTypeAttributes, $itemTypeAttributes);
            }
        }

        $duration = round(microtime(true) - $start, 1);
        echo "* {$duration}s - Exporting merged metadata...\n";

        $export = \ContentModelMetadata::exportStatic($merged->itemTypes, $merged->itemTypeAttributes);

        echo "* {$duration}s - Encoding JSON...\n";
        $json = Json::encode($export);
        file_put_contents('/app/out.json', $json);

        echo "* {$duration}s - Done. Contents (len " . strlen($json) . ") saved in out.json\n";
    }

}