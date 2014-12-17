<?php
namespace Tc;

class Command {
    public function execute($list, array $articleList = []) {
        var_dump(\Hyperframework\Common\Inflector::pluralize('xml'));
        var_dump(\Hyperframework\Common\Inflector::singularize('TEARS'));
//        $input = file_get_contents('/home/az/Desktop/input');
//        $items = explode("\n", $input);
//        $output = [];
//        foreach ($items as $x) {
//            $x = trim($x);
//            //f ($x === '') {
//            //   continue;
//            //
//            $x = str_replace('inflect.singular(', "'", $x);
//            $x = str_replace('inflect.plural(', "'", $x);
//            $x = str_replace("i, '", "' => '", $x);
//            $x = str_replace("')", "',", $x);
//            array_unshift($output, $x);
//        }
//        //print_r($items);
//        //print_r($output);
//        file_put_contents('/home/az/Desktop/output', implode("\n", $output));
    }
}
