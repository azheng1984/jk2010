<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;
use PDO;

class DbClientTest extends Base {
    protected function setUp() {
        Config::set( 'hyperframework.app_root_path', dirname(__DIR__));
        DbImportCommand::execute(
            'Document',
            [
                [1, 'doc 1', 12.34],
                [2, 'doc 2', null],
            ],
            ['column_names' => ['id', 'name', 'decimal']]
        );
    }

    protected function tearDown() {
        DbClient::delete('Document', null);
        Config::clear();
    }

    public function testFindById() {
        $this->assertSame(DbClient::findById('Document', '3'), false);
        $this->assertSame(
            DbClient::findById('Document', '1'),
            ['id' => 1, 'name' => 'doc 1', 'decimal' => '12.34']
        );
        $this->assertSame(
            DbClient::findById('Document', '1', 'name'), ['name' => 'doc 1']
        );
        $this->assertSame(
            DbClient::findById('Document', '1', ['name']), ['name' => 'doc 1']
        );
    }

    public function testFindColumn() {
        $this->assertSame(DbClient::findColumn(
            "select name from Document where id = 1"
        ), 'doc 1');
    }

    public function testFindColumnByColumns() {
        $this->assertSame(
            DbClient::findColumnByColumns(
                'Document', ['name' => 'doc 1'], 'id'
            ),
            1
        );
    }

    public function testFindRow() {
        $this->assertSame(DbClient::findRow(
            "select name from Document where id = 1"
        ), ['name' => 'doc 1']);
    }

    public function testFindRowByColumns() {
        $this->assertSame(
            DbClient::findRowByColumns(
                'Document', ['name' => 'doc 1'], ['id']
            ),
            ['id' => 1]
        );
    }

    public function testFindAll() {
        $this->assertEquals(
            DbClient::findAll(
                "select name from Document where id = 1"
            ),
            [['name' => 'doc 1']]
        );
    }

    public function testFindAllByColumns() {
        $this->assertSame(
            DbClient::findAllByColumns(
                'Document', ['name' => 'doc 1'], ['id']
            ),
            [['id' => 1]]
        );
    }

    public function testCount() {
        $this->assertSame(
            DbClient::count('Document', 'id > 1'),
            1
        );
    }
}
