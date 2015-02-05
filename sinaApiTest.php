<?php

require 'cls_json.php';

header("Content-type:text/html;charset=utf-8");
/*
 * function getSinaDataTest
 * get the sina microblog data by sina api
 * author lou daosheng
 * time 2015-1-24 10:36:27
 */

function getSinaDataTest($source,$CurrentUser) {

//    source :your sina app key
    $url = "https://api.weibo.com/2/statuses/user_timeline.json?source=$source&page=1&count=10";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($curl, CURLOPT_USERPWD, $CurrentUser);
    $data = curl_exec($curl);
    curl_close($curl);
//    parse json code
    $json = new Services_JSON();
    $result = $json->decode($data);
//    echo "<pre>";var_dump($result);exit();
//    connect mongo
    $conn = new Mongo();
    $db = $conn->mydb;
    $collection = $db->column;
    foreach ($result as $k => $weibo) {
        foreach ($weibo as $weiboinfo) {
            $array = array("uid"=>$weiboinfo->user->id,
                            "user"=>$weiboinfo->user->screen_name,
                            "text" => $weiboinfo->text,
                            "post_tem"=>$weiboinfo->created_at);
            $result = $collection->insert($array, true);
            if(!$result){
                echo "inset db error!<br>";
            }
        }
    }
}

getSinaDataTest($source,$CurrentUser);
?>


