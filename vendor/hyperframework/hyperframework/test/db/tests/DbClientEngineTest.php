<?php
namespace Hyperframework\Db;

use PDO;
use Exception;
use Hyperframework\Db\Test\DbCustomConnection;
use Hyperframework\Common\Config;
use Hyperframework\Db\Test\TestCase as Base;

class DbClientEngineTest extends Base {
    private $engine;

    protected function setUp() {
        parent::setUp();
        DbImportCommand::execute(
            'Document',
            [[1, 'doc 1', 12.34], [2, 'doc 2', null]],
            ['column_names' => ['id', 'name', 'decimal']]
        );
        $this->engine = new DbClientEngine;
    }

    protected function tearDown() {
        DbClient::delete('Document', null);
        parent::tearDown();
    }

    public function testFindColumnByColumns() {
        $this->assertSame(1, $this->engine->findColumnByColumns(
            'Document', ['name' => 'doc 1'], 'id'
        ));
    }

    public function testFindColumnById() {
        $this->assertSame(
            'doc 1',
            $this->engine->findColumnById('Document', 1, 'name')
        );
    }

    public function testFindRow() {
        $this->assertSame(
            ['name' => 'doc 1'],
            $this->engine->findRow("SELECT name FROM Document WHERE id = 1")
        );
    }

    public function testFindRowByColumns() {
        $this->assertSame(
            ['id' => 1],
            $this->engine->findRowByColumns(
                'Document', ['name' => 'doc 1'], ['id']
            )
        );
    }

    public function testFindRowById() {
        $this->assertSame(
            ['id' => 1, 'name' => 'doc 1', 'decimal' => '12.34'],
            $this->engine->findRowById('Document', '1')
        );
    }

    public function testFindAll() {
        $this->assertEquals(
            [['name' => 'doc 1']],
            $this->engine->findAll("SELECT name FROM Document WHERE id = 1")
        );
    }

    public function testFindAllByColumns() {
        $this->assertSame(
            [['id' => 1]],
            $this->engine->findAllByColumns(
                'Document', ['name' => 'doc 1'], ['id']
            )
        );
    }

    public function testFind() {
        $statement = $this->engine->find("SELECT * FROM Document");
        $this->assertTrue($statement instanceof DbStatementProxy);
    }

    public function testFindByColumns() {
        $statement = $this->engine->findByColumns(
            'Document', ['name' => 'doc 1']
        );
        $this->assertSame(1, $statement->rowCount());
        $this->assertSame(3, count($statement->fetch(PDO::FETCH_ASSOC)));
        $this->assertSame(
            2, $this->engine->findByColumns('Document', [])->rowCount()
        );
        $this->assertSame(1, count($this->engine->findByColumns(
            'Document', [], ['name']
        )->fetch(PDO::FETCH_ASSOC)));
        $this->assertSame(2, count($this->engine->findByColumns(
            'Document', [], ['id', 'name']
        )->fetch(PDO::FETCH_ASSOC)));
        $this->assertSame(3, count($this->engine->findByColumns(
            'Document', [], []
        )->fetch(PDO::FETCH_ASSOC)));
    }

    public function testCount() {
        $this->assertSame(1, $this->engine->count('Document', 'id > ?', [1]));
        $this->assertSame(1, $this->engine->count(
            'Document', ['name' => 'doc 1'])
        );
    }

    public function testMin() {
        $this->assertSame(
            1, $this->engine->min('Document', 'id', 'id > ?', [0])
        );
        $this->assertSame(
            1, $this->engine->min('Document', 'id', ['name' => 'doc 1'])
        );
    }

    public function testMax() {
        $this->assertSame(
            2, $this->engine->max('Document', 'id', 'id > ?', [1])
        );
        $this->assertSame(
            1, $this->engine->min('Document', 'id', ['name' => 'doc 1'])
        );
    }

    public function testAverage() {
        $this->assertEquals(
            1.5, $this->engine->average('Document', 'id', 'id > ?', [0])
        );
        $this->assertEquals(
            1, $this->engine->average('Document', 'id', ['name' => 'doc 1'])
        );
    }

    public function testInsert() {
        $this->engine->insert('Document', ['id' => 3]);
        $this->assertSame(3, $this->engine->count('Document'));
    }

    public function testUpdate() {
        $this->engine->update(
            'Document', ['name' => 'updated 1'], 'id = ?', [1]
        );
        $row = $this->engine->findRowById('Document', 1);
        $this->assertSame('updated 1', $row['name']);
        $this->engine->update('Document', ['name' => 'updated 2'], ['id' => 1]);
        $row = $this->engine->findRowById('Document', 1);
        $this->assertSame('updated 2', $row['name']);
        $this->engine->update('Document', ['name' => 'updated 3'], null);
        $row = $this->engine->findRowById('Document', 1);
        $this->assertSame('updated 3', $row['name']);
        $row = $this->engine->findRowById('Document', 2);
        $this->assertSame('updated 3', $row['name']);
    }

    public function testDelete() {
        $this->engine->delete('Document', 'id = ?', [1]);
        $this->assertFalse($this->engine->findRowById('Document', 1));
        $this->engine->delete('Document', ['id' => 2]);
        $this->assertFalse($this->engine->findRowById('Document', 2));
    }

    public function testDeleteAll() {
        $this->engine->delete('Document', null);
        $this->assertTrue(0 === $this->engine->count('Document'));
    }

    public function testDeleteById() {
        $this->engine->delete('Document', 1);
        $this->assertFalse($this->engine->findRowById('Document', 1));
    }

//    public function testSaveNewRow() {
//        $row = [];
//        $this->engine->save('Document', $row);
//        $this->assertTrue(isset($row['id']));
//    }
//
//    public function testSaveExistingRow() {
//        $row = ['id' => 1, 'name' => 'updated'];
//        $this->engine->save('Document', $row);
//        $this->assertSame(
//            'updated', $this->engine->findColumnById('Document', 1, 'name')
//        );
//    }

    public function testExecute() {
        $this->assertSame(
            1, $this->engine->execute('DELETE FROM Document WHERE id = ?', [1])
        );
        $this->assertFalse($this->engine->findRowById('Document', 1));
    }

    public function testGetLastInsertId() {
        $row = [];
        $this->engine->save('Document', $row);
        $this->assertNotNull($this->engine->getLastInsertId());
    }

    public function testTransaction() {
        $this->engine->beginTransaction();
        $this->assertTrue($this->engine->inTransaction());
        $this->engine->delete('Document', 1);
        $this->engine->commit();
        $this->assertFalse($this->engine->inTransaction());
        $this->assertFalse($this->engine->findRowById('Document', 1));
    }

    public function testRollback() {
        $this->engine->beginTransaction();
        $this->engine->delete('Document', 1);
        $this->engine->rollback();
        $this->assertTrue(is_array($this->engine->findRowById('Document', 1)));
    }

    public function testQuoteIdentifier() {
       $this->assertSame(
           1, strpos($this->engine->quoteIdentifier('id'), 'id')
       );
    }

    public function testPrepare() {
        $this->assertTrue($this->engine->prepare('SELECT * FROM Document', [])
            instanceof DbStatementProxy);
    }

    public function testSetConnection() {
        $connection = new DbCustomConnection;
        $this->engine->setConnection($connection);
        $this->assertTrue($connection === $this->engine->getConnection());
    }

    public function testConnect() {
        $this->engine->connect('backup');
        $connection = $this->engine->getConnection();
        $this->assertSame('backup', $connection->getName());
    }
}
