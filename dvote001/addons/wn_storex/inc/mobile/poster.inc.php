<?php
defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$ops = array('display');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'error';
$uid = mc_openid2uid($_W['openid']);
$storeid = intval($_GPC['id']);
$type = !empty($_GPC['type']) ? intval($_GPC['type']) : 1;
mload()->model('poster');
mload()->model('entry');
mload()->model('clerk');

$user_info = mc_fansinfo($_W['openid']);
if ($op == 'display') {
	$id = intval($_GPC['id']);
	$poster_info = pdo_get('storex_poster', array('id' => $id));
	$params = iunserializer($poster_info['params']);
	$qr_show = false;
	if (!empty($params) && is_array($params)) {
		foreach ($params as $value) {
			if ($value['type'] == 'qr') {
				$qr_show = true;
			}
		}
	}
	if (!empty($qr_show)) {
		if ($poster_info['type'] == 1) {
			$url = murl('entry', array('m' => 'wn_storex', 'do' => 'display'), true, true);
		} elseif ($poster_info['type'] == 2) {
			$goodsid = intval($_GPC['goodsid']);
			if (!empty($goodsid)) {
				$url = goods_entry_fetch($poster_info['storeid'], array('goodsid' => $goodsid));
			}
		} elseif ($poster_info['type'] == 3) {
			$order_p = clerk_permission_storex('order', $poster_info['storeid']);
			$room_p = clerk_permission_storex('room', $poster_info['storeid']);
			load()->model('account');
			$acid = intval($_W['acid']);
			$uniacccount = WeAccount::create($acid);
			if (empty($_W['openid'])) {
				message(error(-1, '场景值错误，生成海报失败'));
			}
			$scene_str = md5('wn_storex_' . $poster_info['id'] . '_' . $poster_info['storeid'] . '_' . $_W['openid']);
			$qrcode = pdo_get('qrcode', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'scene_str' => $scene_str, 'model' => 2), array('ticket', 'id'));
			if (!empty($qrcode)) {
				$ticket = urlencode($qrcode['ticket']);
			} else {
				$barcode = array(
					'expire_seconds' => '',
					'action_name' => '',
					'action_info' => array(
						'scene' => array(),
					),
				);
				$barcode['action_info']['scene']['scene_str'] = $scene_str;
				$barcode['action_info']['scene']['storeid'] = $poster_info['storeid'];
				$barcode['action_info']['scene']['openid'] = $_W['openid'];
				$barcode['action_name'] = 'QR_LIMIT_STR_SCENE';
				$qr_result = $uniacccount->barCodeCreateFixed($barcode);
				if (!is_error($qr_result)) {
					$insert = array(
						'uniacid' => $_W['uniacid'],
						'acid' => $_W['acid'],
						'qrcid' => $barcode['action_info']['scene']['scene_id'],
						'scene_str' => $barcode['action_info']['scene']['scene_str'],
						'keyword' => $poster_info['keyword'],
						'name' => $poster_info['name'],
						'model' => 2,
						'ticket' => $qr_result['ticket'],
						'url' => $qr_result['url'],
						'expire' => $qr_result['expire_seconds'],
						'createtime' => TIMESTAMP,
						'status' => '1',
						'type' => 'scene',
					);
					pdo_insert('qrcode', $insert);
					$ticket = urlencode($qr_result['ticket']);
				} else {
					message(error(-1, '生成海报失败'));
				}
			}
			$url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket;
		}
		if (!empty($params) && is_array($params)) {
			foreach ($params as &$val) {
				if ($val['type'] == 'qr') {
					$val['url'] = $url;
				}
				if ($val['type'] == 'nickname') {
					$val['url'] = $user_info['nickname'];
				}
				if ($val['type'] == 'avatar') {
					$val['url'] = $user_info['headimgurl'];
				}
			}
			unset($val);
		}
	}
	$poster = array(
		'id' => $poster_info['id'],
		'storeid' => $poster_info['storeid'],
		'background' => $poster_info['background'],
		'items' => $params,
		'type' => $poster_info['type'],
	);
}
include $this->template('poster');