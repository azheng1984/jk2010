<?php
namespace Hyperframework\Web;

interface IRouter {
    function getParam($name);
    function getParams();
    function hasParam($name);
    function getModule();
    function getController();
    function getControllerClass();
    function getAction();
    function getActionMethod();
}