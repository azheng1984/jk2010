<?php
namespace Hyperframework\Db;

use Hyperframework\Db\Test\TestCase as Base;

class DbImportCommandTest extends Base {
    protected function tearDown() {
        DbClient::delete('Document', null);
        parent::tearDown();
    }

    public function testExecute() {
        DbImportCommand::execute(
            'Document',
            [['id' => 1, 'name' => 'doc 1', 'decimal' => 12.34]]
        );
        $this->assertSame(1, DbClient::count('Document'));
    }
}
