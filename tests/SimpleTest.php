<?php
namespace modules\mongodb\tests;

use modules\mongodb\tests\models\Log;
use regenix\test\UnitTest;

class SimpleTest extends UnitTest {

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
}