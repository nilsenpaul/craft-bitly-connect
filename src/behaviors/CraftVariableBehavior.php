<?php
namespace nilsenpaul\bitlyconnect\behaviors;

use Craft;
use yii\base\Behavior;
use nilsenpaul\bitlyconnect\elements\Bitlink;
use nilsenpaul\bitlyconnect\elements\db\BitlinkQuery;

class CraftVariableBehavior extends Behavior
{
    public function bitlinks($criteria = null): BitlinkQuery
    {
        $query = Bitlink::find();
        if ($criteria) {
            Craft::configure($query, $criteria);
        }
        return $query;
    }
}
