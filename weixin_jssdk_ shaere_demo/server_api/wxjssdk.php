<?php

/**
 *  微信JSSDK，服务器端， 由客户端请求本接口（带参数（名称url）），本接口返回签名等参数供前端使用。    
 */

header('Access-Control-Allow-Origin:*');

class JSSDK
{
	private $appId;
	private $appSecret;

	public function __construct($appId, $appSecret)
	{
		$this->appId = $appId;
		$this->appSecret = $appSecret;
	}
	public function getSignPackage()
	{
		$jsapiTicket = $this->getJsApiTicket();
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url =$_REQUEST['url'];
		$timestamp = time();
		$nonceStr = $this->createNonceStr();
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);
		$signPackage = array(
			"appId" => $this->appId,
			"nonceStr" => $nonceStr,
			"timestamp" => $timestamp,
			"url" => $url,
			"signature" => $signature,
			"rawString" => $string,
			"jsapiTicket" => $jsapiTicket

		);
		return $signPackage;
	}

	private function createNonceStr($length = 16)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function getJsApiTicket()
	{
		$data = json_decode($this->get_php_file("../server_cache/cache/jsapi_ticket.php") ,true  );
		if ($data['expire_time'] < time()) {
			$accessToken = $this->getAccessToken();
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode( file_get_contents($url) ,true);
			$ticket = $res['ticket'];
			if ($ticket) {
				$data['expire_time'] = time() + 7000;
				$data['jsapi_ticket'] = $ticket;
				$this->set_php_file("../server_cache/cache/jsapi_ticket.php", json_encode($data));
			}
		} else {
			$ticket = $data['jsapi_ticket'];
		}


		return $ticket;
	}
 
	private function getAccessToken()
	{
		$data = json_decode($this->get_php_file("../server_cache/cache/access_token.php"),true)  ;
		if ($data['expire_time'] < time()) {
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode( file_get_contents($url) ,true);
			$access_token = $res['access_token'];
			if ($access_token) {
				$data['expire_time'] = time() + 7000;
				$data['access_token'] = $access_token;
				$this->set_php_file("../server_cache/cache/access_token.php", json_encode($data));
			}
		} else {
			$access_token = $data['access_token'];
		}

		return $access_token;
	}



	private function get_php_file($filename)
	{
		return trim(substr(file_get_contents($filename), 15));
	}

	private function set_php_file($filename, $content)
	{
		file_put_contents($filename, "<?php exit();?>" . $content);
	}
}


/*------php代码中唯一需要修改的  , begin --------------------*/

 $appid  = 'wx49c92e6d3e501a15' ;    //请替换为您的appid
 $secret = 'd4624c36b6795d1d99dcf0547af5443d' ;    //请替换您的app secret

/*------php代码中唯一需要修改的  , end --------------------*/

$wxjssdk = new JSSDK($appid, $secret);
$signPackage =     $wxjssdk->getSignPackage();
$signPackage['url'] = $_REQUEST['url'];
echo json_encode( $signPackage); die;


 