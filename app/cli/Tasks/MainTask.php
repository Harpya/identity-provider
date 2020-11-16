<?php

declare(strict_types=1);

namespace Harpya\CLI\Tasks;

use \Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction()
    {
        echo "\n Help ";
        echo "\n  ";
        echo "\n harpya <module> <command> <parms>";
        echo "\n Where <module> can be: ";
        echo "\n    app = applications";
        echo "\n\n";
    }
}
