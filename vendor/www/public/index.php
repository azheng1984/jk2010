<?php
$stime=microtime(true);
function foo($bar) {
    //func_num_args();
     func_get_args();
}
for ($i = 0; $i < 2000000; ++$i) {
    call_user_func_array('foo', array(1));
    //foo(1);
}
$etime=microtime(true);

echo ($etime-$stime) * 1000;
exit;
