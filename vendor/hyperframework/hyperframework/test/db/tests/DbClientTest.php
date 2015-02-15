<?php
namespace Hyperframework\Db;

use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Db\Test\DbCustomConnection;
use Hyperframework\Db\Test\TestCase as Base;

class DbClientTest extends Base {
    protected function setUp() {
        parent::setUp();
        DbImportCommand::execute(
            'Document',
            [
                [1, 'doc 1', 12.34], [2, 'doc 2', null],
            ],
            ['column_names' => ['id', 'name', 'decimal']]
        );
    }

    protected function tearDown() {
        DbClient::delete('Document', null);
        parent::tearDown();
    }

    public function testFindRowById() {
        $this->assertTrue(is_array(DbClient::findRowById('Document', '1')));
    }

    public function testFindColumn() {
        $this->assertSame(
            'doc 1',
            DbClient::findColumn("SELECT name FROM Document WHERE id = 1")
        );
    }

    public function testFindColumnByColumns() {
        $this->assertSame(
            1,
            DbClient::findColumnByColumns('Document', ['name' => 'doc 1'], 'id')
        );
    }

    public function testFindColumnById() {
        $this->assertTrue(is_string(
            DbClient::findColumnById('Document', 1, 'name'))
        );
    }

    public function testFindRow() {
        $this->assertSame(
            ['name' => 'doc 1'],
            DbClient::findRow("select name from Document where id = 1")
        );
    }

    public function testFindRowByColumns() {
        $this->assertSame(
            ['id' => 1],
            DbClient::findRowByColumns('Document', ['name' => 'doc 1'], ['id'])
        );
    }

    public function testFindAll() {
        $this->assertEquals(
            [['name' => 'doc 1']],
            DbClient::findAll("select name from Document where id = 1")
        );
    }

    public function testFindAllByColumns() {
        $this->assertSame(
            [['id' => 1]],
            DbClient::findAllByColumns('Document', ['name' => 'doc 1'], ['id'])
        );
    }

    public function testFind() {
        $this->assertInstanceof(
            'Hyperframework\Db\DbStatementProxy',
            DbClient::find("select name from Document where id = 1")
        );
    }

    public function testFindByColumns() {
        $this->assertInstanceOf(
            'Hyperframework\Db\DbStatementProxy',
            DbClient::findByColumns('Document', ['name' => 'doc 1'])
        );
    }

    public function testCount() {
        $this->assertSame(
            1, DbClient::count('Document', 'id > ?', 1)
        );
    }

    public function testMin() {
        $this->assertSame(
            1, DbClient::min('Document', 'id', 'id > ?', 0)
        );
    }

    public function testMax() {
        $this->assertSame(
            2, DbClient::max('Document', 'id', 'id > ?', 1)
        );
    }

    public function testAverage() {
        $this->assertEquals(
            1.5, DbClient::average('Document', 'id', 'id > ?', 0)
        );
    }

    public function testInsert() {
        DbClient::insert('Document', ['id' => 3]);
        $this->assertSame(3, DbClient::count('Document'));
    }

    public function testUpdate() {
        DbClient::update('Document', ['name' => 'updated'], 'id = ?', 1);
        $row = DbClient::findRowById('Document', 1);
        $this->assertSame('updated', $row['name']);
    }

    public function testDelete() {
        DbClient::delete('Document', 'id = ?', 1);
        $this->assertFalse(DbClient::findRowById('Document', 1));
    }

    public function testDeleteById() {
        DbClient::delete('Document', 1);
        $this->assertFalse(DbClient::findRowById('Document', 1));
    }

    public function testSave() {
        $row = [];
        DbClient::save('Document', $row);
        $this->assertTrue(isset($row['id']));
    }

    public function testExecute() {
        $this->assertSame(
            1, DbClient::execute('DELETE FROM Document WHERE id = ?', 1)
        );
        $this->assertFalse(DbClient::findRowById('Document', 1));
    }

    public function testGetLastInsertId() {
        $row = [];
        DbClient::save('Document', $row);
        $this->assertNotNull(DbClient::getLastInsertId());
    }

    public function testTransaction() {
        DbClient::beginTransaction();
        $this->assertTrue(DbClient::inTransaction());
        DbClient::delete('Document', 1);
        DbClient::commit();
        $this->assertFalse(DbClient::inTransaction());
        $this->assertFalse(DbClient::findRowById('Document', 1));
    }

    public function testRollback() {
        DbClient::beginTransaction();
        DbClient::delete('Document', 1);
        DbClient::rollback();
        $this->assertTrue(is_array(DbClient::findRowById('Document', 1)));
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidEngineConfig() {
        DbClient::setEngine(null);
        Config::set('hyperframework.db.client.engine_class', 'Unknown');
        try {
            DbClient::delete('Document', 1);
        } catch (Exception $e) {
            Config::remove('hyperframework.db.client.engine_class');
            throw $e;
        }
        Config::remove('hyperframework.db.client.engine_class');
    }

    public function testSetCustomEngineUsingConfig() {
        DbClient::setEngine(null);
        Config::set(
            'hyperframework.db.client.engine_class',
            'Hyperframework\Db\Test\DbCustomClientEngine'
        );
        $this->assertInstanceOf(
            'Hyperframework\Db\Test\DbCustomClientEngine', DbClient::getEngine()
        );
        Config::remove('hyperframework.db.client.engine_class');
        DbClient::setEngine(null);
    }

    public function testQuoteIdentifier() {
       $this->assertSame(
           1, strpos(DbClient::quoteIdentifier('id'), 'id')
       );
    }

    public function testPrepare() {
        $this->assertTrue(DbClient::prepare('SELECT * FROM Document', [])
            instanceof DbStatementProxy);
    }

    public function testSetConnection() {
        $connection = new DbCustomConnection;
        DbClient::setConnection($connection);
        $this->assertTrue($connection === DbClient::getConnection());
        DbClient::setConnection(null);
    }

    public function testConnect() {
        DbClient::connect('backup');
        $connection = Dbclient::getConnection();
        $this->assertSame('backup', $connection->getName());
        DbClient::connect('default');
    }
}
