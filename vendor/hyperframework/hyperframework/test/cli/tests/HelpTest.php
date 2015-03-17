<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\TestCase as Base;

class HelpTest extends Base {
    public function testRender() {
        $this->expectOutputString(
            'Usage: test [-t] [-h|--help] [--version] <arg>' . PHP_EOL
        );
        $app = $this->getMockBuilder('Hyperframework\Cli\App')
            ->disableOriginalConstructor()
            ->getMock();
        $app->method('getCommandConfig')->willReturn(new CommandConfig);
        $app->method('isSubcommandEnabled')->willReturn(false);
        $help = new Help($app);
        $help->render();
    }
}
