<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/4/15
 * Time: 11:45
 */

namespace beauty\models;


use yii\base\Model;
use common\components\Ip2Location;


/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $rateEnable
 * @property integer $adEnable
 * @property string $rateTitle
 * @property string $rateConfirm
 * @property string $rateRefuse
 */
class ConfigInfo extends Model
{
    public $id;
    public $rateEnable;
    public $adEnable;
    public $rateTitle;
    public $rateConfirm;
    public $rateRefuse;

    private static $configInfo = [
        [
            'id' => 101,
            'appType' => 'unknown',
            'showBeauty' => 1,
            'func' => array(),
			'ad' => [
				'enable'=>0,
				'qqappid'=>'1105333924',
				'qqposid'=>'5030015066168155',
				'qqnum'=>10,
				'google'=>[
					'indexBannerEnable'=>0,
					'indexBannerId'=>'ca-app-pub-2992367458107598/7867984670',
				],
				'ads'=>[
					[
						'banner'=>'http://in.3b2o.com/img/show/sid/zc731CHo5Ov/w/1280/h/720/t/1/show.jpg',
						'logo'=>'http://in.3b2o.com/img/show/sid/zc731CHo5Ov/w/128/h/128/t/1/show.jpg',
						'title'=>'测试广告标题1',
						'description'=>'测试广告描述1',
						'link'=>'http://m.zhiboba.com/',
						'action'=>'行动语',
					],
					[
						'banner'=>'http://in.3b2o.com/img/show/sid/zc731CHo5Ov/w/1280/h/720/t/1/show.jpg',
						'logo'=>'http://in.3b2o.com/img/show/sid/zc731CHo5Ov/w/128/h/128/t/1/show.jpg',
						'title'=>'测试广告标题2',
						'description'=>'测试广告描述2',
						'link'=>'http://m.zhiboba.com/',
						'action'=>'去看看啊',
					],
				],
			],
			'alertAndroid' => [
				'newVersion'=> [
					'enable'=>1,
					'version'=>'1.0.0',
					'message'=>"新版本\n提供\n好好好。",
					'confirm'=>"去市场下载",
					'refuse'=>"暂不更新",
					'apk'=>"com.subo.beauty",
				],
				'alert'=> [
					'code'=>'',
					'delay'=>5,
					'message'=>"给个好评呗\n好好好。",
					'confirm'=>"赏你好评",
					'refuse'=>"拒绝",
					'apk'=>"com.subo.beauty",
				],
	            'outOfService' => [
					'enable'=>0,
					'action'=>[
						'message'=>"本应用已经停止更新，请下载xxxx。\n已经停止使用，请下载最新版本。本版本已经停止使用，请下载最新版本。",
						'confirm' => '去下载',
						'refuse' => '关闭应用',
						'apk'=>"com.subo.beauty",
					],
				],
			],
			'alertIOS' => [
				'alert'=> [
					'code'=>'',
					'delay'=>5,
					'message'=>"给个好评呗\n好好好。",
					'confirm'=>"赏你好评",
					'refuse'=>"拒绝",
					'link'=>"https://itunes.apple.com/cn/app/id1122402098?mt=8&at=1000l8vm",
				],
				'videoAlert'=> [
					'code'=>'',
					'message'=>"给个好评呗\n好好好。",
					'confirm'=>"赏你好评",
					'refuse'=>"拒绝",
					'link'=>"https://itunes.apple.com/cn/app/id1122402098?mt=8&at=1000l8vm",
				],
				'outOfService' => [
					'enable'=>0,
					'message'=>"给个五星好评，客户端将升级为劲爆版。\n提供更多劲爆图片。\n要试试么？",
					'confirmMessage' => '去给好评',
					'refuseMessage' => '不想要',
					'link'=>'https://itunes.apple.com/cn/app/id1128648541?mt=8&at=1000l8vm',
				],

			],
            'rateEnable' => 0,
            'rateTitle' => '给个好评吧！标题要长长长长长！还要长长长长长长长！',
            'rateConfirm' => '赏你好评',
            'rateRefuse' => '残忍拒绝',
            'outOfService' => [
				'enable'=>0,
				'action'=>[
					'message'=>"给个五星好评，客户端将升级为劲爆版。\n提供更多劲爆图片。\n要试试么？",
					'confirmMessage' => '去给好评',
					'refuseMessage' => '不想要',
					'link'=>'https://itunes.apple.com/cn/app/id1128648541?mt=8&at=1000l8vm',
				],
			],
            'myFuncion' => [
				'enableVideo'=>1,
				'enablePhoto'=>1,
				'enableJoke'=>1,
			],

		],
    ];
	
	/*
	 * 配置项，iOS根据客户端不同，返回参数可能不一样
	 * 安卓一般只有两种
	 */
    public static function getConfigInfo() {
		$app = MobileApp::findByUA();
		$device = MobileDevice::findByUA();
		$minfo = self::getMobileInfo();
		$config = self::$configInfo;
		$config[0]['appType'] = $app->getCustomParam('appType', 'unknown');
		$config[0]['showBeauty'] = self::getIsShowBeauty();
		$config[0]['func'] = $app->getFunc();
		$config = self::getConfigInfoAd($config);
		if ($minfo['app'] == 'kuaibo') {
			//$config[0]['outOfService']['enable'] = 1;
		}
		//$config[0]['location'] = self::getUserLocation();
		if ($config[0]['showBeauty'] == 2 && !empty($device) && $device->visit_times == 1) {
			$device->addVisit(true);
			$config[0]['outOfService']['enable'] = 1;
			$config[0]['outOfService']['action']['link'] = 'https://itunes.apple.com/cn/app/id' . $app->appleid . '?mt=8&at=1000l8vm';
		}
		if ('android' == $minfo['system'] && !empty($device) && $device->visit_times >= 5 && $device->android_alert == 0) {
			$device->setAndroidAlert();
			$config[0]['rateEnable'] = 1;
			$config[0]['rateTitle'] = "我们需要您的好评，以便提供更多更好的图片！";
		}

        return $config;
    }

	/*
	 * 获取配置广告部分
	 */
    public static function getConfigInfoAd($config) {
		$app = MobileApp::findByUA();
		$device = MobileDevice::findByUA();
		$minfo = self::getMobileInfo();

		$config[0]['ad']['ads'] = array();
		$config[0]['ad']['google']['indexBannerEnable'] = $app->getCustomParam('ad_google_enable', 0);
		$config[0]['ad']['enable'] = $app->getCustomParam('ad_qq_enable', 0);
		$config[0]['ad']['qqappid'] = $app->getCustomParam('ad_qq_id', '');
		$config[0]['ad']['qqposid'] = $app->getCustomParam('ad_qq_flow_id', '');

		$config[0]['ad']['indexFlow'] = array();
		$config[0]['ad']['indexFlow']['enable'] = $app->getCustomParam('ad_qq_enable', 0);
		$config[0]['ad']['indexFlow']['clickAlert'] = 0;
		$config[0]['ad']['indexFlow']['interval'] = 6;
		$config[0]['ad']['indexFlow']['plaminfo']['qq'] = [
			'adnum'=>10,
			'appid'=>$app->getCustomParam('ad_qq_id', ''),
			'posid'=>$app->getCustomParam('ad_qq_flow_id', ''),
		];
		$config[0]['ad']['pageFlow'] = array();
		$config[0]['ad']['pageFlow']['enable'] = $app->getCustomParam('ad_qq_enable', 0);
		$config[0]['ad']['pageFlow']['clickAlert'] = 0;
		$config[0]['ad']['pageFlow']['interval'] = 6;
		$config[0]['ad']['pageFlow']['plaminfo']['qq'] = [
			'adnum'=>10,
			'appid'=>$app->getCustomParam('ad_qq_id', ''),
			'posid'=>$app->getCustomParam('ad_qq_flow_id', ''),
		];
		$config[0]['ad']['exitWindow'] = array();
		$config[0]['ad']['exitWindow']['enable'] = $app->getCustomParam('ad_exit_enable', 0);
		$config[0]['ad']['exitWindow']['plaminfo']['qq'] = [
			'adnum'=>10,
			'appid'=>$app->getCustomParam('ad_qq_id', ''),
			'posid'=>$app->getCustomParam('ad_qq_exit_id', ''),
		];
		$config[0]['ad']['photoBanner'] = array();
		$config[0]['ad']['photoBanner']['enable'] = $app->getCustomParam('ad_photo_banner_enable', 0);
		$config[0]['ad']['photoBanner']['plaminfo']['qq'] = [
			'appid'=>$app->getCustomParam('ad_qq_id', $app->getCustomParam('ad_qq_id', '')),
			'posid'=>$app->getCustomParam('ad_qq_photo_banner_id', $app->getCustomParam('ad_qq_photo_banner_id', '')),
		];
		$config[0]['ad']['videoFlow'] = array();
		$config[0]['ad']['videoFlow']['enable'] = $app->getCustomParam('ad_video_flow_enable', 0);
		$config[0]['ad']['videoFlow']['clickAlert'] = 0;
		$config[0]['ad']['videoFlow']['interval'] = 6;
		$config[0]['ad']['videoFlow']['plaminfo']['qq'] = [
			'adnum'=>10,
			'appid'=>$app->getCustomParam('ad_qq_id', ''),
			'posid'=>$app->getCustomParam('ad_qq_video_flow_id', ''),
		];

		$config[0]['ad']['jokeFlow'] = array();
		$config[0]['ad']['jokeFlow']['enable'] = $app->getCustomParam('ad_joke_flow_enable', 0);
		$config[0]['ad']['jokeFlow']['clickAlert'] = 0;
		$config[0]['ad']['jokeFlow']['interval'] = 6;
		$config[0]['ad']['jokeFlow']['plaminfo']['qq'] = [
			'adnum'=>10,
			'appid'=>$app->getCustomParam('ad_qq_id', ''),
			'posid'=>$app->getCustomParam('ad_qq_joke_flow_id', ''),
		];

		if (empty($config[0]['ad']['photoBanner']['plaminfo']['qq']['appid']) || empty($config[0]['ad']['photoBanner']['plaminfo']['qq']['posid'])) {
			$config[0]['ad']['photoBanner']['enable'] = 0;
		}
		if (empty($config[0]['ad']['exitWindow']['plaminfo']['qq']['appid']) || empty($config[0]['ad']['exitWindow']['plaminfo']['qq']['posid'])) {
			$config[0]['ad']['exitWindow']['enable'] = 0;
		}
		if (empty($config[0]['ad']['indexFlow']['plaminfo']['qq']['appid']) || empty($config[0]['ad']['indexFlow']['plaminfo']['qq']['posid'])) {
			$config[0]['ad']['indexFlow']['enable'] = 0;
		}
		if (empty($config[0]['ad']['pageFlow']['plaminfo']['qq']['appid']) || empty($config[0]['ad']['pageFlow']['plaminfo']['qq']['posid'])) {
			$config[0]['ad']['pageFlow']['enable'] = 0;
		}
		if (empty($config[0]['ad']['qqappid']) || empty($config[0]['ad']['qqposid'])) {
			$config[0]['ad']['enable'] = 0;
		}

		return $config;
	}

    public static function getIsShowBeauty() {
		$minfo = self::getMobileInfo();
		$location = self::getUserLocation();
		if (
			strstr($location, '美国') || 
			0
			) {
			return 0;
		}
		if ('iostest' == $minfo['app']) {
//			return 0;
		}

		if (
			strstr($location, '北京') || 
			strstr($location, '杭州') || 
			strstr($location, '广州') || 
			strstr($location, '上海') || 
			strstr($location, '深圳') || 
			0
			) {
			return 1;
		}
        if ('oppo' == $minfo['channel'] && strstr($location, '东莞')) {
			return 1;
		}
		if ('baidu' == $minfo['channel'] && strstr($location, '福州')) {
			return 1;
		}
		if ('baidu' == $minfo['channel'] && strstr($location, '厦门')) {
			return 1;
		}

		return 2;
	}

    public static function getIsIosInreview() {
		$minfo = self::getMobileInfo();
		if ('zero' == $minfo['app'] && $minfo['appversion'] <= 10101) {
			return 0;
		}
		if ('beauty' == $minfo['app'] && $minfo['appversion'] <= 10100) {
			return 0;
		}
		if ('kuaibo' == $minfo['app'] && $minfo['appversion'] <= 10000) {
			return 0;
		}
		return 1;
	}

    public static function getMobileInfo(){
        $ret = array();
        $ret['app'] = '';
        $ret['system'] = '';
        $ret['systemVersion'] = '';
        $ret['appversion'] = 0;
        $ret['browser'] = '';
        $ret['browserVersion'] = '';
        $ret['userid'] = '';
        $ret['channel'] = 'unknown';
		if (empty($_SERVER['HTTP_USER_AGENT'])) {
			return $ret;
		}
		$ua = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/([\w]+) ([\w]+) v([\d\.]+) /siU', $ua, $m)) {
	        $ret['app'] 		= $m[1];
	        $ret['system'] 		= $m[2];
	    	$versions = explode('.', $m[3]);
	       	if (count($versions) == 3) {
	    		$ret['appversion'] = $versions[0]*10000+$versions[1]*100+$versions[2];
	      	}
        }
        if (preg_match('/mid:([\d\-a-f]+)/si', $ua, $userid)) {
        	$ret['userid'] 	= $userid[1];
        }
        if (preg_match('/channel:([\w]+)/si', $ua, $channel)) {
        	$ret['channel'] 	= strtolower($channel[1]);
        }
        return $ret;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rateEnable'], 'integer'],
            [['rateTitle'], 'string', 'max' => 255],
            [['rateConfirm', 'rateRefuse'], 'string', 'max' => 64],
        ];
    }
    
    /* 
     * 获取ip地址
     * */
    public static function getIpLocation($ip){
        $location = new Ip2Location();
        $locationData = $location->getLocation($ip);
        
        return $locationData['country'] . ' ' . $locationData['area'];
    }
    
    /* 
     * 获取用户地址
     * */
    public static function getUserLocation(){
        $ip = '';
        if(isset($_SERVER['HTTP_X_REAL_IP'])){
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        elseif(isset($_SERVER['REMOTE_ADDR'])){
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return !empty($ip) ? self::getIpLocation($ip) : '';
    }
}
