<?php
namespace Hyperframework\Db;

use stdClass;
use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Db\Test\DbCustomConnection;
use Hyperframework\Db\Test\DbCustomClientEngine;
use Hyperframework\Db\Test\TestCase as Base;

class DbClientTest extends Base {
    protected function setUp() {
        parent::setUp();
        DbClient::setEngine(null);
    }

    protected function tearDown() {
        DbClient::setEngine(null);
        parent::tearDown();
    }

    public function testFindColumn() {
        $result = new stdClass;
        $this->mockEngineMethod('findColumn')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame($result, DbClient::findColumn("sql", 'param'));
    }

    public function testFindColumnWithoutParam() {
        $result = new stdClass;
        $this->mockEngineMethod('findColumn')->with(
            $this->equalTo('sql')
        )->will($this->returnValue($result));
        $this->assertSame($result, DbClient::findColumn("sql"));
    }

    public function testFindColumnByColumns() {
        $result = new stdClass;
        $this->mockEngineMethod('findColumnByColumns')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo('id')
        )->will($this->returnValue($result));
        $this->assertSame(
            $result, DbClient::findColumnByColumns('table', [], 'id')
        );
    }

    public function testFindColumnById() {
        $result = new stdClass;
        $this->mockEngineMethod('findColumnById')->with(
            $this->equalTo('table'),
            $this->equalTo(1),
            $this->equalTo('name')
        )->will($this->returnValue($result));
        $this->assertSame(
            $result, DbClient::findColumnById('table', 1, 'name')
        );
    }

    public function testFindRow() {
        $result = new stdClass;
        $this->mockEngineMethod('findRow')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame($result, DbClient::findRow("sql", 'param'));
    }

    public function testFindRowByColumns() {
        $result = new stdClass;
        $this->mockEngineMethod('findRowByColumns')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo(['name'])
        )->will($this->returnValue($result));
        $this->assertSame(
            $result, DbClient::findRowByColumns('table', [], ['name'])
        );
    }

    public function testFindAll() {
        $result = new stdClass;
        $this->mockEngineMethod('findAll')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame($result, DbClient::findAll("sql", 'param'));
    }

    public function testFindAllByColumns() {
        $result = new stdClass;
        $this->mockEngineMethod('findAllByColumns')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo(['name'])
        )->will($this->returnValue($result));
        $this->assertSame(
            $result, DbClient::findAllByColumns('table', [], ['name'])
        );
    }

    public function testFind() {
        $result = new stdClass;
        $this->mockEngineMethod('find')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame($result, DbClient::find("sql", 'param'));
    }

    public function testFindByColumns() {
        $result = new stdClass;
        $this->mockEngineMethod('findByColumns')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo(['name'])
        )->will($this->returnValue($result));
        $this->assertSame(
            $result, DbClient::findByColumns('table', [], ['name'])
        );
    }

    public function testCount() {
        $result = new stdClass;
        $this->mockEngineMethod('count')->with(
            $this->equalTo('table'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame($result, DbClient::count("table", 'where', 'param'));
    }

    public function testMin() {
        $result = new stdClass;
        $this->mockEngineMethod('min')->with(
            $this->equalTo('table'),
            $this->equalTo('column'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame(
            $result, DbClient::min("table", 'column', 'where', 'param')
        );
    }

    public function testMax() {
        $result = new stdClass;
        $this->mockEngineMethod('max')->with(
            $this->equalTo('table'),
            $this->equalTo('column'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame(
            $result, DbClient::max("table", 'column', 'where', 'param')
        );
    }

    public function testAverage() {
        $result = new stdClass;
        $this->mockEngineMethod('average')->with(
            $this->equalTo('table'),
            $this->equalTo('column'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame(
            $result, DbClient::average("table", 'column', 'where', 'param')
        );
    }

    public function testInsert() {
        $this->mockEngineMethod('insert')->with(
            $this->equalTo('table'),
            $this->equalTo(['key' => 'value'])
        );
        DbClient::insert('table', ['key' => 'value']);
    }

    public function testUpdate() {
        $result = new stdClass;
        $this->mockEngineMethod('update')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame(
            $result, DbClient::update('table', [], 'where', 'param')
        );
    }

    public function testDelete() {
        $result = new stdClass;
        $this->mockEngineMethod('delete')->with(
            $this->equalTo('table'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame($result, DbClient::delete('table', 'where', 'param'));
    }

    public function testDeleteById() {
        $result = new stdClass;
        $this->mockEngineMethod('deleteById')->with(
            $this->equalTo('table'),
            $this->equalTo('id')
        )->will($this->returnValue($result));
        $this->assertSame($result, DbClient::deleteById('table', 'id'));
    }

    public function testSave() {
        $result = new stdClass;
        $this->mockEngineMethod('save')->will(
            $this->returnCallback(function($table, array &$row) use ($result) {
                $this->assertSame('table', $table);
                $row['id'] = 1;
                return $result;
            })
        );
        $row = [];
        $this->assertSame($result, DbClient::save('table', $row));
        $this->assertTrue($row['id'] === 1);
    }

    public function testExecute() {
        $result = new stdClass;
        $this->mockEngineMethod('execute')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue($result));
        $this->assertSame($result, DbClient::execute("sql", 'param'));
    }

    public function testGetLastInsertId() {
        $result = new stdClass;
        $this->mockEngineMethod('getLastInsertId')->will(
            $this->returnValue($result)
        );
        $this->assertSame($result, DbClient::getLastInsertId());
    }

    public function testBeginTransaction() {
        $this->mockEngineMethod('beginTransaction');
        DbClient::beginTransaction();
    }

    public function testInTransaction() {
        $result = new stdClass;
        $this->mockEngineMethod('inTransaction')->will(
            $this->returnValue($result)
        );
        $this->assertSame($result, DbClient::inTransaction());
    }

    public function testCommit() {
        $this->mockEngineMethod('commit');
        DbClient::commit();
    }

    public function testRollback() {
        $this->mockEngineMethod('rollback');
        DbClient::rollback();
    }

    public function testQuoteIdentifier() {
        $result = new stdClass;
        $this->mockEngineMethod('quoteIdentifier')->with(
            $this->equalTo('string')
        )->will($this->returnValue($this));
        $this->assertSame($this, DbClient::quoteIdentifier('string'));
    }

    public function testPrepare() {
        $result = new stdClass;
        $this->mockEngineMethod('prepare')->with(
            $this->equalTo('sql'), $this->equalTo([])
        )->will($this->returnValue($this));
        $this->assertSame($this, DbClient::prepare('sql', []));
    }

    public function testSetConnection() {
        $connection = new DbCustomConnection;
        $this->mockEngineMethod('setConnection')->with(
            $this->equalTo($connection)
        );
        DbClient::setConnection($connection);
    }

    public function testGetConnection() {
        $result = new stdClass;
        $this->mockEngineMethod('getConnection')->will(
            $this->returnValue($result)
        );
        $this->assertSame($result, DbClient::getConnection());
    }

    public function testConnect() {
        $this->mockEngineMethod('connect')->with($this->equalTo('master'));
        DbClient::connect('master');
    }

    public function testSetEngineUsingConfig() {
        DbClient::setEngine(null);
        Config::set(
            'hyperframework.db.client.engine_class',
            'Hyperframework\Db\Test\DbCustomClientEngine'
        );
        $this->assertTrue(
            DbClient::getEngine() instanceof DbCustomClientEngine
        );
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidEngineConfig() {
        DbClient::setEngine(null);
        Config::set('hyperframework.db.client.engine_class', 'Unknown');
        try {
            DbClient::getEngine();
        } catch (Exception $e) {
            Config::remove('hyperframework.db.client.engine_class');
            throw $e;
        }
        Config::remove('hyperframework.db.client.engine_class');
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMockBuilder(
            'Hyperframework\Db\DbClientEngine'
        )->getMock();
        DbClient::setEngine($engine);
        return $engine->expects($this->once())->method($method);
    }
}
