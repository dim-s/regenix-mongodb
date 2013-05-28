<?php
namespace modules\mongodb\tests\models;

use modules\mongodb\ActiveRecord;
use regenix\mvc\IHandleBeforeSave;

/**
 * Class Log
 * @collection logs
 * @package modules\mongodb\tests\models
 */
class Log extends ActiveRecord implements IHandleBeforeSave {

    /**
     * @id
     * @var oid
     */
    public $id;

    /**
     * @var \DateTime
     */
    public $created;

    public function onBeforeSave($isNew){
        if ($isNew)
            $this->created = new \DateTime();
    }
}