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

    public function testFindByColumns() {
        $doc = Document::find(['name' => 'doc 1']);
        $this->assertSame(1, $doc->getId());
    }

    public function testFindByString() {
        $doc = Document::find('name = ?', 'doc 1');
        $this->assertSame(1, $doc->getId());
    }

    public function testFindNothing() {
        $this->assertNull(Document::find(['id' => 3]));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFindByInvalidArgument() {
        Document::find(1);
    }

    public function testFindById() {
        $doc = Document::findById(1);
        $this->assertSame(1, $doc->getId());
    }

    public function testFindByNonExistentId() {
        $this->assertNull(Document::findById(3));
    }

    public function testFindBySql() {
        $doc = Document::findBySql('SELECT * FROM Document WHERE id = ? ', 1);
        $this->assertSame(1, $doc->getId());
    }

    public function testFindBySqlReturnNull() {
        $this->assertNull(
            Document::findBySql('SELECT * FROM Document WHERE id = ? ', 3)
        );
    }

    public function testFindAll() {
        $docs = Document::findAll();
        $this->assertSame(2, count($docs));
    }

    public function testFindAllByColumns() {
        $docs = Document::findAll(['name' => 'doc 1']);
        $this->assertSame(1, $docs[0]->getId());
    }

    public function testFindAllByString() {
        $docs = Document::findAll('name = ?', 'doc 1');
        $this->assertSame(1, $docs[0]->getId());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFindAllByInvalidArgument() {
        Document::findAll(1);
    }

    public function testFindAllBySql() {
        $docs = Document::findAllBySql(
            'SELECT * FROM Document WHERE id = ?', 1
        );
        $this->assertSame(1, $docs[0]->getId());
    }

    public function testInsert() {
        $doc = new Document(['name' => 'doc 1']);
        $doc->insert();
        $this->assertSame(3, DbClient::count('Document'));
        $this->assertTrue($doc->getId() !== null);
    }

    public function testUpdate() {
        $doc = new Document(['name' => 'doc 1']);
        $doc->insert();
        $doc->setName('updated');
        $doc->update();
        $this->assertSame(
            'updated',
            DbClient::findColumnById('Document', $doc->getId(), 'name')
        );
    }

    public function testDelete() {
        $doc = new Document(['name' => 'doc 1']);
        $doc->insert();
        $doc->delete();
        $this->assertSame(2, DbClient::count('Document'));
    }

    /**
     * @expectedException Hyperframework\Db\DbActiveRecordException
     */
    public function testDeleteWithoutId() {
        $doc = new Document(['name' => 'doc 1']);
        $doc->delete();
    }

    /**
     * @expectedException Hyperframework\Db\DbActiveRecordException
     */
    public function testUpdateWithoutId() {
        $doc = new Document(['name' => 'doc 1']);
        $doc->update();
    }

    /**
     * @expectedException Hyperframework\Db\DbActiveRecordException
     */
    public function testUpdateWhichOnlyHasIdColumn() {
        $doc = new Document(['id' => 1]);
        $doc->update();
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

    public function testMin() {
        $this->assertSame('0.00', Document::min('decimal'));
        $this->assertSame(
            '0.00', Document::min('decimal', 'name = ?', 'doc 2')
        );
    }

    public function testSum() {
        $this->assertSame('12.34', Document::sum('decimal'));
        $this->assertSame(
            '0.00', Document::sum('decimal', 'name = ?', 'doc 2')
        );
    }

    public function testAverage() {
        $this->assertSame('6.170000', Document::average('decimal'));
        $this->assertSame(
            '0.000000', Document::average('decimal', 'name = ?', 'doc 2')
        );
    }

    public function testGetCloumn() {
        $doc = new Document(['id' => 1]);
        $this->assertSame(
            1, $this->callProtectedMethod($doc, 'getColumn', ['id'])
        );
        $this->assertNull(
            $this->callProtectedMethod($doc, 'getColumn', ['unknown'])
        );
    }

    public function testHasCloumn() {
        $doc = new Document(['id' => 1]);
        $this->assertTrue(
            $this->callProtectedMethod($doc, 'hasColumn', ['id'])
        );
        $this->assertFalse(
            $this->callProtectedMethod($doc, 'hasColumn', ['unknown'])
        );
    }

    public function testSetColumn() {
        $doc = new Document;
        $this->callProtectedMethod($doc, 'setColumn', ['id', 1]);
        $this->verifyRow($doc, ['id' => 1]);
    }

    public function testRemoveColumn() {
        $doc = new Document(['id' => 1]);
        $this->callProtectedMethod($doc, 'removeColumn', ['id']);
        $this->verifyRow($doc, []);
    }

    public function testGetTableName() {
        $doc = new Document;
        $this->assertSame(
            'Document',
            $this->callProtectedMethod($doc, 'getTableName')
        );
    }

    public function testSetRow() {
        $doc = new Document;
        $this->callProtectedMethod($doc, 'setRow', [['id' => 1]]);
        $this->verifyRow($doc, ['id' => 1]);
    }

    private function verifyRow($doc, $value) {
        $this->assertSame(
            $value, $this->callProtectedMethod($doc, 'getRow')
        );
    }
}
