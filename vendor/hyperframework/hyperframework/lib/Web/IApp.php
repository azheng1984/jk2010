<?php
namespace Hyperframework\Web;

interface IApp {
    /**
     * @return IRouter
     */
    function getRouter();
    function quit();
}