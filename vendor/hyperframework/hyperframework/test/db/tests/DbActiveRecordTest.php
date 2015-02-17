<?php
namespace Hyperframework\Db;

use Hyperframework\Db\Test\Document;
use Hyperframework\Db\Test\TestCase as Base;

class DbActiveRecordTest extends Base {
    public function testInsert() {
        $document = new Document;
        $document['name'] = 'doc 1';
        $document->insert();
        $this->assertSame(1, DbClient::count('Document'));
        $this->assertTrue(isset($document['id']));
    }

    public function testUpdate() {
        $document = new Document;
        $document['name'] = 'doc 1';
        $document->insert();
        $document['name'] = 'updated';
        $document->update();
        $this->assertSame(
            'updated',
            DbClient::findColumnById('Document', $document['id'], 'name')
        );
    }
}
