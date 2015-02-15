<?php
namespace Hyperframework\Db;

use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Db\Test\DbCustomConnection;
use Hyperframework\Db\Test\TestCase as Base;

class DbClientTest extends Base {
    private $engine;

    protected function setUp() {
        parent::setUp();
        $this->engine = $this->getMockBuilder(
            'Hyperframework\Db\Test\DbCustomClientEngine'
        )->enableArgumentCloning()->getMock();
        DbClient::setEngine($this->engine);
    }

    protected function tearDown() {
        DbClient::setEngine(null);
        parent::tearDown();
    }

    public function testFindColumn() {
        $this->engine->expects($this->once())->method('findColumn')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findColumn("sql", 'param'));
    }

    public function testFindColumnWithoutParam() {
        $this->engine->expects($this->once())->method('findColumn')->with(
            $this->equalTo('sql')
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findColumn("sql"));
    }


    public function testFindColumnByColumns() {
        $this->engine->expects($this->once())->method('findColumnByColumns')
            ->with(
                $this->equalTo('table'),
                $this->equalTo([]),
                $this->equalTo('id')
            )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findColumnByColumns('table', [], 'id'));
    }

    public function testFindColumnById() {
        $this->engine->expects($this->once())->method('findColumnById')
            ->with(
                $this->equalTo('table'),
                $this->equalTo(1),
                $this->equalTo('name')
            )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findColumnById('table', 1, 'name'));
    }

    public function testFindRow() {
        $this->engine->expects($this->once())->method('findRow')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findRow("sql", 'param'));
    }

    public function testFindRowByColumns() {
        $this->engine->expects($this->once())->method('findRowByColumns')
            ->with(
                $this->equalTo('table'),
                $this->equalTo([]),
                $this->equalTo(['name'])
            )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findRowByColumns('table', [], ['name']));
    }

    public function testFindAll() {
        $this->engine->expects($this->once())->method('findAll')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findAll("sql", 'param'));
    }

    public function testFindAllByColumns() {
        $this->engine->expects($this->once())->method('findAllByColumns')
            ->with(
                $this->equalTo('table'),
                $this->equalTo([]),
                $this->equalTo(['name'])
            )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findAllByColumns('table', [], ['name']));
    }

    public function testFind() {
        $this->engine->expects($this->once())->method('find')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::find("sql", 'param'));
    }

    public function testFindByColumns() {
        $this->engine->expects($this->once())->method('findByColumns')
            ->with(
                $this->equalTo('table'),
                $this->equalTo([]),
                $this->equalTo(['name'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findByColumns('table', [], ['name']));
    }

    public function testCount() {
        $this->engine->expects($this->once())->method('count')->with(
            $this->equalTo('table'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::count("table", 'where', 'param'));
    }

    public function testMin() {
        $this->engine->expects($this->once())->method('min')->with(
            $this->equalTo('table'),
            $this->equalTo('column'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::min("table", 'column', 'where', 'param'));
    }

    public function testMax() {
        $this->engine->expects($this->once())->method('max')->with(
            $this->equalTo('table'),
            $this->equalTo('column'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(
            1, DbClient::max("table", 'column', 'where', 'param')
        );
    }

    public function testAverage() {
        $this->engine->expects($this->once())->method('average')->with(
            $this->equalTo('table'),
            $this->equalTo('column'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(
            1, DbClient::average("table", 'column', 'where', 'param')
        );
    }

    public function testInsert() {
        $this->engine->expects($this->once())->method('insert')->with(
            $this->equalTo('table'),
            $this->equalTo(['key' => 'value'])
        );
        DbClient::insert('table', ['key' => 'value']);
    }

    public function testUpdate() {
        $this->engine->expects($this->once())->method('update')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::update('table', [], 'where', 'param'));
    }

    public function testDelete() {
        $this->engine->expects($this->once())->method('delete')->with(
            $this->equalTo('table'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::delete('table', 'where', 'param'));
    }

    public function testDeleteById() {
        $this->engine->expects($this->once())->method('deleteById')->with(
            $this->equalTo('table'),
            $this->equalTo('id')
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::deleteById('table', 'id'));
    }

    public function testSave() {
        $this->engine->expects($this->once())->method('save')->will(
            $this->returnCallback(function($table, array &$row) {
                $this->assertSame('table', $table);
                $row['id'] = 1;
                return 1;
            })
        );
        $row = [];
        $this->assertSame(1, DbClient::save('table', $row));
        $this->assertTrue($row['id'] === 1);
    }

    public function testExecute() {
        $this->engine->expects($this->once())->method('execute')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::execute("sql", 'param'));
    }

    public function testGetLastInsertId() {
        $this->engine->expects($this->once())->method('getLastInsertId')->
            will($this->returnValue(1));
        $this->assertSame(1, DbClient::getLastInsertId());
    }

//    public function testTransaction() {
//        DbClient::beginTransaction();
//        $this->assertTrue(DbClient::inTransaction());
//        DbClient::delete('Document', 1);
//        DbClient::commit();
//        $this->assertFalse(DbClient::inTransaction());
//        $this->assertFalse(DbClient::findRowById('Document', 1));
//    }

//    public function testRollback() {
//        DbClient::beginTransaction();
//        DbClient::delete('Document', 1);
//        DbClient::rollback();
//        $this->assertTrue(is_array(DbClient::findRowById('Document', 1)));
//    }
//
//    public function testQuoteIdentifier() {
//       $this->assertSame(
//           1, strpos(DbClient::quoteIdentifier('id'), 'id')
//       );
//    }
//
//    public function testPrepare() {
//        $this->assertTrue(DbClient::prepare('SELECT * FROM Document', [])
//            instanceof DbStatementProxy);
//    }
//
//    public function testSetConnection() {
//        $connection = new DbCustomConnection;
//        DbClient::setConnection($connection);
//        $this->assertTrue($connection === DbClient::getConnection());
//        DbClient::setConnection(null);
//    }
//
//    public function testConnect() {
//        DbClient::connect('backup');
//        $connection = DbClient::getConnection();
//        $this->assertSame('backup', $connection->getName());
//        DbClient::connect('default');
//    }

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
}
