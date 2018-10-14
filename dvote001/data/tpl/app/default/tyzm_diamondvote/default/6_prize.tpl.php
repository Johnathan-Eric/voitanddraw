<?php defined('IN_IA') or exit('Access Denied');?>﻿<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('sider', TEMPLATE_INCLUDEPATH)) : (include template('sider', TEMPLATE_INCLUDEPATH));?>
<div class="divmain10">
  <div class="divcon">
  <?php  if($reply['prizemsg']=="") { ?>
     请至后台编辑活动，设置活动内容！
  <?php  } else { ?>
     <?php  echo $reply['prizemsg'];?>
  <?php  } ?>
  </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav_footer', TEMPLATE_INCLUDEPATH)) : (include template('nav_footer', TEMPLATE_INCLUDEPATH));?>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>