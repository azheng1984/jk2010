<?php
namespace Hyperframework\Db;

interface IDbProfileHandler {
    /**
     * @param array $profile
     */
    function handle(array $profile);
}
