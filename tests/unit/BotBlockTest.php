<?php

use \schorsch3000\botblock\testHelper\BotBlockTestHelper;

class BotBlockTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $testfile;

    protected function _before()
    {
        require_once __DIR__ . '/src/BotBlockTestHelper.php';
        $this->testfile = tempnam(sys_get_temp_dir(), __CLASS__);
    }

    protected function resetConfig()
    {
        file_put_contents($this->testfile, '{"blockList": {},"traps": ["/trap"],"blockDuration": 60}');
    }

    protected function _after()
    {
        unlink($this->testfile);
    }

    // tests
    public function testNoFalsePositive()
    {

        $this->resetConfig();
        $_SERVER['REQUEST_URI'] = '/good';
        $_SERVER['REMOTE_ADDR'] = 'good_boy';

        new  \schorsch3000\botBlock\BotBlock($this->testfile);
        //if it would fail it would call exit

        verify(true)->true();


    }

    public function testBlockOnTrapPath()
    {
        $this->resetConfig();
        $_SERVER['REQUEST_URI'] = '/trap';
        $_SERVER['REMOTE_ADDR'] = 'bad_boy';
        $testee = new  BotBlockTestHelper($this->testfile);
        verify($testee->isBlocked)->true();

    }

    public function testBlockOnPreviousTrapPath()
    {
        $this->resetConfig();
        $_SERVER['REQUEST_URI'] = '/trap';
        $_SERVER['REMOTE_ADDR'] = 'bad_boy';
        new  BotBlockTestHelper($this->testfile);
        $_SERVER['REQUEST_URI'] = '/good';
        $_SERVER['REMOTE_ADDR'] = 'bad_boy';
        $testee = new  BotBlockTestHelper($this->testfile);
        verify($testee->isBlocked)->true();

    }

    public function testCreateDefaultConfig()
    {
        $cwd = getcwd();
        chdir(sys_get_temp_dir());
        $tmpdir = uniqid(__CLASS__);
        mkdir($tmpdir);
        chdir($tmpdir);
        $_SERVER['REQUEST_URI'] = '/good';
        $_SERVER['REMOTE_ADDR'] = 'good_boy';
        new BotBlockTestHelper();
        verify(is_file('botBlock.json'))->true();
        unlink('botBlock.json');
        chdir(sys_get_temp_dir());
        rmdir($tmpdir);
        chdir($cwd);
    }


    public function testCleanupOldEntriesInline()
    {
        $this->resetConfig();
        $config = json_decode(file_get_contents($this->testfile));
        $config->blockDuration = -1;
        file_put_contents($this->testfile, json_encode($config));
        $_SERVER['REQUEST_URI'] = '/trap';
        $_SERVER['REMOTE_ADDR'] = 'bad_boy';
        $testee = new  BotBlockTestHelper($this->testfile);
        $_SERVER['REQUEST_URI'] = '/good';
        $_SERVER['REMOTE_ADDR'] = 'bad_boy';
        $testee->run();
        verify($testee->isBlocked)->false();
    }

    public function testCleanupOldEntries()
    {
        $this->resetConfig();
        $config = json_decode(file_get_contents($this->testfile));
        $config->blockDuration = -1;
        $config->blockList->old_entry = 1;
        $config->blockList->new_entry = time() + 600;
        file_put_contents($this->testfile, json_encode($config));
        $_SERVER['REQUEST_URI'] = '/good';
        $_SERVER['REMOTE_ADDR'] = 'good_boy';
        $testee = new  BotBlockTestHelper($this->testfile);
        $testee->cleanUp();
        $config = json_decode(file_get_contents($this->testfile));

        verify(isset($config->blockList->old_entry))->false();
        verify(isset($config->blockList->new_entry))->true();

    }

}