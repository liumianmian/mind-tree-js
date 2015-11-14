<?php

$handle = fopen('游戏发行业务 (3).csv', 'r');
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
$maxLow = 12;
$curKey = "";
$nextKey = "";
for($i = 1; $i < $len_result; $i++) //循环获取各字段值
{
//    $data = iconv('gb2312', 'utf-8', $result[$i][0]); //中文转码

    if($result[$i][0]) {
        $curKey = $result[$i][0];
    } elseif($result[$i][1]) {
        $nextKey = $result[$i][1];
        $charset[$curKey][] = $nextKey;
    } elseif($result[$i][2]) {
        $charset[$curKey][$nextKey][] = $result[$i][2];
    }
}

$mindTreeData = array();
$n = 0;
foreach($charset as $name => $baby) {
    $mindTreeData[$n] = array(
        'id'        => 'p' . $n,
        'topic'     => $name,
        'direction' => 'left',
    );

    if(is_array($baby) && $baby) {
        foreach($baby as $value) {
            $mindTreeData[$n]['children'][] = array(
                'id'     => 'c' . $n,
                'topic'  => $value,
            );
        }
    }
}

//计算data数据
function makeMindTree($charset, $f = '') {
    //方向
    $position = array('left', 'right');
    $mindTreeData = array();
    $n = 0;
    foreach($charset as $name => $baby) {
        $mindTreeData[$n] = array(
            'id'        => 'p' . $f . $n,
            'topic'     => $name,
            'direction' => $position[rand(0, 1)],
        );

        if($baby) {
            $m = 0;
            foreach($baby as $key => $value) {
                if(!is_numeric($key)) {
                    continue;
                }

                if(isset($charset[$name][$value]) && is_array($charset[$name][$value])) {
                    $mindTreeData[$n]['children'] = makeMindTree(array($value => $charset[$name][$value]), $m . $n);
                } else {
                    $mindTreeData[$n]['children'][] = array(
                        'id'     => 'c' . $f . $m . $n,
                        'topic'  => $value,
                    );
                }

                $m++;
            }
        }

        $n++;
    }

    return $mindTreeData;
}



//print_r($charset);
//print_r(makeMindTree($charset));
print_r(json_encode(makeMindTree($charset)));

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