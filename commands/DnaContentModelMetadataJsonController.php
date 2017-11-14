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

        $merged = (object) [
            'itemTypes' => [],
            'itemTypeAttributes' => [],
        ];

        $duration = round(microtime(true) - $start, 1);
        echo "* {$duration}s - Fetching itemTypes for all content models \n";
        $itemTypes = \ItemType::model()
            /*
            ->with(
                [
                    'itemTypeAttributes' => array(
                        // we don't want to include these
                        'select' => false,
                    ),
                    'changesets' => array(
                        // we don't want to include these
                        'select' => false,
                    ),
                    'contentModelMetadata',
                    'itemTypeCategory',
                ]
            )
            */
            ->findAllByAttributes(
                [
                    'content_model_metadata_id' => $configIds
                ]
            );
        $duration = round(microtime(true) - $start, 1);
        echo "* {$duration}s - Fetching itemTypeAttributes for all content models \n";
        $itemTypeAttributes = \ItemTypeAttribute::model()
            /*
            ->with(
                [
                    'attributeType',
                ]
            )
            */
            ->findAllByAttributes(
                [
                    'content_model_metadata_id' => $configIds
                ]
            );

        $itemTypesByContentModelMetadataId = [];
        foreach ($configIds as $configId) {
            $itemTypesByContentModelMetadataId[$configId] = [];
        }
        foreach ($itemTypes as $itemType) {
            $itemTypesByContentModelMetadataId[$itemType->content_model_metadata_id][] = $itemType;
        }
        $itemTypeAttributesByContentModelMetadataId = [];
        foreach ($configIds as $configId) {
            $itemTypeAttributesByContentModelMetadataId[$configId] = [];
        }
        foreach ($itemTypeAttributes as $itemTypeAttribute) {
            $itemTypeAttributesByContentModelMetadataId[$itemTypeAttribute->content_model_metadata_id][] = $itemTypeAttribute;
        }

        $duration = round(microtime(true) - $start, 1);
        echo "* {$duration}s - Fetched related items for all content models \n";

        // First content model metadata exports as usual
        $configId = array_shift($configIds);
        $firstCmm = \ContentModelMetadata::model()->findByPk($configId);
        if (empty($firstCmm)) {
            throw new ErrorException("There is no ContentModelMetadata record with id {$configId}");
        }
        $duration = round(microtime(true) - $start, 1);
        echo "* {$duration}s - Starting with {$firstCmm->getItemLabel()} \n";
        $merged->itemTypes = $itemTypesByContentModelMetadataId[$configId];
        $merged->itemTypeAttributes = $itemTypeAttributesByContentModelMetadataId[$configId];

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
                echo "* {$duration}s - Merging in {$cmm->getItemLabel()} \n";
                // Union item types and attributes, which only should be specified in one cmm at once
                $merged->itemTypes += $itemTypesByContentModelMetadataId[$configId];
                $merged->itemTypeAttributes += $itemTypeAttributesByContentModelMetadataId[$configId];
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