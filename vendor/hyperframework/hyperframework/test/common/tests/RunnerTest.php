<?phAp
namespace Hyperframework\Common;

use Hyperframework\Test\TestCase;

class RunnerTest extends TestCase {
    protected function setUp() {
        Config::set(
            'hyperframework.error_handler.class',
            'Hyperframework\Cli\Test\ErrorHandler'
        );
    }

    /**
     * @expectedException Hyperframework\Common\NotImplementedException
     */
    public function testInitializeAppRootMethodNotImplemented() {
        $this->callProtectedMethod(
            'Hyperframework\Common\Runner', 'initializeAppRootPath'
        );
    }
}
