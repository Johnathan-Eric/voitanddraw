<?php

	class Making {  
		protected $uniacid; 
		protected $making_tmp; 


		public function making_do($uniacid,$making_tmp){

	        $_W['page']['title'] = '一键制作模板';

	        $item = pdo_fetch("SELECT uniacid FROM ".tablename('sudu8_page_base') ." WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
	        

            if($making_tmp != -1){

                //建立顶级图片栏目
                $cateTopPic = pdo_fetch("SELECT uniacid,id FROM ".tablename('sudu8_page_cate') ." WHERE uniacid = :uniacid AND type='showPic' AND cid=0 ", array(':uniacid' => $uniacid));
                $dataCateTopPic = array(
                'uniacid' => $uniacid,
                'num' => 9,
                'type' => 'showPic',
                'statue' => 1,
                'cid' => 0,
                'name' => '客照欣赏',
                'ename' => 'Photo Cases',
                'name_n' => '客照欣赏',
                'show_i' => 1,
                'list_tstyle' => 2,
                'list_tstylel' => 0,
                'list_type' => 1,
                'list_style' => 2,
                'list_stylet' => 'tcb',
                'pic_page_btn'=>0,
                );
                if (empty($cateTopPic['uniacid'])) {
                    pdo_insert('sudu8_page_cate', $dataCateTopPic);
                } else {
                    pdo_update('sudu8_page_cate', $dataCateTopPic ,array('uniacid' => $uniacid,'id'=>$cateTopPic['id']));
                }
                $dataCateTopPic = pdo_fetch("SELECT uniacid,id FROM ".tablename('sudu8_page_cate') ." WHERE uniacid = :uniacid AND type='showPic' AND cid=0", array(':uniacid' => $uniacid));
                $dataCateTopPicId = $dataCateTopPic['id'];

                $catePicSon = pdo_fetchall("SELECT uniacid FROM ".tablename('sudu8_page_cate')." WHERE uniacid = :uniacid and type='showPic' and cid = :cid ", array(':uniacid' => $uniacid,':cid' => $cateTopPic['id']));
                $catePicSonNum = count($catePicSon);

                //建立子级图片栏目
                if($catePicSonNum == 0){
                    $dataCatePicS1 = array(
                        'uniacid' => $uniacid,
                        'num' => 9,
                        'type' => 'showPic',
                        'statue' => 1,
                        'cid' => $dataCateTopPicId,
                        'name' => '婚纱摄影',
                        'ename' => 'Wedding photography',
                        'name_n' => '婚纱摄影',
                        'show_i' => 1,
                        'catepic'=>'images/template/hunsha/nav/1.png',
                        'list_tstyle' => 2,
                        'list_tstylel' => 0,
                        'list_type' => 1,
                        'list_style' => 2,
                        'list_stylet' => 'tcb',
                        'pic_page_btn'=>0,
                    );
                    $dataCatePicS2 = array(
                        'uniacid' => $uniacid,
                        'num' => 9,
                        'type' => 'showPic',
                        'statue' => 1,
                        'cid' => $dataCateTopPicId,
                        'name' => '全球旅拍',
                        'ename' => 'Global tour',
                        'name_n' => '全球旅拍',
                        'show_i' => 1,
                        'catepic'=>'images/template/hunsha/nav/2.png',
                        'list_tstyle' => 2,
                        'list_tstylel' => 0,
                        'list_type' => 1,
                        'list_style' => 2,
                        'list_stylet' => 'tcb',
                        'pic_page_btn'=>0,
                    );
                    $dataCatePicS3 = array(
                        'uniacid' => $uniacid,
                        'num' => 9,
                        'type' => 'showPic',
                        'statue' => 1,
                        'cid' => $dataCateTopPicId,
                        'name' => '个人写真',
                        'ename' => 'Personal photo',
                        'name_n' => '个人写真',
                        'show_i' => 1,
                        'catepic'=>'images/template/hunsha/nav/3.png',
                        'list_tstyle' => 2,
                        'list_tstylel' => 0,
                        'list_type' => 1,
                        'list_style' => 2,
                        'list_stylet' => 'tcb',
                        'pic_page_btn'=>0,
                    );
                    $dataCatePicS4 = array(
                        'uniacid' => $uniacid,
                        'num' => 9,
                        'type' => 'showPic',
                        'statue' => 1,
                        'cid' => $dataCateTopPicId,
                        'name' => '情侣闺蜜',
                        'ename' => 'Lovers & girlfriends',
                        'name_n' => '情侣闺蜜',
                        'show_i' => 1,
                        'catepic'=>'images/template/hunsha/nav/4.png',
                        'list_tstyle' => 2,
                        'list_tstylel' => 0,
                        'list_type' => 1,
                        'list_style' => 2,
                        'list_stylet' => 'tcb',
                        'pic_page_btn'=>0,
                    );
                    pdo_insert('sudu8_page_cate', $dataCatePicS1);
                    pdo_insert('sudu8_page_cate', $dataCatePicS2);
                    pdo_insert('sudu8_page_cate', $dataCatePicS3);
                    pdo_insert('sudu8_page_cate', $dataCatePicS4);
                    $dataCatePicSAll = pdo_fetchAll("SELECT uniacid,id FROM ".tablename('sudu8_page_cate') ." WHERE uniacid = :uniacid AND type='showPic' AND cid!=0", array(':uniacid' => $uniacid));
                    $dataCatePicS1Id = $dataCatePicSAll[0]['id'];
                    $dataCatePicS2Id = $dataCatePicSAll[1]['id'];
                    $dataCatePicS3Id = $dataCatePicSAll[2]['id'];
                    $dataCatePicS4Id = $dataCatePicSAll[3]['id'];
                }else{
                    $dataCatePicSAll = pdo_fetchAll("SELECT uniacid,id FROM ".tablename('sudu8_page_cate') ." WHERE uniacid = :uniacid AND type='showPic' AND cid!=0", array(':uniacid' => $uniacid));
                    $dataCatePicS1Id = $dataCatePicSAll[0]['id'];
                    $dataCatePicS2Id = $dataCatePicSAll[1]['id'];
                    $dataCatePicS3Id = $dataCatePicSAll[2]['id'];
                    $dataCatePicS4Id = $dataCatePicSAll[3]['id'];
                }

                //建立导航
                $dataNavCon = pdo_fetch("SELECT * FROM ".tablename('sudu8_page_nav') ." WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
                $dataNav = array(
                    'url'=>$dataCatePicS1Id.",".$dataCatePicS2Id.",".$dataCatePicS3Id.",".$dataCatePicS4Id,
                    'uniacid' => $uniacid,
                    'statue'=>1,
                    'name_s'=>1,
                    'box_p_tb'=>3,
                    'box_p_lr'=>1,
                    'number'=>4,
                    'img_size'=>60,
                    'title_position'=>1,
                    'title_color'=>'#222',
                );
                if (empty($dataNavCon['uniacid'])) {
                    pdo_insert('sudu8_page_nav', $dataNav);
                } else {
                    pdo_update('sudu8_page_nav', $dataNav ,array('uniacid' => $uniacid));
                }

                //建立顶级文章栏目
                $cateTopArt = pdo_fetch("SELECT uniacid,id FROM ".tablename('sudu8_page_cate') ." WHERE uniacid = :uniacid AND type='showArt' AND cid=0", array(':uniacid' => $uniacid));
                $dataCateTopArt = array(
                'uniacid' => $uniacid,
                'num' => 8,
                'type' => 'showArt',
                'statue' => 1,
                'cid' => 0,
                'name' => '优惠活动',
                'ename' => 'Activities',
                'name_n' => '优惠活动',
                'show_i' => 1,
                'list_tstyle' => 2,
                'list_tstylel' => 0,
                'list_type' => 1,
                'list_style' => 1,
                'list_stylet' => 'tlb',
                );
                if (empty($cateTopArt['uniacid'])) {
                    pdo_insert('sudu8_page_cate', $dataCateTopArt);
                } else {
                    pdo_update('sudu8_page_cate', $dataCateTopArt ,array('uniacid' => $uniacid,'id'=>$cateTopArt['id']));
                }
                $dataCateTopArt = pdo_fetch("SELECT uniacid,id FROM ".tablename('sudu8_page_cate') ." WHERE uniacid = :uniacid AND type='showArt' AND cid=0", array(':uniacid' => $uniacid));
                $dataCateTopArtId = $dataCateTopArt['id'];

                //建立顶级商品栏目
                $cateTopPro = pdo_fetch("SELECT uniacid,id FROM ".tablename('sudu8_page_cate') ." WHERE uniacid = :uniacid AND type='showPro' AND cid=0", array(':uniacid' => $uniacid));
                $dataCateTopPro = array(
                'uniacid' => $uniacid,
                'num' => 5,
                'type' => 'showPro',
                'statue' => 1,
                'cid' => 0,
                'name' => '服务展示',
                'ename' => 'Service display',
                'name_n' => '服务展示',
                'show_i' => 1,
                'list_tstyle' => 2,
                'list_tstylel' => 0,
                'list_type' => 1,
                'list_style' => 12,
                'list_stylet' => 'none',
                );
                if (empty($cateTopPro['uniacid'])) {
                    pdo_insert('sudu8_page_cate', $dataCateTopPro);
                } else {
                    pdo_update('sudu8_page_cate', $dataCateTopPro ,array('uniacid' => $uniacid,'id'=>$cateTopPro['id']));
                }
                $dataCateTopPro = pdo_fetch("SELECT uniacid,id FROM ".tablename('sudu8_page_cate') ." WHERE uniacid = :uniacid AND type='showPro' AND cid=0", array(':uniacid' => $uniacid));
                $dataCateTopProId = $dataCateTopPro['id']; //顶级商品栏目的ID


                //添加组图
                $catePicCon = pdo_fetchall("SELECT uniacid FROM ".tablename('sudu8_page_products')." WHERE uniacid = :uniacid and type='showPic' and pcid = :pcid ", array(':uniacid' => $uniacid,':pcid' => $cateTopPic['id']));
                $catePicConNum = count($catePicCon);
                if($catePicConNum == 0){
                    $dataPicCon1 = array(
                        'uniacid' => $uniacid,
                        'cid'=> $dataCatePicS1Id,
                        'pcid'=> $cateTopPic['id'],
                        'type_x'=> 1,
                        'type'=> 'showPic',
                        'type_i'=> 1,
                        'title'=> '婚纱摄影欣赏',
                        'thumb'=> 'images/template/hunsha/case/3.jpg',
                        'text'=> 'a:4:{i:0;s:37:"images/template/hunsha/case_con/1.jpg";i:1;s:37:"images/template/hunsha/case_con/2.jpg";i:2;s:37:"images/template/hunsha/case_con/3.jpg";i:3;s:37:"images/template/hunsha/case_con/5.jpg";}',
                    );
                    $dataPicCon2 = array(
                        'uniacid' => $uniacid,
                        'cid'=>$dataCatePicS1Id,
                        'pcid'=> $cateTopPic['id'],
                        'type_x'=>1,
                        'type'=> 'showPic',
                        'type_i'=>1,
                        'title'=>'海南岛之旅',
                        'thumb'=>'images/template/hunsha/case/4.jpg',
                        'text'=>'a:4:{i:0;s:37:"images/template/hunsha/case_con/1.jpg";i:1;s:37:"images/template/hunsha/case_con/2.jpg";i:2;s:37:"images/template/hunsha/case_con/3.jpg";i:3;s:37:"images/template/hunsha/case_con/5.jpg";}',
                    );
                    $dataPicCon3 = array(
                        'uniacid' => $uniacid,
                        'cid'=>$dataCatePicS1Id,
                        'pcid'=> $cateTopPic['id'],
                        'type_x'=>1,
                        'type'=> 'showPic',
                        'type_i'=>1,
                        'title'=>'梦幻仙境',
                        'thumb'=>'images/template/hunsha/case/8.jpg',
                        'text'=>'a:4:{i:0;s:37:"images/template/hunsha/case_con/1.jpg";i:1;s:37:"images/template/hunsha/case_con/2.jpg";i:2;s:37:"images/template/hunsha/case_con/3.jpg";i:3;s:37:"images/template/hunsha/case_con/5.jpg";}',
                    );
                    $dataPicCon4 = array(
                        'uniacid' => $uniacid,
                        'cid'=>$dataCatePicS1Id,
                        'pcid'=> $cateTopPic['id'],
                        'type_x'=>1,
                        'type'=> 'showPic',
                        'type_i'=>1,
                        'title'=>'夕阳的艳丽',
                        'thumb'=>'images/template/hunsha/case/7.jpg',
                        'text'=>'a:4:{i:0;s:37:"images/template/hunsha/case_con/1.jpg";i:1;s:37:"images/template/hunsha/case_con/2.jpg";i:2;s:37:"images/template/hunsha/case_con/3.jpg";i:3;s:37:"images/template/hunsha/case_con/5.jpg";}',
                    );
                    pdo_insert('sudu8_page_products', $dataPicCon1);
                    pdo_insert('sudu8_page_products', $dataPicCon2);
                    pdo_insert('sudu8_page_products', $dataPicCon3);
                    pdo_insert('sudu8_page_products', $dataPicCon4);
                }

                //添加文章
                $cateArtCon = pdo_fetchall("SELECT uniacid FROM ".tablename('sudu8_page_products')." WHERE uniacid = :uniacid and type='showArt' and pcid = :pcid ", array(':uniacid' => $uniacid,':pcid' => $dataCateTopArtId));
                $cateArtConNum = count($cateArtCon);
                if($cateArtConNum == 0){
                    $dataArtCon1 = array(
                        'uniacid' => $uniacid,
                        'cid'=> $dataCateTopArtId,
                        'pcid'=> $dataCateTopArtId,
                        'type'=> 'showArt',
                        'type_i'=> 1,
                        'title'=> '为爱再造一座城',
                        'thumb'=> 'images/template/hunsha/art/a1.jpg',
                        'text'=> '这里是文章内容',
                    );
                    $dataArtCon2 = array(
                        'uniacid' => $uniacid,
                        'cid'=> $dataCateTopArtId,
                        'pcid'=> $dataCateTopArtId,
                        'type'=> 'showArt',
                        'type_i'=> 1,
                        'title'=> '恰好遇见你',
                        'thumb'=> 'images/template/hunsha/art/a2.jpg',
                        'text'=> '这里是文章内容',
                    );
                    $dataArtCon3 = array(
                        'uniacid' => $uniacid,
                        'cid'=> $dataCateTopArtId,
                        'pcid'=> $dataCateTopArtId,
                        'type'=> 'showArt',
                        'type_i'=> 0,
                        'title'=> '月夜香奈儿活动',
                        'thumb'=> 'images/template/hunsha/art/a3.jpg',
                        'text'=> '这里是文章内容',
                    );
                    pdo_insert('sudu8_page_products', $dataArtCon1);
                    pdo_insert('sudu8_page_products', $dataArtCon2);
                    pdo_insert('sudu8_page_products', $dataArtCon3);
                }

                //添加商品
                $cateProCon = pdo_fetchall("SELECT uniacid FROM ".tablename('sudu8_page_products')." WHERE uniacid = :uniacid and type='showPro' and pcid = :pcid ", array(':uniacid' => $uniacid,':pcid' => $dataCateTopProId));
                $cateProConNum = count($cateProCon);
                if($cateProConNum == 0){
                    $dataProCon1 = array(
                        'uniacid' => $uniacid,
                        'cid'=> $dataCateTopProId,
                        'pcid'=> $dataCateTopProId,
                        'type'=> 'showPro',
                        'type_i'=> 1,
                        'labels'=>'',
                        'title'=> '情侣写真',
                        'price'=>199,
                        'market_price'=>999,
                        'sale_num'=>18,
                        'thumb'=> 'images/template/hunsha/case/c6.jpg',
                        'text'=> 'a:1:{i:0;s:34:"images/template/hunsha/case/c6.jpg";}',
                        'product_txt'=>'这里是商品的详细介绍，可放图文',
                    );
                    $dataProCon2 = array(
                        'uniacid' => $uniacid,
                        'cid'=> $dataCateTopProId,
                        'pcid'=> $dataCateTopProId,
                        'type'=> 'showPro',
                        'type_i'=> 1,
                        'labels'=>'',
                        'title'=> '婚纱套系',
                        'price'=>2999,
                        'market_price'=>5999,
                        'sale_num'=>22,
                        'thumb'=> 'images/template/hunsha/case/c1.jpg',
                        'text'=> 'a:1:{i:0;s:34:"images/template/hunsha/case/c1.jpg";}',
                        'product_txt'=>'这里是商品的详细介绍，可放图文',
                    );
                    pdo_insert('sudu8_page_products', $dataProCon1);
                    pdo_insert('sudu8_page_products', $dataProCon2);
                }
                $dataAbout = array(
                    'uniacid' => $uniacid,
                    'header'=>1,
                    'tel_box'=>1,
                    'serv_box'=>1,
                    'content'=>'<p>这里是介绍内容</p><p><br/></p><p>这里是介绍内容</p><p><br/></p><p>这里是介绍内容</p>',
                );
                $about = pdo_fetch("SELECT uniacid FROM ".tablename('sudu8_page_about') ." WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
                if (empty($about['uniacid'])) {
                    pdo_insert('sudu8_page_about', $dataAbout);
                } else {
                    pdo_update('sudu8_page_about', $dataAbout ,array('uniacid' => $uniacid));
                }
                $dataForms = array(
                    'uniacid' => $uniacid,
                    'forms_style'=> 2,
                    'forms_inps'=> 0,
                    'forms_head'=> 'none',
                    'forms_name'=> '自助预约',
                    'forms_ename'=> 'Self Booking',
                    'forms_title_s'=> 'title1',
                    'subtime'=> 0,
                    'forms_btn'=> '立即预约',
                    'success'=> '您已预约成功！',
                    'name'=> '姓名',
                    'name_must'=> 1,
                    'tel'=> '电话',
                    'tel_use'=> 1,
                    'tel_must'=> 1,
                    'date'=> '预约时间',
                    'date_use'=> 1,
                    'checkbox_n'=> '拍摄类型',
                    'checkbox_num'=> 2,
                    'checkbox_use'=> 1,
                    'checkbox_v'=> '婚纱摄影,亲子儿童,个人写真,情侣闺蜜',
                    'content_n'=> '备注',
                    'content_use'=> 1,
                    'wechat_use'=> 0,
                    'address_use'=> 0,
                    'date_must'=> 0,
                    'single_use'=> 0,
                    'checkbox_must'=> 0,
                    'content_must'=> 0,
                );
                $froms = pdo_fetch("SELECT uniacid FROM ".tablename('sudu8_page_forms_config') ." WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
                if (empty($froms['uniacid'])) {
                    pdo_insert('sudu8_page_forms_config', $dataForms);
                } else {
                    pdo_update('sudu8_page_forms_config', $dataForms ,array('uniacid' => $uniacid));
                }


                if($making_tmp == 0){
                    //建立基础信息
                    $dataBase = array(
                        'uniacid' => $uniacid,
                        'index_style' => 'slide',
                        'slide' =>'a:3:{i:0;s:35:"images/template/hunsha/banner/1.jpg";i:1;s:35:"images/template/hunsha/banner/2.jpg";i:2;s:35:"images/template/hunsha/banner/3.jpg";}',
                        'name' => '通用门店模版',
                        'desc'=>'捷安特GCW、禧玛诺高级维修中心',
                        'about' => '捷安特，全称台湾巨大机械工业股份有限公司，是全球自行车生产及行销最具规模的公司之一，其网络横跨五大洲，五十余个国家，公司遍布中国大陆、美国、英国、德国、法国、日本、加拿大、荷兰等地，掌握着超过1万个销售通路。',
                        'address' => '北京市东城区三环路888号',
                        'time' => '9:00 - 18:00',
                        'tel' => '18669868123',
                        'longitude' => '116.321697',
                        'latitude' => '39.894197',
                        'aboutCN' => '公司介绍',
                        'aboutCNen' => 'About Company',
                        'index_about_title' => 'title1',
                        'banner'=>'images/template/hunsha/common/bg.jpg',
                        'logo'=>'images/template/hunsha/common/logo.jpg',
                        'base_color'=> '#389bde',
                        'base_tcolor'=> '#ffffff',
                        'base_color2'=> '#ffcf3d',
                        'base_color_t'=> '#ffcf3d',
                        'index_style'=> 'header',
                        'tel_box'=> '1',
                        'index_about_title'=> 'title2',
                        'catename_x'=> '最新客照',
                        'catenameen_x'=> 'New Photos',
                        'i_b_x_ts'=>'9',
                        'i_b_x_iw'=> '560',
                        'i_b_y_ts'=> '9',
                        'c_b_bg'=> 'images/template/hunsha/common/bg_s.jpg',
                        'c_b_btn'=> '0',
                        'form_index'=> '0',
                        'c_title'=> '1',
                        'tabbar'=>'a:5:{i:0;s:179:"a:4:{s:8:"tabbar_l";s:5:"index";s:8:"tabbar_t";s:6:"首页";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/1.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/1.png";}";i:1;s:186:"a:4:{s:8:"tabbar_l";s:5:"about";s:8:"tabbar_t";s:12:"公司介绍";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/2.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/2.png";}";i:2;s:201:"a:5:{s:8:"tabbar_l";s:1:"7";s:8:"tabbar_t";s:12:"产品中心";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/3.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/3.png";s:4:"type";s:1:"7";}";i:3;s:201:"a:5:{s:8:"tabbar_l";s:1:"7";s:8:"tabbar_t";s:12:"最新活动";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/4.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/4.png";s:4:"type";s:1:"7";}";i:4;s:192:"a:4:{s:8:"tabbar_l";s:10:"usercenter";s:8:"tabbar_t";s:12:"个人中心";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/6.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/6.png";}";}',
                    );
                    if (empty($item['uniacid'])) {
                        pdo_insert('sudu8_page_base', $dataBase);
                    } else {
                        pdo_update('sudu8_page_base', $dataBase ,array('uniacid' => $uniacid));
                    }
                    $dataNav = array(
                        'uniacid' => $uniacid,
                        'statue'=>0,
                    );
                    pdo_update('sudu8_page_nav', $dataNav ,array('uniacid' => $uniacid));
                }elseif ($making_tmp == 1){
                    //婚纱摄影版
                    $dataBase = array(
                        'uniacid' => $uniacid,
                        'index_style' => 'slide',
                        'slide' =>'a:3:{i:0;s:35:"images/template/hunsha/banner/1.jpg";i:1;s:35:"images/template/hunsha/banner/2.jpg";i:2;s:35:"images/template/hunsha/banner/3.jpg";}',
                        'name' => '婚纱摄影演示版',
                        'desc'=>'中国新派婚纱摄影典范',
                        'about' => '婚纱摄影是为客户量身打造，服务，品质，销售于一体的婚纱摄影。摄影为讲究品牌、注重品质的顾客，精心打造世界一流的婚纱照，使顾客充分享受时尚、专业、舒适的拍摄过程。',
                        'address' => '北京市东城区三环路888号',
                        'time' => '9:00 - 18:00',
                        'tel' => '18669868123',
                        'longitude' => '116.321697',
                        'latitude' => '39.894197',
                        'aboutCN' => '公司介绍',
                        'aboutCNen' => 'About Company',
                        'index_about_title' => '9',
                        'banner'=>'images/template/hunsha/common/bg.jpg',
                        'logo'=>'images/template/hunsha/common/logo.jpg',
                        'base_color'=> '#eb75cc',
                        'base_tcolor'=> '#ffffff',
                        'base_color2'=> '#ff007b',
                        'base_color_t'=> '#ffcf3d',
                        'index_style'=> 'slide',
                        'tel_box'=> '0',
                        'index_about_title'=> 'title2',
                        'catename_x'=> '最新客照',
                        'catenameen_x'=> 'New Photos',
                        'i_b_x_ts'=>'2',
                        'i_b_x_iw'=> '560',
                        'i_b_y_ts'=> '9',
                        'c_b_bg'=> 'images/template/hunsha/common/bg_s.jpg',
                        'c_b_btn'=> '1',
                        'form_index'=> '0',
                        'c_title'=> '1',
                        'tabbar'=>'a:5:{i:0;s:179:"a:4:{s:8:"tabbar_l";s:5:"index";s:8:"tabbar_t";s:6:"首页";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/1.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/1.png";}";i:1;s:186:"a:4:{s:8:"tabbar_l";s:5:"about";s:8:"tabbar_t";s:12:"关于我们";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/2.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/2.png";}";i:2;s:201:"a:5:{s:8:"tabbar_l";s:1:"7";s:8:"tabbar_t";s:12:"客照欣赏";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/3.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/3.png";s:4:"type";s:1:"7";}";i:3;s:201:"a:5:{s:8:"tabbar_l";s:1:"7";s:8:"tabbar_t";s:12:"最新活动";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/4.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/4.png";s:4:"type";s:1:"7";}";i:4;s:192:"a:4:{s:8:"tabbar_l";s:10:"usercenter";s:8:"tabbar_t";s:12:"个人中心";s:9:"tabbar_p1";s:35:"images/template/hunsha/tabbar/6.png";s:9:"tabbar_p2";s:35:"images/template/hunsha/tabbar/6.png";}";}',
                    );
                    if (empty($item['uniacid'])) {
                        pdo_insert('sudu8_page_base', $dataBase);
                    } else {
                        pdo_update('sudu8_page_base', $dataBase ,array('uniacid' => $uniacid));
                    }
                }
            }


            return 1;
            //message('一键制作成功!', $this->createWebUrl('copyright', array('op'=>'making')), 'success');
	        

		}   

	}
