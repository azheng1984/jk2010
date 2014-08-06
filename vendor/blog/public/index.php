<?php
//namespace Hyperframework\Blog;

namespace Hi {
use Hyperframework\Web\Runner;
   class /*dfasdf*/ sdf/*dsaff*/ {
   }
function x($path) {
        $code = file_get_contents($path);
        $classes = array();
        $namespace = '';
        $tokens = token_get_all($code);
        $count = count($tokens);
        for ($index = 0; $index < $count; $index++) {
            if (isset($tokens[$index][0]) === false) {
                continue;
            }
            if ($tokens[$index][0] === T_NAMESPACE) {
                $namespace = '';
                ++$index;
                while ($index < $count) {
                    if (isset($tokens[$index][0]) && $tokens[$index][0] === T_STRING) {
                        $namespace .= "\\" . $tokens[$index][1];
                    } elseif ($tokens[$index] === '{' || $tokens[$index]=== ';') {
                        break;
                    }
                    ++$index;
                }
            } elseif ($tokens[$index][0] === T_CLASS) {
                while ($index < $count) {
                    if (isset($tokens[$index][0]) && $tokens[$index][0] === T_STRING) {
                        $classes[] = $namespace . "\\" . $tokens[$index][1];
                        break;
                    }
                    ++$index;
                }
            }
        }
        return $classes;
    }

print_r(x(__DIR__ .'/test.php'));
exit;
}
//define('Hyperframework\Blog\ROOT_PATH', dirname(__DIR__));
//require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
//    . DIRECTORY_SEPARATOR . 'init_const.php';
//require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
//    . DIRECTORY_SEPARATOR . 'Runner.php';
//Runner::run(__NAMESPACE__, ROOT_PATH);
