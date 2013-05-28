<?php
namespace modules\mongodb;

use regenix\lang\CoreException;
use regenix\lang\String;
use regenix\modules\Module;
use regenix\mvc\AbstractQuery;
use regenix\mvc\AbstractService;
use regenix\mvc\AbstractActiveRecord;
use regenix\mvc\Annotations;

abstract class ActiveRecord extends AbstractActiveRecord {

    const type = __CLASS__;

    /**
     * @param $id
     * @param mixed $id
     * @return ActiveRecord|null
     */
    public static function findById($id){
        return static::getService()->findById($id);
    }

    /**
     * @param AbstractQuery $query
     * @param array $fields
     * @return mixed|DocumentCursor
     */
    public static function find(AbstractQuery $query = null, array $fields = array()){
        return static::getService()->findByFilter($query, $fields);
    }

    public static function findAndModify(Query $query, array $update, array $fields = array()){
        return static::getService()->findByFilterAndModify($query, $update, $fields);
    }

    /**
     * @return Service
     */
    public static function getService(){
        $class = get_called_class();
        return Service::get($class);
    }

    // handle, call on first load class
    public static function initialize(){
        parent::initialize();

        /** register indexed */
        /** @var $service Service */
        $service = static::getService();
        $service->registerIndexed();
    }

    /**
     * @param string $where
     * @return Query
     */
    public static function query($where = ''){
        $query = new Query(static::getService());
        if ($where)
            call_user_func_array(array($query, 'filter'), func_get_args());

        return $query;
    }
}


abstract class AtomicOperation {

    public $oper;
    public $value;

    public $needTyped = false;

    public function __construct($oper, $value = ''){
        $this->oper  = $oper;
        $this->value = $value;
    }

    public function getDefaultValue(){
        return null;
    }

    public function validateType($type){
        return true;
    }

    public function doTyped($type, $ref = null){
        // ...
    }
}

class AtomicInc extends AtomicOperation {

    public function __construct($value){
        parent::__construct('$inc', (int)$value);
    }

    public function getDefaultValue(){
        return $this->value;
    }

    public function validateType($type){
        return $type === 'int' || $type === 'integer' || $type === 'long';
    }
}

class AtomicRename extends AtomicOperation {

    public function __construct($value){
        parent::__construct('$rename', (string)$value);
    }
}

class AtomicPush extends AtomicOperation {

    public $needTyped = true;

    /**
     * @param $value array|mixed
     */
    public function __construct($value){
        if ( is_array($value) )
            parent::__construct('$pushAll', $value);
        else
            parent::__construct('$push', $value);
    }

    // TODO fix typed
    public function doTyped($type, $ref = null){

        $realType = 'mixed';
        if ( String::endsWith($type, '[]') ){
            $realType = substr($type, 0, -2);
        }

        if ( is_array($this->value) ){
            foreach($this->value as &$val){
                $val = Service::typed($val, $realType, $ref);
            }
            unset($val);
        } else {
            $this->value = Service::typed($this->value, $realType, $ref);
        }
    }
}


// ANNOTATIONS
{
    // @indexed .class
    Annotations::registerAnnotation('indexed', array(
        'fields' => array('$background' => 'boolean'),
        'multi' => true,
        'any' => true
    ), 'class');

    // @indexed .property
    // todo fix remove _arg
    Annotations::registerAnnotation('indexed', array(
        'fields' => array('_arg' => 'mixed', 'background' => 'boolean', 'sort' => 'integer')
    ), 'property');
}