<?php
// +----------------------------------------------------------------------
// | 大集系统日志类
// | @author wscsky<wscsky@qq.com>
// +----------------------------------------------------------------------
namespace Think;
/**
 * 日志处理类
 */
class Log {

    // 日志级别 从上到下，由低到高
    const EMERG     = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT     = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT      = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR       = 'ERR';  // 一般错误: 一般性错误
    const WARN      = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE    = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO      = 'INFO';  // 信息: 程序输出信息
    const DEBUG     = 'DEBUG';  // 调试: 调试信息
    const SQL       = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效

    // 日志信息
    static protected $logs       =  array();

    // 日志存储
    static protected $storage   =   null;
    
    
    /**
     * 记录日志 并且会过滤未经设置的级别
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param boolean $record  是否强制记录
     * @return void
     */
    static function record($message, $level=self::ERR, $record=false) {
    	if($record || false !== strpos(C('LOG_LEVEL'),$level)){
    		self::log('system', $message, $level, $record);
    	}
    }
    
    /**
     * 记录日常日志处理
     * @param string $type 日志类型
     * @param string $message	日志标题内容
     * @param array  $data 日志重要内容
     * @param int $level 日志等级
     */    
    static public function log($type, $message, $data = array(), $level = self::NOTICE) {
    	
    	global $member;
    	if($type == "system" && C("SYSTEM_LOG") === false) return;
    	if(false !== strpos(C('LOG_LEVEL'),$level)){
            // 准备记录日志
            self::$logs[] = array(
                            'uid'         => isset($member->uid) ? $member->uid : 0,
                            'aid'         => isset($member->aid) ? $member->aid : 0,
                            'shanghu_uid' => isset($member->mid) ? $member->mid : 0,
                            'uname'       => isset($member->uname) ? $member->uname : '',
                            'type'        => $type,
                            'message'     => $message,
                            'data'		  => my_json_encode($data),
                            'rank'    	  => $level,    			
                            'location'    => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
                            'referer'     => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                            'ip'    => get_client_ip(),
                            'addtime'   => time(),
            );
    	}

    }
    
    /**
     * 记录商户日常日志处理
     * @param string $type 日志类型
     * @param string $message	日志标题内容
     * @param array  $data 日志重要内容
     * @param int $level 日志等级
     */    
    static public function merlog($type, $message, $data = array(), $level = self::NOTICE) {
    	
    	global $member;
    	if($type == "system" && C("SYSTEM_LOG") === false) return;
    	if(false !== strpos(C('LOG_LEVEL'),$level)){
            // 准备记录日志
            self::$logs[] = array(
                'uid'         => isset($member->uid) ? $member->uid : 0,
                'aid'         => isset($member->aid) ? $member->aid : 0,
                'shanghu_uid' => isset($member->id) ? $member->id : 0,
                'uname'       => isset($member->store_name) ? $member->store_name : '',
                'type'        => $type,
                'message'     => $message,
                'data'		  => get_client_browser('-'),
                'rank'    	  => $level,    			
                'location'    => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
                'referer'     => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                'ip'    => get_client_ip(),
                'addtime'   => time(),
            );
    	}

    }
    
    /**
     * 日志保存
     * @static
     * @access public
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function save($type='',$destination='') {
    	if(empty(self::$logs)) return ;
    	$model = M('logs');
    	$model->addAll(self::$logs);
    	self::$logs = array();
    	return ;
    	
    }
    
    /**
     * 日志直接写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function write($message, $level=self::ERR, $type='', $destination='') {
    	global $member;
    	if($type == "system" && C("SYSTEM_LOG") === false) return;
    	// 准备记录日志
    	$log = array(
    			'uid'         => isset($member->uid) ? $member->uid : 0,
    			'suid'		  => isset($member->suid) ? $member->suid : 0,
                        'shanghu_uid' => isset($member->id) ? $member->id : 0,
    			'user_name'   => isset($member->user_name) ? $member->user_name : '',
    			'user_type'	  => isset($member->user_type) ? $member->user_type : 0,
    			'type'        => trim($type),
    			'message'     => $message,
    			'rank'    	  => $level,
    			'location'    => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
    			'referer'     => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
    			'ip'    => get_client_ip(),
    			'addtime'   => time(),
    	);
    	$model = M('logs');
    	$model->addAll($log);
    }
}