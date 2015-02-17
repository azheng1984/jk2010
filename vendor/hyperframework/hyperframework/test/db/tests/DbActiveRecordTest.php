<?php
namespace Hyperframework\Db;

use Hyperframework\Db\Test\Document;
use Hyperframework\Db\Test\TestCase as Base;

class DbActiveRecordTest extends Base {
    public function testSave() {
        $document = new Document;
        $document['name'] = 'doc 1';
        $document->save();
        $this->assertSame(1, DbClient::count('Document'));
        $this->assertTrue(isset($document['id']));
    }
}
