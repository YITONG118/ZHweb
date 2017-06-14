<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 2017/5/14
 * Time: 16:02
 */
    header("Content-type: text/html; charset=utf-8");
//    error_reporting(0);
    $postData = json_decode(file_get_contents("php://input",true),true);

    $mysqli = new mysqli('127.0.0.1', 'root', '','ZHnt');
    if($mysqli->connect_error) {
        die("连接失败：".$mysqli->connect_error);
    }
    else {
        $mysqli->query("set names utf8");//插入数据库乱码解决办法

        $sql = "SELECT ntId FROM nt ORDER BY ntId";
        $ntId = 0;
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            // 输出每行数据
            while($row = $result->fetch_assoc()) {
                $ntId++;
                if($ntId != $row["ntId"]) {
                    $ntId--;
                    break;
                }
            }
        }
        $ntId++;
        $ntPath = "../json/".$ntId.".json";
        $png = "png/".$ntId.".png";
        $pngData = base64_decode(substr($postData["png"],22));

        $sql = "UPDATE user SET ntNum = ntNum + 1 WHERE userId = ".$postData["userId"];
        $mysqli->query($sql);

        $stmt = $mysqli->prepare("INSERT INTO nt (userId, ntId, ntName, ntPath, abstract,png) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('iissss',$postData["userId"],$ntId,$postData["ntName"],$ntPath,$postData["abstract"],$png);
        file_put_contents($ntPath, json_encode($postData["jsonMsg"]));
        $stmt->execute();
        $png = "../png/".$ntId.".png";
        file_put_contents($png,$pngData);

        echo $ntId;
        $stmt->close();
        $mysqli->close();
    }
?>