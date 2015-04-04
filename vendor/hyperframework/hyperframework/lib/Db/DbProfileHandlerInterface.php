<?php
namespace Hyperframework\Db;

interface DbProfileHandlerInterface {
    /**
     * @param array $profile
     */
    function handle(array $profile);
}
