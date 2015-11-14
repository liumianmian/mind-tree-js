<?php

$handle = fopen('游戏发行业务.csv', 'r');
$result = input_csv($handle); //解析csv
$len_result = count($result);
if($len_result==0)
{
    echo '没有任何数据！';
    exit;
}

//$data_values = substr($data_values,0,-1); //去掉最后一个逗号
fclose($handle); //关闭指针

//编码问题
$charset = array();
for($i = 1; $i < $len_result; $i++) //循环获取各字段值
{
    $data = iconv('gb2312', 'utf-8', $result[$i][0]); //中文转码
    $charset[] = getData($data);
}

//echo strlen('	');
//echo str_word_count('				a');
//var_export($charset);

//遍历取值
function getData($result) {
    $newData = array();
    list($d1, $d2) = explode('	', $result);
//    $tmp = explode('	', $d2);
    var_dump($d2);
    if(str_word_count($d2) > 0 && count(array()) > 1) {
//        $newData[$d1] = getData($d2);
//        var_dump(getData($d2));
    } else {
        $newData[$d1][] = $d2;
    }

    return $newData;
}

function input_csv($handle)
{
    $out = array ();
    $n = 0;
    while ($data = fgetcsv($handle, 10000))
    {
        $num = count($data);
        for ($i = 0; $i < $num; $i++)
        {
            $out[$n][$i] = $data[$i];
        }
        $n++;
    }
    return $out;
}