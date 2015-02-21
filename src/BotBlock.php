<?php
namespace schorsch3000\botBlock;

class BotBlock
{
    use BlockDie;
    protected $config = [];
    protected $configPath;
    protected $blocker;

    public function __construct($configPath = false)
    {
        if (false === $configPath) {
            $configPath = 'botBlock.json';
        }

        $this->configPath = $configPath;
        if (is_null($this->blocker)) {
            $this->blocker = 'blockDie';
        }
        $this->run();
    }

    public function run()
    {
        $this->readConfig();
        if ($this->isInTrapList($_SERVER['REQUEST_URI'])) {
            $this->addToblockList($_SERVER['REMOTE_ADDR']);
            $this->block();
        } elseif ($this->isInblockList($_SERVER['REMOTE_ADDR'])) {
            $this->block();
        }
    }

    protected function block()
    {
        call_user_func([$this, $this->blocker]);
    }


    protected function addToblockList($ip)
    {
        $this->config['blockList'][$ip] = time() + $this->config['blockDuration'];
        $this->writeConfig();
    }

    protected function isInblockList($ip)
    {
        if (!isset($this->config['blockList'][$ip])) {
            return false;
        }
        if (time() < $this->config['blockList'][$ip]) {
            return true;
        }
        unset($this->config['blockList'][$ip]);
        $this->writeConfig();
        return false;
    }

    protected function isInTrapList($path)
    {
        return in_array($path, $this->config['traps']);
    }

    protected function readConfig()
    {
        if (!is_file($this->configPath)) {
            $this->createDefaultConfig();
            $this->writeConfig();
        }
        $this->config = json_decode(file_get_contents($this->configPath), true);
    }

    protected function writeConfig()
    {
        file_put_contents($this->configPath, json_encode($this->config), JSON_UNESCAPED_SLASHES);
    }

    protected function createDefaultConfig()
    {
        $this->config = ['blockList' => [], 'traps' => [], 'blockDuration' => 86400, 'blockType' => 'die'];
    }

    public function cleanUp()
    {
        $shouldWrite = false;
        foreach ($this->config['blockList'] as $ip => $expires) {
            if (time() < $expires) {
                continue;
            }
            unset($this->config['blockList'][$ip]);
            $shouldWrite = true;
        }
        if ($shouldWrite) {
            $this->writeConfig();
        }

    }
}