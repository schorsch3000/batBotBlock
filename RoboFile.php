<?php

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    public function watch()
    {
        $this->taskWatch()
            ->monitor('composer.json', function () {
                $this->clear();
                $this->taskComposerUpdate()->run();
                exit;
                    })
            ->monitor('src',function(){
                $this->clear();
                $this->taskCodecept()->run();
                exit;
            })
            ->monitor('tests/unit/',function(){
                $this->clear();
                $this->taskCodecept()->run();
                exit;
            })
            ->run();
    }
    protected function clear(){
        echo str_repeat("\n",100);
    }
}