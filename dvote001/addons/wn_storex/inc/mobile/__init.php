<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
wxapp_uniacid();
//�ӷ���Ա��������ӽ���
if (!empty($_GPC['agentid'])) {
	mload()->model('member');
	member_agent_insert($_GPC['agentid']);
}
