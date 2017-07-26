<?php

/*
	轩哥出品
 */
 
 header('Content-type:text');

 define("TOKEN","weixin");
 $wechatObj = new wechatCallbackapiTest();
 if(!isset($_GET['echostr'])){
 	$wechatObj->responseMsg();
 }else{
 	$wechatObj->valid();
 }

 class wechatCallbackapiTest
 {
 	// 验证签名
 	public function valid()
 	{
 		$echoStr = $_GET["echostr"];
 		$signature = $_GET["signature"];
 		$timestamp = $_GET['timestamp'];
 		$nonce = $_GET["nonce"];
 		$token = TOKEN;
 		$tmpArr = array($token, $timestamp, $nonce);
 		sort($tmpArr, SORT_STRING);
 		$tmpStr = implode($tmpArr);
 		$tmpStr = sha1($tmpStr);
 		if($tmpStr == $signature){
 			echo $echoStr;
 			exit;
 		}
 	}

 	// 响应消息
 	public function responseMsg()
 	{
 		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
 		if(!empty($postStr)){
 			$this->logger("R \r\n".$postStr);
 			// 接收原始post数据,class名,以CDATA为文本节点
 			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
 			$RX_TYPE = trim($postObj->Msgtype);

 			// 消息类型分离
 			switch (RX_TYPE) {
				 case "event":        //事件
             	    $result = $this->receiveEvent($postObj);
             	    break;
             	case "text":        //文本
             	    $result = $this->receiveText($postObj);
             	    break;
             	case "image":        //图片
             	    $result = $this->receiveImage($postObj);
             	    break;
             	case "location":    //位置
             	    $result = $this->receiveLocation($postObj);
             	    break;
             	case "voice":        //语音
             	    $result = $this->receiveVoice($postObj);
             	    break;
             	case "video":        //视频
             	case "shortvideo":
             	    $result = $this->receiveVideo($postObj);
             	    break;
             	case "link":        //链接
             	    $result = $this->receiveLink($postObj);
             	    break;
             	default:
             	    $result = "unknown msg type: ".$RX_TYPE;
             	    break;
 			}
 			$this->logger("T \r\n".$result);
 			echo $result;
 		}else{
 			echo "";
 			exit;
 		}
 	}


 	//接收事件消息
 	private function receiveEvent($object)
 	{
 		$content = "";
         switch ($object->Event)
         {
             case "subscribe":
                 $content = "欢迎关注方倍工作室 \n请回复以下关键字：文本 表情 单图文 多图文 音乐\n请按住说话 或 点击 + 再分别发送以下内容：语音 图片 小视频 我的收藏 位置";
                 break;
             case "unsubscribe":
                 $content = "取消关注";
                 break;
             default:
                 $content = "receive a new event: ".$object->Event;
                 break;
         }

         if(is_array($content)){
         	$result = $this->tran
         }
 	}

 	// 日志记录
 	private function logger($log_content)
 	{
 		if(isset($_SERVER['HTTP_APPNAME'])){//SAE
 			 sae_set_display_errors(false);
             sae_debug($log_content);
             sae_set_display_errors(true);
 		}else if ($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){//LOCAL
 			$max_size = 100000;
 			$log_filename = "log.xml";
 			if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){
 				unlink($log_filename);
 			}
 			file_put_contents($log_filename,date('Y-m-d H:i:s')." ".$log_content."\r\n", FILE_APPEND);
 		}
 	}
 }
?>