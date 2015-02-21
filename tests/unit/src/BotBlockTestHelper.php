<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 21.02.15
 * Time: 09:01
 */

namespace schorsch3000\botblock\testHelper;


use schorsch3000\botBlock\BlockTest;
use schorsch3000\botBlock\BotBlock;

class BotBlockTestHelper extends BotBlock{
    use BlockTest;
    public $isBlocked = false;

    public function __construct($configFile=false){
        $this->blocker='blockTest';
        $this->isBlocked=false;
        parent::__construct($configFile);


    }
    public function run(){
        $this->isBlocked=false;
        parent::run();
    }

}