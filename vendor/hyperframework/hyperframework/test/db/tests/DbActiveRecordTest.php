<?php
namespace Hyperframework\Db;

use Hyperframework\Db\Test\Document;
use Hyperframework\Db\Test\TestCase as Base;

class DbActiveRecordTest extends Base {
    protected function tearDown() {
        DbClient::delete('Document', null);
        parent::tearDown();
    }

    public function testInsert() {
        $document = new Document(['name' => 'doc 1']);
        $document->insert();
        $this->assertSame(1, DbClient::count('Document'));
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
}
