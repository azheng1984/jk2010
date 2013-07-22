<?php
class ExceptionHandlerTest extends PHPUnit_Framework_TestCase {
    public function testReloadApplication() {
        $this->setExpectedException('PHPUnit_Framework_Error');
        $app = $this->getMock('Hyperframework\Web\Application', array('run'));
        $app->expects($this->once())->method('run')->with(
            $this->equalTo('/error/internal_server_error')
        );
        $exception = new TestException;
        $handler = new Hyperframework\Web\ExceptionHandler($app);
        try {
            $handler->handle($exception);
        } catch (PHPUnit_Framework_Error $error) {
            $this->assertSame($exception->__toString(), $error->getMessage());
            throw $error;
        }
    }
}
