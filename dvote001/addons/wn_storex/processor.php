<?php

/**
 * 万能小店
 *
 * @author WeEngine Team & ewei
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Wn_storexModuleProcessor extends WeModuleProcessor {

	public function respond() {
		global $_W;
		load()->classs('account');
		WeUtility::logging('guanzhu', $this->message);
		$rid = $this->rule;
		WeUtility::logging('guanzhu', $rid);
		file_put_contents(IA_ROOT . '/addons/wn_storex/c.txt', $rid);
		$sql = "SELECT * FROM " . tablename('storex_wxcard_reply') . " WHERE rid = :rid ORDER BY RAND() LIMIT 1";
		$reply = pdo_fetch($sql, array(':rid' => $rid));
		load()->classs('weixin.account');
		include IA_ROOT . '/addons/wn_storex/class/coupon.class.php';
		if (!empty($reply['card_id'])) {
			if (pdo_get('storex_coupon', array('card_id' => $reply['card_id']))) {
				$file = IA_ROOT . '/addons/wn_storex/class/coupon.class.php';
				if (file_exists($file)) {
					include $file;
				}
				$coupon = new WnCoupon();
				if(is_error($coupon)) {
					$this->error($reply, $coupon['message']);
					die;
				}
				$card = $coupon->BuildCardExt($reply['cid']);
				if(is_error($card)) {
					$this->error($reply, $card['message']);
					die;
				}
				$data = array(
					'touser' => $_W['openid'],
					'msgtype' => 'wxcard',
					'wxcard' => array(
						'card_id' => $card['card_id'],
						'card_ext' => $card['card_ext'],
					)
				);
				$acc = WeAccount::create($_W['acid']);
				$status = $acc->sendCustomNotice($data);
				if(is_error($status)) {
					$this->error($reply, $status['message']);
					die;
				}
				if(!empty($reply['success'])) {
					return $this->respText($reply['success']);
					die;
				}
			}
		} else {
			$poster_info = pdo_get('storex_poster', array('uniacid' => $_W['uniacid'], 'rid' => $rid));
			if (!empty($poster_info)) {
				if ($this->message['event'] == 'subscribe') {
					$exist = pdo_getall('qrcode_stat', array('uniacid' => $_W['uniacid'], 'openid' => $this->message['from'], 'scene_str' => $this->message['scene']));
					if (empty($exist)) {
						$reward = iunserializer($poster_info['reward']);
						load()->model('mc');
						$uid = mc_openid2uid($this->message['from']);
						if (!empty($reward['follow'])) {
							$record[] = $uid;
							$record[] = '通过万能小店海报关注';
							$record[] = $this->module;
							foreach ($reward['follow'] as $type => $num) {
								mc_credit_update($uid, $type, $num, $record);
							}
						}
						return $this->respText('关注公众号成功');
					} else {
						return $this->respText('欢迎再次关注本公众号');
					}
				} elseif ($this->message['event'] == 'SCAN') {
					return $this->respText('已经关注公众号');
				} 
				include IA_ROOT . '/addons/wn_storex/model/poster.mod.php';
				$wait = $poster_info['wait'];
				$account_api = WeAccount::create($_W['acid']);
				$notice = array(
					'touser' => $_W['openid'],
					'msgtype' => 'text',
					'text' => array(
						'content' => urlencode($wait)
					)
				);
				if ($poster_info['type'] != 3) {
					$account_api->sendCustomNotice($notice);
				}
				if ($poster_info['type'] == 2) {
					$keyword = $poster_info['keyword'];
					$content = trim($this->message['content']);
					$goodsid = substr($content, strlen($keyword));
					$store_info = pdo_get('storex_bases', array('id' => $poster_info['storeid']), array('store_type'));
					// return $this->respText($store_info['store_type']);
					if ($store_info['store_type'] == 1) {
						$goodsinfo = pdo_get('storex_room', array('recycle' => 2, 'store_base_id' => $poster_info['storeid'], 'is_house !=' => 1, 'status' => 1, 'id' => $goodsid), array('id'));
					} else {
						$goodsinfo = pdo_get('storex_goods', array('recycle' => 2, 'store_base_id' => $poster_info['storeid'], 'status' => 1, 'id' => $goodsid), array('id'));
					}
					if (empty($goodsinfo)) {
						return $this->respText('该商品已隐藏或已删除');
					}
				}
				$url = murl('entry', array('m' => 'wn_storex', 'do' => 'poster', 'op' => 'display', 'id' => $poster_info['id'], 'goodsid' => $goodsid), true, true);
				return $this->respText($url);
			} else {
				$content = trim($this->message['content']);
				$code = substr($content, 2);
				$coupon_record = pdo_get('storex_coupon_record', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'code' => $code), array('couponid', 'id'));
				if (empty($coupon_record)) {
					$message = '未找到该卡券记录';
				} else {
					include IA_ROOT . '/addons/wn_storex/model/activity.mod.php';
					$result = activity_coupon_consume($coupon_record['couponid'], $coupon_record['id'], 0);
					if (is_error($result)) {
						$message = $result['message'];
					} else {
						$message = '卡券核销成功';
					}
				}
			}
		}
		return $this->respText($message);
	}
	
	public function error($reply, $msg) {
		if(empty($reply['error'])) {
			if(empty($msg)) {
				return true;
			}
			return $this->respText($msg);
		} else {
			return $this->respText($reply['error']);
		}
	}
}