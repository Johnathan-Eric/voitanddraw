<?php 
/**
 * 检测是不是该店铺的新用户
 * @param 店铺id $storeid
 * @return boolean
 */
function member_check_new_user($storeid) {
	global $_W;
	$order = pdo_get('storex_order', array('hotelid' => $storeid, 'openid' => $_W['openid'], 'newuser' => 1), array('id', 'newuser', 'openid'));
	if (!empty($order)) {
		return false;
	}
	return true;
}

function member_get_mode() {
	global $_W;
	$member = pdo_get('storex_member', array('weid' => $_W['uniacid'], 'from_user' => $_W['openid']), array('phone', 'email'));
	$memberinfo = array();
	if (!empty($member) && is_array($member)) {
		foreach ($member as $k => $v) {
			if (!empty($v)) {
				$memberinfo[$k] = $v;
				$memberinfo['type'] = $k;
			}
		}
	}
	return $memberinfo;
}

/**
 * 计算用户密码hash
 * @param string $input 输入字符串
 * @param string $salt 附加字符串
 * @return string
 */
function member_hash($input, $salt) {
	global $_W;
	$input = "{$input}-{$salt}-{$_W['config']['setting']['authkey']}";
	return sha1($input);
}

/**
 * 获取单条用户信息，如果查询参数多于一个字段，则查询满足所有字段的用户
 * PS:密码字段不要加密
 * @param array $member 要查询的用户字段，可以包括  uid, username, password, status
 * @param bool 是否要同时获取状态信息
 * @return array 完整的用户信息
 */
function member_single($member) {
	$sql = "SELECT * FROM " . tablename('storex_member') . " WHERE 1";
	$params = array();
	if (!empty($member['weid'])) {
		$sql .= " AND `weid` = :weid";
		$params[':weid'] = $member['weid'];
	}
	if (!empty($member['from_user'])) {
		$sql .= " AND `from_user` = :from_user";
		$params[':from_user'] = $member['from_user'];
	}
	if (!empty($member['username'])) {
		$sql .= " AND `username` = :username";
		$params[':username'] = $member['username'];
	}
	if (!empty($member['status'])) {
		$sql .= " AND `status` = :status";
		$params[':status'] = intval($member['status']);
	}
	$sql .= " LIMIT 1";
	$record = pdo_fetch($sql, $params);
	if (empty($record)) {
		return false;
	}
	if (!empty($member['password'])) {
		$password = member_hash($member['password'], $record['salt']);
		if ($password != $record['password']) {
			return false;
		}
	}
	return $record;
}

function member_insert($member) {
	global $_W;
	if (!isset($member['userid']) || empty($member['userid'])) {
		load()->model('mc');
		$userinfo = mc_oauth_userinfo();
		$member['userid'] = mc_openid2uid($_W['openid']);
		$mc_members = pdo_get('mc_members', array('uid' => $member['userid']), array('mobile', 'realname', 'uid'));
		if (!empty($mc_members)) {
			$member['realname'] = $mc_members['realname'];
			$member['mobile'] = $mc_members['mobile'];
		} else {
			$member['realname'] = $userinfo['nickname'];
		}
	}
	$member['createtime'] = TIMESTAMP;
	$member['isauto'] = 1;
	$member['status'] = 1;
	pdo_insert('storex_member', $member);
	return pdo_insertid();
}

function member_agent_insert($agentid) {
	global $_W;
	$agent = pdo_get('storex_agent_apply', array('id' => $agentid), array('id', 'openid', 'storeid'));
	if (!empty($agent)) {
		$is_agent = pdo_get('storex_agent_apply', array('storeid' => $agent['storeid'], 'uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
		if ($agent['openid'] != $_W['openid'] && empty($is_agent)) {
			$member = pdo_get('storex_member', array('weid' => $_W['uniacid'], 'from_user' => $_W['openid']), array('id', 'from_user'));
			$member_agent = pdo_get('storex_member_agent', array('storeid' => $agent['storeid'], 'uniacid' => $_W['uniacid'], 'openid' => $_W['openid']), array('id', 'openid', 'agentid'));
			if (empty($member_agent)) {
				$insert = array(
					'uniacid' => $_W['uniacid'],
					'storeid' => $agent['storeid'],
					'openid' => $_W['openid'],
					'agentid' => $agent['id'],
					'time' => TIMESTAMP,
				);
				if (!empty($member)) {
					$insert['memberid'] = $member['id'];
				}
				pdo_insert('storex_member_agent', $insert);
			}
		}
	}
}