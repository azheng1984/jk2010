<?php
namespace Hyperframework\Web;

interface IRouter {
    /**
     * @param string $name
     * @return mixed
     */
    function getParam($name);

    /**
     * @return array
     */
    function getParams();

    /**
     * @param string $name
     * @return bool
     */
    function hasParam($name);

    /**
     * @return string
     */
    function getModule();

    /**
     * @return string
     */
    function getController();

    /**
     * @return string
     */
    function getControllerClass();

    /**
     * @return string
     */
    function getAction();

    /**
     * @return string
     */
    function getActionMethod();
}