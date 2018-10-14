<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('image', 'wxapp_code');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'error';

if ($op == 'image') {
	$url = trim($_GPC['url']);
	load()->func('communication');
	$result = ihttp_request($url);
	header("Content-type: image/png");
	echo $result['content'];
}

if ($op == 'wxapp_code') {
	load()->classs('wxapp.account');
	$scene = trim($_GPC['scene']);
	$wxapp_api = WxappAccount::create();
	$result = $wxapp_api->getCodeUnlimit($scene);
	header("Content-type: image/png");
	echo $result;
}