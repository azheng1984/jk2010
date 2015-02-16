<?php
namespace Hyperframework\Db;

use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Db\Test\DbCustomClientEngine;
use Hyperframework\Db\Test\TestCase as Base;

class DbClientTest extends Base {
    private $engine;

    protected function setUp() {
        parent::setUp();
        $this->engine = $this->getMockBuilder(
            'Hyperframework\Db\DbClientEngine'
        )->getMock();
        DbClient::setEngine($this->engine);
    }

    protected function tearDown() {
        DbClient::setEngine(null);
        parent::tearDown();
    }

    public function testFindColumn() {
        $this->mockEngineMethod('findColumn')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findColumn("sql", 'param'));
    }

    public function testFindColumnWithoutParam() {
        $this->mockEngineMethod('findColumn')->with(
            $this->equalTo('sql')
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findColumn("sql"));
    }

    public function testFindColumnByColumns() {
        $this->mockEngineMethod('findColumnByColumns')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo('id')
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findColumnByColumns('table', [], 'id'));
    }

    public function testFindColumnById() {
        $this->mockEngineMethod('findColumnById')->with(
            $this->equalTo('table'),
            $this->equalTo(1),
            $this->equalTo('name')
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findColumnById('table', 1, 'name'));
    }

    public function testFindRow() {
        $this->mockEngineMethod('findRow')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findRow("sql", 'param'));
    }

    public function testFindRowByColumns() {
        $this->mockEngineMethod('findRowByColumns')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo(['name'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findRowByColumns('table', [], ['name']));
    }

    public function testFindAll() {
        $this->mockEngineMethod('findAll')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findAll("sql", 'param'));
    }

    public function testFindAllByColumns() {
        $this->mockEngineMethod('findAllByColumns')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo(['name'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findAllByColumns('table', [], ['name']));
    }

    public function testFind() {
        $this->mockEngineMethod('find')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::find("sql", 'param'));
    }

    public function testFindByColumns() {
        $this->mockEngineMethod('findByColumns')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo(['name'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::findByColumns('table', [], ['name']));
    }

    public function testCount() {
        $this->mockEngineMethod('count')->with(
            $this->equalTo('table'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::count("table", 'where', 'param'));
    }

    public function testMin() {
        $this->mockEngineMethod('min')->with(
            $this->equalTo('table'),
            $this->equalTo('column'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(
            1, DbClient::min("table", 'column', 'where', 'param')
        );
    }

    public function testMax() {
        $this->mockEngineMethod('max')->with(
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
        $this->mockEngineMethod('average')->with(
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
        $this->mockEngineMethod('insert')->with(
            $this->equalTo('table'),
            $this->equalTo(['key' => 'value'])
        );
        DbClient::insert('table', ['key' => 'value']);
    }

    public function testUpdate() {
        $this->mockEngineMethod('update')->with(
            $this->equalTo('table'),
            $this->equalTo([]),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::update('table', [], 'where', 'param'));
    }

    public function testDelete() {
        $this->mockEngineMethod('delete')->with(
            $this->equalTo('table'),
            $this->equalTo('where'),
            $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::delete('table', 'where', 'param'));
    }

    public function testDeleteById() {
        $this->mockEngineMethod('deleteById')->with(
            $this->equalTo('table'),
            $this->equalTo('id')
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::deleteById('table', 'id'));
    }

    public function testSave() {
        $this->mockEngineMethod('save')->will(
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
        $this->mockEngineMethod('execute')->with(
            $this->equalTo('sql'), $this->equalTo(['param'])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::execute("sql", 'param'));
    }

    public function testGetLastInsertId() {
        $this->mockEngineMethod('getLastInsertId')->will($this->returnValue(1));
        $this->assertSame(1, DbClient::getLastInsertId());
    }

    public function testBeginTransaction() {
        $this->mockEngineMethod('beginTransaction');
        DbClient::beginTransaction();
    }

    public function testInTransaction() {
        $this->mockEngineMethod('inTransaction')->will($this->returnValue(1));
        $this->assertSame(1, DbClient::inTransaction());
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
        $this->mockEngineMethod('quoteIdentifier')->with(
            $this->equalTo('string')
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::quoteIdentifier('string'));
    }

    public function testPrepare() {
        $this->mockEngineMethod('prepare')->with(
            $this->equalTo('sql'), $this->equalTo([])
        )->will($this->returnValue(1));
        $this->assertSame(1, DbClient::prepare('sql', []));
    }

    public function testSetConnection() {
        $this->mockEngineMethod('setConnection')->with(
            $this->equalTo($this)
        );
        DbClient::setConnection($this);
    }

    public function testGetConnection() {
        $this->mockEngineMethod('getConnection')->will($this->returnValue(1));
        $this->assertSame(1, DbClient::getConnection());
    }

    public function testConnect() {
        $this->mockEngineMethod('connect')->with($this->equalTo('master'));
        DbClient::connect('master');
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMockBuilder(
            'Hyperframework\Db\DbClientEngine'
        )->getMock();
        DbClient::setEngine($engine);
        return $engine->expects($this->once())->method($method);
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
}
