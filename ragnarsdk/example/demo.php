<?php
require_once "../src/MidTool.php";
require_once "../src/RagnarConst.php";
require_once "../src/RagnarSDK.php";
require_once "../src/Traceid.php";
require_once "../src/Util.php";

error_reporting(E_ALL);
ini_set("display_errors", "On");

//这俩必须在init之前
//设置业务日志等级
\Adinf\RagnarSDK\RagnarSDK::setLogLevel(\Adinf\RagnarSDK\RagnarConst::LOG_TYPE_INFO);

//初始化ragnar项目 实际生产环境用这个初始化,仅限FPM工作
//\Adinf\RagnarSDK\RagnarSDK::init("ragnar_projectname");

//命令行测试使用，生产环境不适用
\Adinf\RagnarSDK\RagnarSDK::devmode("ragnar_projectname");

//设置要索引的日志附加数据，在ES搜索内能看到，不建议加太多
\Adinf\RagnarSDK\RagnarSDK::setMeta(123, "", array("extrakey" => "extraval"));

\Adinf\RagnarSDK\RagnarSDK::RecordLog(\Adinf\RagnarSDK\RagnarConst::LOG_TYPE_INFO, __FILE__, __LINE__, "module1_msg", "i wish i can fly!");
\Adinf\RagnarSDK\RagnarSDK::RecordLog(\Adinf\RagnarSDK\RagnarConst::LOG_TYPE_DEBUG, __FILE__, __LINE__, "module2_msg", "i wish i'm rich!");

$digpooint = \Adinf\RagnarSDK\RagnarSDK::digLogStart(__FILE__, __LINE__, "ragnar_test");
\Adinf\RagnarSDK\RagnarSDK::digLogEnd($digpooint, "happy");

$a = \Adinf\RagnarSDK\RagnarSDK::getChildCallParam();

//url 内包含变量替换注册函数演示
$url = "http://dev.weibo.c1om/v1/log/12312312/lists.json?a=1";

$filterURL = function ($url, $hashquery) {
    if (trim($url) == "") {
        return "";
    }
    if (stripos($url, 'http') !== 0) {
        $url = "http://" . $url;
    }

    $urlinfo = parse_url($url);

    if (!$urlinfo) {
        return $url . "#PARSERERROR";
    }

    if (!isset($urlinfo["scheme"])) {
        $urlinfo["scheme"] = "http";
    }

    if (!isset($urlinfo["path"])) {
        $urlinfo["path"] = "/";
    }

    if (!isset($urlinfo["query"])) {
        $urlinfo["query"] = "";
    }

    if (isset($urlinfo["host"]) && ($urlinfo["host"] == "dev.weibo.com" || $urlinfo["host"] == "biz.weibo.com")) {
        $pathinfo = explode("/", $urlinfo["path"]);
        if (count($pathinfo) == 5) {
            $pathinfo[3] = "filted";//统一更换成固定字符
            $pathinfo = implode("/", $pathinfo);
            $url = $urlinfo["scheme"] . "://" . $urlinfo["host"] . $pathinfo;
            if ($hashquery) {
                $url .= "?" . $urlinfo["query"];
            }

            return $url;
        }
    }

    if (isset($urlinfo["host"]) && $urlinfo["host"] == "10.1.1.1") {
        if (stripos($urlinfo["path"], "/mid=") === 0) {
            $mid = substr($urlinfo["path"], 6);
            $urlinfo["path"] = "/mid/";
            $urlinfo["query"] = "mid=" . $mid;
        }
    }

    if ($hashquery) {
        return $urlinfo["scheme"] . "://" . $urlinfo["host"] . $urlinfo["path"] . "?" . $urlinfo["query"];
    } else {
        return $urlinfo["scheme"] . "://" . $urlinfo["host"] . $urlinfo["path"];
    }
};

\Adinf\RagnarSDK\RagnarSDK::setUrlFilterCallback($filterURL);
var_dump(\Adinf\RagnarSDK\RagnarSDK::getTraceID());
var_dump(\Adinf\RagnarSDK\RagnarSDK::decodeTraceID(\Adinf\RagnarSDK\RagnarSDK::getTraceID()));
