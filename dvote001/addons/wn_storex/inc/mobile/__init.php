<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
wxapp_uniacid();
//从分销员分享的链接进入
if (!empty($_GPC['agentid'])) {
	mload()->model('member');
	member_agent_insert($_GPC['agentid']);
}
