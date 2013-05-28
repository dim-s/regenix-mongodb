<?php
namespace modules\mongodb\tests;

use modules\mongodb\tests\models\Log;
use regenix\lang\ClassScanner;
use regenix\test\UnitTest;

class SimpleTest extends MongodbTest {

    const type = __CLASS__;

    public function __construct(){
        $this->requiredOk(QueryBuilderTest::type);
    }

    public function simple(){
        $log = new Log();
        $log->save();
        $id = $log->getId();

        $this->assertRequire($id, 'Test get id after save');
        $this->assertType('\\MongoId', $log->getId(), 'Test id type');
        $this->assertEqual(Log::findById($id), $log, 'Test find by id');

        $log->delete();
        $this->assertNotRequire($log->getId(), 'Test removed id document');
        $this->assertNull(Log::findById($id), 'Test find by id removed');
    }

    public function testComplexSave(){
        $log = new Log();
        $log->message = 'test message';
        $this->assertEqual('#test message', $log->message, 'Test setter method');
        $this->assertEqual($log, $log->save(), 'Test save');

        Log::deleteAll();
        $this->assertEqual(0, Log::filter()->count());
    }

    public function testFilter(){
        Log::deleteAll();

        /** @var $logs Log[] */
        $logs = array();
        foreach(range(1, 10) as $i){
            $log = new Log();
            $log->sort = $i;
            $log->message = 'msg #' . $i;
            $log->save();
            $logs[] = $log;
        }

        $result = Log::filter('sort >=', 1, 'sort <=', 10);
        $this->assertRequire($result);
        $this->assertEqual(10, $result->count());
    }

    public function testDelete(){
        Log::deleteAll('sort', 2);
        $this->assertEqual(9, Log::filter()->count());

        Log::deleteAll('sort >', 4, 'sort <', 10);
        $this->assertEqual(4, Log::filter()->count());

        Log::deleteAll();
        $this->assertArraySize(0, Log::filter()->asArray());
    }
}