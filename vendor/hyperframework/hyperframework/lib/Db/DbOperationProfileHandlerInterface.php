<?php
namespace Hyperframework\Db;

interface DbOperationProfileHandlerInterface {
    /**
     * @param array $profile
     */
    function handle(array $profile);
}
