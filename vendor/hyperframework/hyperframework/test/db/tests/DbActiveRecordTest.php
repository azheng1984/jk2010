<?php
namespace Hyperframework\Db;

use Hyperframework\Db\Test\Document;
use Hyperframework\Db\Test\TestCase as Base;

class DbActiveRecordTest extends Base {
    protected function setUp() {
        parent::setUp();
        DbImportCommand::execute(
            'Document',
            [[1, 'doc 1', 12.34], [2, 'doc 2', 0]],
            ['column_names' => ['id', 'name', 'decimal']]
        );
    }

    protected function tearDown() {
        DbClient::delete('Document', null);
        parent::tearDown();
    }

    public function testInsert() {
        $document = new Document(['name' => 'doc 1']);
        $document->insert();
        $this->assertSame(3, DbClient::count('Document'));
        $this->assertTrue($document->getId() !== null);
    }

    public function testUpdate() {
        $document = new Document(['name' => 'doc 1']);
        $document->insert();
        $document->setName('updated');
        $document->update();
        $this->assertSame(
            'updated',
            DbClient::findColumnById('Document', $document->getId(), 'name')
        );
    }

    public function testDelete() {
        $document = new Document(['name' => 'doc 1']);
        $document->insert();
        $document->delete();
        $this->assertSame(2, DbClient::count('Document'));
    }

    /**
     * @expectedException Hyperframework\Db\DbActiveRecordException
     */
    public function testDeleteWithoutId() {
        $document = new Document(['name' => 'doc 1']);
        $document->delete();
    }

    /**
     * @expectedException Hyperframework\Db\DbActiveRecordException
     */
    public function testUpdateWithoutId() {
        $document = new Document(['name' => 'doc 1']);
        $document->update();
    }

    /**
     * @expectedException Hyperframework\Db\DbActiveRecordException
     */
    public function testUpdateWhichOnlyHasIdColumn() {
        $document = new Document(['id' => 1]);
        $document->update();
    }

    public function testCount() {
        $this->assertSame(2, Document::count());
        $this->assertSame(1, Document::count('name = ?', 'doc 1'));
    }

    public function testMax() {
        $this->assertSame('12.34', Document::max('decimal'));
        $this->assertSame(
            '0.00', Document::max('decimal', 'name = ?', 'doc 2')
        );
    }

    public function testGetCloumn() {
        $document = new Document(['id' => 1]);
        $this->assertSame(
            1, $this->callProtectedMethod($document, 'getColumn', ['id'])
        );
        $this->assertNull(
            $this->callProtectedMethod($document, 'getColumn', ['unknown'])
        );
    }

    public function testHasCloumn() {
        $document = new Document(['id' => 1]);
        $this->assertTrue(
            $this->callProtectedMethod($document, 'hasColumn', ['id'])
        );
        $this->assertFalse(
            $this->callProtectedMethod($document, 'hasColumn', ['unknown'])
        );
    }
}
