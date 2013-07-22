<?php
namespace Hyperframework\Web;

class ActionProcessorTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $GLOBALS['TEST_CALLBACK_TRACE'] = array();
    }

    public function testExecuteRequestMethod() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->process();
        $this->assertSame(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
        $this->assertSame('TestAction->GET', $GLOBALS['TEST_CALLBACK_TRACE'][0]);
    }

    public function testRequestMethodNotAllowed() {
        $this->setExpectedException('Hyperframework\Web\MethodNotAllowedException');
        $_SERVER['REQUEST_METHOD'] = 'POST';
        try {
            $this->process();
        } catch (Hyperframework\Web\MethodNotAllowedException $exception) {
            $this->assertSame(0, count($GLOBALS['TEST_CALLBACK_TRACE']));
            throw $exception;
        }
    }

    private function process() {
        $processor = new Hyperframework\Web\ActionProcessor;
        $processor->run(
            array('class' => 'TestAction', 'method' => array('GET'))
        );
    }
}
