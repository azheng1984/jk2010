<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class DbClientTest extends Base {
    protected function setUp() {
        Config::set( 'hyperframework.app_root_path', dirname(__DIR__));
        DbImportCommand::execute(
            'Document',
            [
                [1, 'doc 1'],
                [2, 'doc 2']
            ],
            ['column_names' => ['id', 'name']]
        );
    }

    protected function tearDown() {
        DbClient::delete('Document', null);
        Config::clear();
    }

    public function testFindById() {
        $this->assertEquals(DbClient::findById('Document', '3'), null);
        $this->assertEquals(
            DbClient::findById('Document', '1'),
            ['id' => 1, 'name' => 'doc 1']
        );
        $this->assertEquals(
            DbClient::findById('Document', '1', 'name'), ['name' => 'doc 1']
        );
        $this->assertEquals(
            DbClient::findById('Document', '1', ['name']), ['name' => 'doc 1']
        );
        $this->assertEquals(
            DbClient::findById('Document', '1'), ['name' => 'doc 1']
        );
    }
}
