<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 21.02.15
 * Time: 10:41
 */

namespace schorsch3000\botBlock;

trait BlockDie
{
    public function blockDie()
    {
        echo "oh dear, you behaved badly, please stay out";
        exit;
    }
}
