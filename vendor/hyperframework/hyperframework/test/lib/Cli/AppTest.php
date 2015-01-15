<?php
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * App test case.
 */
class AppTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var App
     */
    private $App;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated AppTest::setUp()
        $this->App = new App(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated AppTest::tearDown()
        $this->App = null;
        
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests App->__construct()
     */
    public function test__construct()
    {
        // TODO Auto-generated AppTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->App->__construct(/* parameters */);
    }

    /**
     * Tests App->run()
     */
    public function testRun()
    {
        // TODO Auto-generated AppTest->testRun()
        $this->markTestIncomplete("run test not implemented");
        
        $this->App->run(/* parameters */);
    }

    /**
     * Tests App->getArguments()
     */
    public function testGetArguments()
    {
        // TODO Auto-generated AppTest->testGetArguments()
        $this->markTestIncomplete("getArguments test not implemented");
        
        $this->App->getArguments(/* parameters */);
    }

    /**
     * Tests App->hasOption()
     */
    public function testHasOption()
    {
        // TODO Auto-generated AppTest->testHasOption()
        $this->markTestIncomplete("hasOption test not implemented");
        
        $this->App->hasOption(/* parameters */);
    }

    /**
     * Tests App->getOption()
     */
    public function testGetOption()
    {
        // TODO Auto-generated AppTest->testGetOption()
        $this->markTestIncomplete("getOption test not implemented");
        
        $this->App->getOption(/* parameters */);
    }

    /**
     * Tests App->getOptions()
     */
    public function testGetOptions()
    {
        // TODO Auto-generated AppTest->testGetOptions()
        $this->markTestIncomplete("getOptions test not implemented");
        
        $this->App->getOptions(/* parameters */);
    }

    /**
     * Tests App->getCommandConfig()
     */
    public function testGetCommandConfig()
    {
        // TODO Auto-generated AppTest->testGetCommandConfig()
        $this->markTestIncomplete("getCommandConfig test not implemented");
        
        $this->App->getCommandConfig(/* parameters */);
    }

    /**
     * Tests App->quit()
     */
    public function testQuit()
    {
        // TODO Auto-generated AppTest->testQuit()
        $this->markTestIncomplete("quit test not implemented");
        
        $this->App->quit(/* parameters */);
    }
}

