<?php
namespace Hyperframework\Web;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
    private static $app;
    private $inexistentPath = '/inexistent_path';

    public static function setUpBeforeClass() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        self::$app = new Application;
    }

    protected function setUp() {
        $GLOBALS['TEST_CALLBACK_TRACE'] = array();
        $_SERVER['REQUEST_URI'] = $this->inexistentPath;
    }

    public function testPathWithParameter() {
        $_SERVER['REQUEST_URI'] = '/?key=value';
        self::$app->run();
        $this->verify();
    }

    public function testRewritePath() {
        self::$app->run('/');
        $this->verify();
    }

    public function testPathNotFound() {
        $this->setExpectedException(
            'Hyperframework\Web\NotFoundException',
            "Path '$this->inexistentPath' not found"
        );
        try {
            self::$app->run();
        } catch (NotFoundException $exception) {
            $this->assertSame(0, count($GLOBALS['TEST_CALLBACK_TRACE']));
            throw $exception;
        }
    }

    private function verify() {
        $this->assertSame(2, count($GLOBALS['TEST_CALLBACK_TRACE']));
        $this->assertSame(
            'TestAction->GET', $GLOBALS['TEST_CALLBACK_TRACE'][0]
        );
        $this->assertSame(
            'TestScreen->render', $GLOBALS['TEST_CALLBACK_TRACE'][1]
        );
    }
}
