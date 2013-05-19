<?php
namespace modules\mongodb\tests;

use framework\test\UnitTest;

class SimpleTest extends UnitTest {

    public function main(){
        $this->assert(2 + 2 == 4, 'Test module testing');
    }
}