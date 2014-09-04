<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbImportCommand;
use Hyperframework\Db\DbProfiler;
use Hyperframework\WebClient;
use PDO;

class Action {
    public function before() {
        CsrfProtection::run();
$f = fopen('/home/az/vim74/Filelist', 'r');

$client2 = new WebClient;
$client = clone $client2;

echo $client->get('http://localhost/#hi', array(
        'query_params' => array(
            'b' => 1,
            'abc' => 'hel?#我们'
        )
    )
);

print_r($client->getInfo());
echo $client->post(
    'http://localhost/index.php?b=1'
    ,array('multipart/form-data' => array(
//    't2[]' => array('content' => 'hi', 'type' => 'application/octet-stream', 'file_name' => 'hi'),
//    array('name' => 't[]', 'content' => 'hi=helo'),
    't[]' => array('content' => 'hi=helo2', 'type' => ''),
    't2[]' => array('file' => '/home/az/vim74/Filelist', 'type' => 'application/octet-stream'),
    array('name' => 't2[]', 'file' => '/home/az/:w', 'type' => 'application/octet-stream')
    )),
     array(CURLOPT_HEADER => 1, CURLINFO_HEADER_OUT => 1)
);

echo filesize('/home/az/vim74/uninstal.txt');
print_r($client->getInfo());
$p = true;
//$client->post('http://localhost', array('file' => '/home/az/vim74/Filelist'));
//echo $client->post('http://localhost?b=1', '@/home/az/vim74/Filelist');
$client = new WebClient;
$client->setOptions(array(
    CURLOPT_HEADER => 1,
//    CURLOPT_INFILE => $f,
//    CURLOPT_INFILESIZE => 5,// filesize('/home/az/vim74/Filelist'),
//    CURLOPT_WRITEHEADER => $f,
    CURLINFO_HEADER_OUT => 1,
//    CURLOPT_UPLOAD => true,
//    CURLOPT_PUT => true,
    //
    CURLOPT_POST => true,
//    CURLOPT_COOKIE => 'hi',
//    CURLOPT_COOKIE => null,
    CURLOPT_POSTFIELDS => array(
        'name' => 'hi',
        'file[0]' => curl_file_create('/home/az/Desktop/today', null),
        'file[1]' => curl_file_create('/home/az/Desktop/today', null)),
//    CURLOPT_POSTFIELDS => 'hello from post fields',
    CURLOPT_HTTPHEADER => array(
       'Expect:',
        'Content-Length:',// . filesize('/tmp/xx.txt'),
       'Content-Type:applicatoin/json',
//        'Content-Type: application/x-www-form-urlencoded',
    ),
//    array('application/json' => 'sdsdfdf'),
//    array('multipart/form-data' => array(
//        'file' => array('mime' => 'pdf', 'name' => 'lil', 'type' => 'file')
//    ));
//    CURLOPT_READFUNCTION => function($h, $b, $c) use(&$p) {
//      //var_dump($h);
//  //return;
//   echo ('-------------hi---------------');
//   echo $c;
//   if ($p === true) {
//       $p = false;
//       return 'hi=hi';
//   }
//    },
//    CURLOPT_WRITEFUNCTION => function($h, $x) {
////echo $x;
//        return strlen($x);
//    },
//    CURLOPT_HEADERFUNCTION => function($h, $x) {
//
//        //var_dump($h);
//echo $x;
//        return strlen($x);
//    }
));

for ($i = 0; $i < 1; ++$i) {
    //echo '.';
    //if (strlen($r = ) === 0) {
    //    echo  $r;
        echo $client->post('http://localhost/index.php?b=1');
    //};
//    ob_flush();
}
var_dump($client->getInfo());
//var_dump( $client->getResponseHeaders());
//echo $info['request_header'];
//$client->close();
//
//$time_end = microtime(true);
//$time = $time_end - $time_start;
//echo "Did in $time seconds\n";
    }

    public function after($ctx) {
        //echo 'xx';
    }

    public function patch($ctx) {
        echo 'hello';
//        $article = $ctx->getForm('article');
//        if (Article::isValid($article, $errors) === false) {
//            return compact('article', 'errors');
//        }
//        Article::save($article);
//        $ctx->redirect('/articles/' . $article['id']);
    }
}
