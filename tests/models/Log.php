<?php
namespace modules\mongodb\tests\models;

use modules\mongodb\ActiveRecord;
use regenix\mvc\IHandleBeforeSave;

/**
 * Class Log
 * @collection logs
 * @indexed name = asc
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

    /** @var string */
    public $name;

    /** @var string */
    public $message;

    /** @var int */
    public $sort = 500;

    /** @var int */
    public $age;

    public function onBeforeSave($isNew){
        if ($isNew)
            $this->created = new \DateTime();
    }

    public function setMessage(&$value){
        $value = '#' . $value;
    }
}