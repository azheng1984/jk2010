<?php
namespace Hyperframework\Db;

use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Db\Test\TestCase as Base;

class DbClientEngineTest extends Base {
    private $engine;

    protected function setUp() {
        parent::setUp();
        DbImportCommand::execute(
            'Document',
            [
                [1, 'doc 1', 12.34], [2, 'doc 2', null],
            ],
            ['column_names' => ['id', 'name', 'decimal']]
        );
        $this->engine = new DbClientEngine;
    }

    protected function tearDown() {
        DbClient::delete('Document', null);
        parent::tearDown();
    }

    public function testFindColumn() {
    }

    public function testFindRowById() {
        $this->assertSame(
            ['id' => 1, 'name' => 'doc 1', 'decimal' => '12.34'],
            $this->engine->findRowById('Document', '1')
        );
        $this->assertSame(
            ['name' => 'doc 1'],
            $this->engine->findRowById('Document', '1', ['name'])
        );
    }
}
