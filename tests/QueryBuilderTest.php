<?php
namespace modules\mongodb\tests;

use modules\mongodb\Query;
use modules\mongodb\Service;
use modules\mongodb\tests\models\Log;
use regenix\mvc\AbstractQuery;
use regenix\test\UnitTest;

class QueryBuilderTest extends MongodbTest {

    const type = __CLASS__;

    public function testMain(){
        $query = Log::query();
        $this->assertRequire($query);
        $this->assertType(AbstractQuery::type, $query);
        $this->assertArraySize(0, $query->getData());
    }

    public function testBuild(){
        $query = Log::query();
        $query->eq('name', 'value');

        $this->assertEqual(array('name' => 'value'), $query->getData());

        $query->gt('age', 999);
        $this->assertEqual(array('name'=>'value', 'age' => array('$gt' => new \MongoInt32(999))), $query->getData(), 'Test $gt, etc.');

        $query->clear();
        $this->assertArraySize(0, $query->getData(), 'Test clear data');

        $query->gte('name', 123);
        $this->assertEqual(array('name' => array('$gte' => 123)), $query->getData(), 'Test $gte');

        $query->clear();
        $query->lt('name', '321');
        $this->assertEqual(array('name' => array('$lt' => '321')), $query->getData(), 'Test $lt');

        $query->clear();
        $query->exists('name');
        $this->assertEqual(array('name' => array('$exists' => true)), $query->getData(), 'Test $exists');

        $query->clear();
        $query->pattern('name', '/any/i');
        $this->assertEqual(array('name' => new \MongoRegex('/any/i')), $query->getData(), 'Test $pattern');
    }

    public function testFilter(){
        $query = Log::query();

        $query->filter('name', 'value');
        $this->assertEqual(array('name' => 'value'), $query->getData());

        $query->filter('age >', 999);
        $this->assertEqual(array('name'=>'value', 'age' => array('$gt' => new \MongoInt32(999))), $query->getData(), 'Test filter $gt, etc.');

        $query->clear();
        $query->filter('name', 'value',  'age >', 999);
        $this->assertEqual(array('name'=>'value', 'age' => array('$gt' => new \MongoInt32(999))), $query->getData(), 'Test filter $gt, etc.');

        $operators = array('<' => '$lt', '<=' => '$lte', '>=' => '$gte', '!=' => '$ne', '<>' => '$ne');
        foreach($operators as $sym => $code){
            $query->clear();
            $query->filter('name ' . $sym, 'value');
            $this->assertEqual(array('name' => array($code => 'value')), $query->getData(), 'Test filter oper `' . $sym . '`');
        }
        $query->clear();
    }
}