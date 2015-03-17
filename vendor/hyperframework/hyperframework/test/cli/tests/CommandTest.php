<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\Command;
use Hyperframework\Cli\Test\TestCase as Base;

class CommandTest extends Base {
    public function testQuit() {
        $app = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $app->expects($this->once())->method('quit');
        $command = new Command($app);
        $command->quit();
    }
}
