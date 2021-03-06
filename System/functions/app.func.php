<?php
/********************************暂时不用******************************************
 * 核心路由

 * Created by PhpStorm.
 * 作者：NumberWolf
 * Email：porschegt23@foxmail.com
 * APACHE 2.0 LICENSE
 * Copyright [2016] [Chang Yanlong]

 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

 **************************************************************************/

$app_router = system::load_config('config');
Roller($app_router);

function Roller($app_router) {
    //// Controller
    // 选取目录Home(Index默认)
    $route_home = preg_match('/^[a-z]/' , $_GET['Home']) ? $_GET['Home'] : $app_router['Home'];

    // 选取页面Controller
    $route_control = preg_match('/^[a-z]/' , $_GET['Cont']) ? $_GET['Cont'] : $app_router['Cont'];

    // 选取方法Method
    $route_method = preg_match('/^[a-z]/' , $_GET['Meth']) ? $_GET['Meth'] : $app_router['Meth'];

    $module = CONT_PATH . '/' . $route_home;
    $controller = $module . '/' . $route_control . '.php';

    if(!is_dir($module)) die('<h1>RollerPHP: \'' . $route_home . '\' 找不到</h1>');

    if(!file_exists($controller)){
        die('<h1>RollerPHP:控制器文件 \'' . $route_control . '\' 找不到!');

    }else{

        include($controller);
        $ctrl = new $route_control();

        if(!method_exists($ctrl , $route_method)){
            die('<h1>RollerPHP: \'' . $route_method . '\' 未定义</h1>');
        }else{
            $dataArr = array();

            foreach ($_GET as $key => $value) {
                if ($key != 'Home' && $key != 'Cont' && $key != 'Meth') {
                    $dataArr[$key] = $value;
                }
            }

            if ($_POST) {
                foreach ($_POST as $key => $value) {
                    $dataArr[$key] = $value;
                }
            }

            if ($_FILES) {
                // var_dump($_FILES);
                /*
                array(1) { ["textFile"]=> array(5) { ["name"]=> string(19) "PoweredByMacOSX.gif" ["type"]=> string(9) "image/gif" ["tmp_name"]=> string(26) "/private/var/tmp/phpigeW2F" ["error"]=> int(0) ["size"]=> int(3726) } } 
                */
                $upload_error_arr = array();

                foreach ($_FILES as $key => $value) {

                    if ($_FILES[$key]["error"] > 0) {
                        // echo "Error: " . $_FILES[$key]["error"] . "<br />";

                        array_push($upload_error_arr, array('upload_errorMsg' => $_FILES[$key]["error"]));
                    } else {
                        // echo "Upload: " . $_FILES[$key]["name"] . "<br />";
                        // echo "Type: " . $_FILES[$key]["type"] . "<br />";
                        // echo "Size: " . ($_FILES[$key]["size"] / 1024) . " Kb<br />";
                        // echo "Stored in: " . $_FILES[$key]["tmp_name"];

                        $path = SOTRAGE_PATH .'/' . $_FILES[$key]["name"];

                        if ($_GET['class']) {
                            // echo "<hr>class:".$_GET['class'];
                            $path = SOTRAGE_PATH .'/' .$_GET['class'].'/'.$_FILES[$key]["name"];
                        }

                        move_uploaded_file($_FILES[$key]["tmp_name"], $path);

                        // echo "Stored in: " . dirname(dirname(dirname(__FILE__))) . '/Storage/' . $_FILES[$key]["name"];
                    }
                }

                $dataArr['upload_error'] = $upload_error_arr;
            }

            /**
             **参数以数组形式传递
             **/
            return $ctrl->$route_method($dataArr);
        }
    }
}
