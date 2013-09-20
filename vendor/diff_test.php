<?php  
var_export(stat('/home'));
function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
    } 
 
//例子
 
//开始
$time_start = getmicrotime();
    
//这里放你的代码
 

/** 
 * 工具文件 
 * 目的在于递归比较两个文件夹 
 *  
 * 调用示例 
 * php compare_folder.php /home/temp/2 /home/temp/55 
 *  
 */  
  
//参数确定  
if (count($argv) > 1 )  
  $dir1 = del_postfix($argv[1]);  
else   
  $dir1 = '/';  
  
if (count($argv) > 2 )  
  $dir2 = del_postfix($argv[2]);  
else  
  $dir2 = '/';

  system("diff -rqH $dir1 $dir2");
//结束
$time_end = getmicrotime();
$time = $time_end - $time_start;
 
echo "Did nothing in $time seconds"; //输出运行总时间

//检查第一个路径有，后者没有或错误的方法。  
process_compare($dir1,  $dir2,  0);  
echo "===========================================================\n";  
  
//检查第2个路径的多余文件夹或文件  
process_compare($dir2 , $dir1, 1);  
echo "all OK\n";  
  
  
  
/** 
 * 去除路径末尾的/，并确保是绝对路径 
 * 
 * @param unknown_type $dir 
 * @return unknown 
 */  
function del_postfix($dir)  
{  
    if (!preg_match('#^/#', $dir)) {  
        throw new Exception('参数必须是绝对路径');  
    }  
    $dir = preg_replace('#/$#', '', $dir);  
    return $dir;  
}  
  
  
/** 
 * 公用函数，会调用一个递归方法实现比较 
 * 
 * @param string $dir1 作为标准的路径 
 * @param string $dir2 对比用的路径 
 * @param int $only_check_has 为1表示不比较文件差异，为0表示还要比较文件的md5校验和 
 */  
function process_compare($dir1, $dir2, $only_check_has){  
    compare_file_folder($dir1,  $dir1, $dir2, $only_check_has);  
}  
  
/** 
 * 真实的函数，私有函数 
 * 
 * @param string $dir1        路径1，是标准 
 * @param string $base_dir1   不变的参数路径2 
 * @param string $base_dir2   不变的待比较的路径2 
 * @param int $only_check_has 为1表示不比较文件差异，为0表示还要比较文件的md5校验和 
 *  
 */  
function compare_file_folder($dir1,  $base_dir1, $base_dir2, $only_check_has=0){  
    if (is_dir($dir1)) {  
        $handle = dir($dir1);  
        if ($dh = opendir($dir1)) {  
            while ($entry = $handle->read()) {  
                if (($entry != ".") && ($entry != "..")  && ($entry != ".svn")){  
                    $new = $dir1."/".$entry;  
                    //echo 'compare:  ' . $new . "\n";  
                    $other = preg_replace('#^'. $base_dir1 .'#' ,  $base_dir2, $new);  
                    if(is_dir($new)) {  
                        //比较  
                        if (!is_dir($other)) {  
                            echo '!!not found direction:  '. $other. '  (' . $new .")\n";  
                        }  
                        compare_file_folder($new, $base_dir1,$base_dir2,  $only_check_has) ;  
                    } else { //如果1是文件，则2也应该是文件  
                        if (!is_file($other)) {  
                            echo '!!not found file:       '. $other. '  ('.$new   .")\n";  
                        } elseif ($only_check_has ==0 && ( md5_file($other) != md5_file($new) )  ) {  
//                        } elseif ($only_check_has == 0 && ( file_get_contents($other) != file_get_contents($new)  )  ) {  
                            echo '!!file md5 error:       '. $other. '  ('.$new   .")\n";  
                        }  
                    }  
                }  
            }  
            closedir($dh);  
        }  
  
    }  
}  
  


//结束
$time_end = getmicrotime();
$time = $time_end - $time_start;
 
echo "Did nothing in $time seconds"; //输出运行总时间
?>
