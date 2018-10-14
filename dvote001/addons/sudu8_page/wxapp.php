<?php
goto O5CIp;
O5CIp:
defined("IN_IA") or die("Access Denied");
goto hXJua;
hXJua:
define("HTTPSHOST", $_W["attachurl"]);
goto osd2J;
osd2J:
class Sudu8_pageModuleWxapp extends WeModuleWxapp
{
    public function doPageAppbase()
    {
        goto Bmn3U;
        etWkx:
        $array = get_object_vars($jsondecode);
        goto JVp2U;
        fkdr5:
        $appsecret = $app["secret"];
        goto TMcz2;
        uW28e:
        goto a1phH;
        goto XUCJi;
        fJgXr:
        a1phH:
        goto ZkB9n;
        ZkB9n:
        NuIVJ:
        goto mxo6p;
        rE2hX:
        $app = pdo_fetch("SELECT * FROM " . tablename("account_wxapp") . " WHERE uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
        goto PW2nG;
        SaRIR:
        return $this->result(0, "success", $data);
        goto fJgXr;
        PW2nG:
        $appid = $app["key"];
        goto fkdr5;
        u2LvR:
        $xuVXW = !$openid;
        goto de9xI;
        hsUoG:
        return $this->result(0, "success", $data);
        goto uW28e;
        kC4En:
        if ($user["n"] == 0) {
            goto aSlmN;
        }
        goto hsUoG;
        kxagW:
        $data = array("uniacid" => $uniacid, "openid" => $openid, "createtime" => time());
        goto BYk76;
        kusaH:
        $jsondecode = json_decode($weixin);
        goto etWkx;
        XUCJi:
        aSlmN:
        goto Gad_g;
        BGvqX:
        $code = $_GPC["code"];
        goto rE2hX;
        BYk76:
        $user = pdo_fetch("SELECT count(*) as n FROM " . tablename("sudu8_page_user") . " WHERE openid = :id and uniacid = :uniacid", array(":id" => $openid, ":uniacid" => $_W["uniacid"]));
        goto kC4En;
        Gad_g:
        pdo_insert("sudu8_page_user", $data);
        goto SaRIR;
        f35xl:
        $uniacid = $_W["uniacid"];
        goto BGvqX;
        de9xI:
        if ($xuVXW) {
            goto NuIVJ;
        }
        goto kxagW;
        JVp2U:
        $openid = $array["openid"];
        goto u2LvR;
        i6prm:
        $weixin = file_get_contents($url);
        goto kusaH;
        TMcz2:
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $appsecret . "&js_code=" . $code . "&grant_type=authorization_code";
        goto i6prm;
        Bmn3U:
        global $_GPC, $_W;
        goto f35xl;
        mxo6p:
    }
    public function doPageUseupdate()
    {
        goto rTUDG;
        PmM6m:
        var_dump($res);
        goto Ccj2A;
        OKjOR:
        $data = array("uniacid" => $uniacid, "nickname" => $_GPC["nickname"], "avatar" => $_GPC["avatarUrl"], "gender" => $_GPC["gender"], "resideprovince" => $_GPC["province"], "residecity" => $_GPC["city"], "nationality" => $_GPC["country"]);
        goto c9AmR;
        rTUDG:
        global $_GPC, $_W;
        goto Sy3Wy;
        zc9Sq:
        $user = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_user") . " WHERE openid = :id and uniacid = :uniacid", array(":id" => $openid, ":uniacid" => $_W["uniacid"]));
        goto OKjOR;
        c9AmR:
        $res = pdo_update("sudu8_page_user", $data, array("openid" => $openid, "uniacid" => $uniacid));
        goto PmM6m;
        ZP1e_:
        $openid = $_GPC["openid"];
        goto zc9Sq;
        Sy3Wy:
        $uniacid = $_W["uniacid"];
        goto ZP1e_;
        Ccj2A:
    }
    public function doPageBase()
    {
        goto TYj11;
        G__7P:
        if ($z3v1d) {
            goto v0Otu;
        }
        goto vt159;
        k808S:
        $baseInfo["slide"] = unserialize($baseInfo["slide"]);
        goto t1cDp;
        SFc5z:
        VL4uG:
        goto tvLec;
        A5GSS:
        $BKHxY = !$baseInfo["c_b_bg"];
        goto MP3z6;
        TqSDk:
        $baseInfo = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_base") . " WHERE uniacid = :uniacid", array(":uniacid" => $uniacid));
        goto UCiOX;
        WqpP5:
        if ($pExRj) {
            goto h8AD9;
        }
        goto OxWQj;
        ZA_c9:
        h8AD9:
        goto Zf44w;
        t1cDp:
        $num = count($baseInfo["slide"]);
        goto tRU__;
        gU21s:
        NxFQu:
        goto oGq0d;
        xbIs2:
        $v3Z6z = !($cate_type["type"] == "coupon");
        goto ooqWF;
        QAPIJ:
        $video_url = stristr($baseInfo["video"], "http");
        goto D2Zn8;
        kZtqL:
        icctN:
        goto iBXr6;
        ksvDM:
        $baseInfo["tabbar"][$i]["type"] = "list" . substr($cate_type["type"], 4, strlen($cate_type["type"]));
        goto CBBrg;
        ZbDsz:
        CvpEh:
        goto A5GSS;
        I1Cd8:
        $EhvYu = !$baseInfo["v_img"];
        goto MAHtu;
        KVmri:
        $baseInfo["banner"] = HTTPSHOST . $baseInfo["banner"];
        goto tnIhL;
        lcYP6:
        $baseInfo["slide"][$i] = HTTPSHOST . $baseInfo["slide"][$i];
        goto EW3Tk;
        izj4G:
        $baseInfo["c_b_bg"] = HTTPSHOST . $baseInfo["c_b_bg"];
        goto K9rgY;
        ZEQFx:
        $i++;
        goto OkQlc;
        hT8CA:
        goto Ac4lp;
        goto gU21s;
        tvLec:
        wBjDF:
        goto ZEQFx;
        BQ1GF:
        $i++;
        goto xINH3;
        xINH3:
        goto ReuhN;
        goto he5K4;
        OrAKZ:
        $baseInfo["tabbar"][$i]["type"] = "coupon";
        goto KR_vH;
        ISPnS:
        $uniacid = $_W["uniacid"];
        goto TqSDk;
        sE7KP:
        bG7AM:
        goto I1Cd8;
        K8CXS:
        if (!($i < $num)) {
            goto wbia5;
        }
        goto lcYP6;
        CBBrg:
        Ac4lp:
        goto SFc5z;
        vvkzZ:
        $baseInfo["tabbar"][$i] = unserialize($baseInfo["tabbar"][$i]);
        goto WNEBs;
        he5K4:
        wbia5:
        goto o0tBQ;
        OxWQj:
        $baseInfo["copyimg"] = HTTPSHOST . $baseInfo["copyimg"];
        goto ZA_c9;
        ooqWF:
        if ($v3Z6z) {
            goto k9GzO;
        }
        goto OrAKZ;
        UCiOX:
        $baseInfo["ot"] = pdo_fetch("SELECT forms_name FROM " . tablename("sudu8_page_forms_config") . " WHERE uniacid = :uniacid", array(":uniacid" => $uniacid));
        goto On7CX;
        TYj11:
        global $_GPC, $_W;
        goto ISPnS;
        v0q3I:
        u6a0w:
        goto gd3QW;
        R07sx:
        if ($jgKd2) {
            goto az9rc;
        }
        goto I0nqz;
        CQ3gy:
        rf_FP:
        goto ksvDM;
        tRU__:
        $i = 0;
        goto WRYp7;
        XLmHN:
        BHF55:
        goto aM93J;
        YZUu2:
        v0Otu:
        goto sE7KP;
        I0nqz:
        $baseInfo["tabbar"][$i]["type"] = "page";
        goto lIv3U;
        RLTpD:
        if ($cate_type["list_type"] == 1 && $cate_type["type"] != "page" && $cate_type["type"] != "coupon") {
            goto rf_FP;
        }
        goto hT8CA;
        oGq0d:
        $baseInfo["tabbar"][$i]["type"] = "listCate";
        goto MyM2I;
        WtR3n:
        return $this->result(0, "success", $baseInfo);
        goto kJLg5;
        WRYp7:
        ReuhN:
        goto K8CXS;
        tnIhL:
        qVv2O:
        goto k808S;
        gd3QW:
        $yfiC1 = !$baseInfo["banner"];
        goto Op8dV;
        tZYEE:
        $baseInfo["ot"]["forms_name"] = "请配置表单名称";
        goto v0q3I;
        SqN9_:
        if ($TWBAr) {
            goto u6a0w;
        }
        goto tZYEE;
        Zf44w:
        $d5Upw = !$baseInfo["video"];
        goto L_2lZ;
        fbknW:
        $pExRj = !$baseInfo["copyimg"];
        goto WqpP5;
        o0tBQ:
        $baseInfo["logo"] = HTTPSHOST . $baseInfo["logo"];
        goto fbknW;
        vMr3p:
        $i = 0;
        goto XLmHN;
        iBXr6:
        $baseInfo["color_bar"] = "1px solid " . $baseInfo["color_bar"];
        goto WtR3n;
        EW3Tk:
        YI6x4:
        goto BQ1GF;
        K9rgY:
        VD2F2:
        goto xA5Yk;
        A9TJp:
        if ($cate_type["list_type"] == 0 && $cate_type["type"] != "page" && $cate_type["type"] != "coupon") {
            goto NxFQu;
        }
        goto RLTpD;
        wqWcc:
        $jgKd2 = !($cate_type["type"] == "page");
        goto R07sx;
        L_2lZ:
        if ($d5Upw) {
            goto bG7AM;
        }
        goto QAPIJ;
        On7CX:
        $TWBAr = !($baseInfo["ot"]["forms_name"] == '');
        goto SqN9_;
        MAHtu:
        if ($EhvYu) {
            goto CvpEh;
        }
        goto Ipo8y;
        f1vgM:
        $cate_type = pdo_fetch("SELECT id,type,list_type FROM " . tablename("sudu8_page_cate") . " WHERE uniacid = :uniacid and id = :id", array(":uniacid" => $uniacid, ":id" => $baseInfo["tabbar"][$i]["tabbar_l"]));
        goto wqWcc;
        KR_vH:
        k9GzO:
        goto A9TJp;
        xA5Yk:
        $baseInfo["tabbar"] = unserialize($baseInfo["tabbar"]);
        goto vMr3p;
        vt159:
        $baseInfo["video"] = HTTPSHOST . $baseInfo["video"];
        goto YZUu2;
        lIv3U:
        az9rc:
        goto xbIs2;
        Op8dV:
        if ($yfiC1) {
            goto qVv2O;
        }
        goto KVmri;
        aM93J:
        if (!($i <= 4)) {
            goto icctN;
        }
        goto vvkzZ;
        MP3z6:
        if ($BKHxY) {
            goto VD2F2;
        }
        goto izj4G;
        D2Zn8:
        $z3v1d = !empty($video_url);
        goto G__7P;
        cVg29:
        if ($RbEqN) {
            goto VL4uG;
        }
        goto f1vgM;
        WNEBs:
        $RbEqN = !is_numeric($baseInfo["tabbar"][$i]["tabbar_l"]);
        goto cVg29;
        Ipo8y:
        $baseInfo["v_img"] = HTTPSHOST . $baseInfo["v_img"];
        goto ZbDsz;
        MyM2I:
        goto Ac4lp;
        goto CQ3gy;
        OkQlc:
        goto BHF55;
        goto kZtqL;
        kJLg5:
    }
    public function doPageAbout()
    {
        goto PoMPB;
        PoMPB:
        global $_GPC, $_W;
        goto aWhDh;
        aWhDh:
        $uniacid = $_W["uniacid"];
        goto Hdlty;
        Hdlty:
        $aboutInfo = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_about") . " WHERE uniacid = :uniacid", array(":uniacid" => $uniacid));
        goto H2fe7;
        H2fe7:
        return $this->result(0, "success", $aboutInfo);
        goto T1Hx0;
        T1Hx0:
    }
    public function doPageCommon()
    {
        goto IH5SP;
        IH5SP:
        global $_GPC, $_W;
        goto Is920;
        Oq1yL:
        return $this->result(0, "success", $copyright);
        goto q36ST;
        WFkGC:
        $copyright = pdo_fetch("SELECT copyright,tel,tel_b,latitude,longitude,name,address FROM " . tablename("sudu8_page_base") . " WHERE uniacid = :uniacid", array(":uniacid" => $uniacid));
        goto Oq1yL;
        Is920:
        $uniacid = $_W["uniacid"];
        goto WFkGC;
        q36ST:
    }
    public function doPageProducts()
    {
        goto ATllj;
        HsbVD:
        j6RQM:
        goto ldpkL;
        dB8KN:
        $products = pdo_fetchall("SELECT id,type,num,title,thumb,`desc`,buy_type FROM " . tablename("sudu8_page_products") . " WHERE uniacid = :uniacid ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid));
        goto Ziuhd;
        ATllj:
        global $_GPC, $_W;
        goto NqwNo;
        ldpkL:
        return $this->result(0, "success", $products);
        goto jnmcy;
        Ziuhd:
        foreach ($products as &$row) {
            $row["thumb"] = HTTPSHOST . $row["thumb"];
            aKaLr:
        }
        goto HsbVD;
        NqwNo:
        $uniacid = $_W["uniacid"];
        goto dB8KN;
        jnmcy:
    }
    public function doPageProductsList()
    {
        goto PyDwh;
        Lt0E7:
        $ProductsList = pdo_fetchall("SELECT id,type,num,title,thumb,`desc`,buy_type FROM " . tablename("sudu8_page_products") . "WHERE uniacid = :uniacid ORDER BY num DESC,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, array(":uniacid" => $uniacid));
        goto oJnZr;
        QRlp0:
        return $this->result(0, "success", $ProductsList);
        goto DzjEB;
        PyDwh:
        global $_GPC, $_W;
        goto QvgeZ;
        oJnZr:
        foreach ($ProductsList as &$row) {
            $row["thumb"] = HTTPSHOST . $row["thumb"];
            h6KYs:
        }
        goto rlRXD;
        rlRXD:
        H6P28:
        goto QRlp0;
        ZmOz5:
        $pindex = max(1, intval($_GPC["page"]));
        goto GgHK4;
        GgHK4:
        $psize = 10;
        goto Lt0E7;
        QvgeZ:
        $uniacid = $_W["uniacid"];
        goto ZmOz5;
        DzjEB:
    }
    public function doPageProductsDetail()
    {
        goto i9hLL;
        dzAFM:
        $ProductsDetail["video"] = HTTPSHOST . $ProductsDetail["video"];
        goto yHxWQ;
        if_2J:
        $data = array("hits" => $ProductsDetail["hits"] + 1);
        goto pHqWx;
        Y2PUc:
        y9d1I:
        goto hhSsG;
        yHxWQ:
        zfs5q:
        goto PKXMQ;
        PKXMQ:
        GsbD5:
        goto MV4kN;
        obMfv:
        $z3v1d = !empty($video_url);
        goto kf0KW;
        X4hn8:
        $id = intval($_GPC["id"]);
        goto CIAPE;
        hhSsG:
        $ProductsDetail["ctime"] = date("Y-m-d H:i:s", $ProductsDetail["ctime"]);
        goto ZVA23;
        XRfQQ:
        $ProductsDetail["btn"] = pdo_fetch("SELECT pic_page_btn FROM " . tablename("sudu8_page_cate") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $ProductsDetail["cid"], ":uniacid" => $uniacid));
        goto if_2J;
        pHqWx:
        pdo_update("sudu8_page_products", $data, array("id" => $id, "uniacid" => $uniacid));
        goto aSxGJ;
        QE3DM:
        $ProductsDetail["etime"] = date("Y-m-d H:i:s", $ProductsDetail["etime"]);
        goto Y2PUc;
        aSxGJ:
        if ($ProductsDetail["etime"]) {
            goto wtS6r;
        }
        goto ZRMMS;
        BllHJ:
        wtS6r:
        goto QE3DM;
        RQk_f:
        $uniacid = $_W["uniacid"];
        goto X4hn8;
        i9hLL:
        global $_GPC, $_W;
        goto RQk_f;
        k8rqg:
        if ($L2mgL) {
            goto GsbD5;
        }
        goto lQzw7;
        lQzw7:
        $video_url = stristr($ProductsDetail["video"], "http");
        goto obMfv;
        MV4kN:
        return $this->result(0, "success", $ProductsDetail);
        goto T7nMI;
        PLbcn:
        goto y9d1I;
        goto BllHJ;
        ZRMMS:
        $ProductsDetail["etime"] = date("Y-m-d H:i:s", $ProductsDetail["ctime"]);
        goto PLbcn;
        CIAPE:
        $ProductsDetail = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_products") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $uniacid));
        goto XRfQQ;
        kf0KW:
        if ($z3v1d) {
            goto zfs5q;
        }
        goto dzAFM;
        ZVA23:
        $L2mgL = !$ProductsDetail["video"];
        goto k8rqg;
        T7nMI:
    }
    public function doPageFormsConfig()
    {
        goto oXfhl;
        ZqsuC:
        goto bpyve;
        goto bIES2;
        X1Ivk:
        $formsConfig["c2"]["c2num"] = $checkbox_num2;
        goto H3b7Z;
        De4E0:
        if ($checkbox_num2 > 100 or $checkbox_num2 < 20) {
            goto v2bGO;
        }
        goto X1Ivk;
        at5mq:
        ZxffT:
        goto sTHwb;
        tQrwZ:
        lBheG:
        goto b2p1Z;
        TKdK8:
        $formsConfig["checkbox_num"] = 100;
        goto Ljlx5;
        n6TZn:
        Xty0D:
        goto ZmPmX;
        cOK5z:
        $formsConfig["single_num"] = 100;
        goto zUVt5;
        b2p1Z:
        return $this->result(0, "success", $formsConfig);
        goto c2xlp;
        Ljlx5:
        goto Zqd_u;
        goto tLel8;
        xtpL9:
        $formsConfig["con2"] = unserialize($formsConfig["con2"]);
        goto uJ0wI;
        Zkg8A:
        if ($single_num > 100 or $single_num < 20) {
            goto Xty0D;
        }
        goto WAVHt;
        KhKuF:
        Zqd_u:
        goto aKGtt;
        zUVt5:
        goto D214J;
        goto HIty4;
        GLroc:
        if (!empty($formsConfig["checkbox_num"]) and $formsConfig["checkbox_num"] != 0) {
            goto APZMH;
        }
        goto TKdK8;
        eeeWK:
        $formsConfig["s2"]["s2num"] = $single_num2;
        goto xTyMm;
        MZc91:
        $uniacid = $_W["uniacid"];
        goto wPNC1;
        aLxMK:
        v2hWk:
        goto mH5Bd;
        uJ0wI:
        if (!empty($formsConfig["single_num"]) and $formsConfig["single_num"] != 0) {
            goto DsldQ;
        }
        goto cOK5z;
        zWhPM:
        $formsConfig["s2"] = unserialize($formsConfig["s2"]);
        goto xtpL9;
        EBoqq:
        if ($single_num2 > 100 or $single_num2 < 20) {
            goto Kmnl3;
        }
        goto eeeWK;
        EYvt6:
        $formsConfig["s2"]["s2num"] = 100;
        goto WC1DD;
        hd0eH:
        tJPsS:
        goto KhKuF;
        tLel8:
        APZMH:
        goto iM93a;
        WAVHt:
        $formsConfig["single_num"] = $single_num;
        goto preR2;
        oXfhl:
        global $_GPC, $_W;
        goto MZc91;
        kZJRa:
        $formsConfig["t5"] = unserialize($formsConfig["t5"]);
        goto lzycx;
        ZmPmX:
        $formsConfig["single_num"] = 100;
        goto aLxMK;
        U8mY3:
        $formsConfig["c2"] = unserialize($formsConfig["c2"]);
        goto zWhPM;
        HIty4:
        DsldQ:
        goto rE1LX;
        uINJy:
        $formsConfig["c2"]["c2num"] = 100;
        goto Ht2hu;
        rE1LX:
        $single_num = 100 / $formsConfig["single_num"];
        goto Zkg8A;
        qYV7a:
        Kmnl3:
        goto EYvt6;
        preR2:
        goto v2hWk;
        goto n6TZn;
        f6CXZ:
        bpyve:
        goto GLroc;
        vgi4F:
        $formsConfig["checkbox_num"] = $checkbox_num;
        goto xVCHv;
        Ht2hu:
        usPfe:
        goto tQrwZ;
        EX0bx:
        g2Zez:
        goto qSfoc;
        pfZzK:
        $single_num2 = 100 / $formsConfig["s2"]["s2num"];
        goto EBoqq;
        wPNC1:
        $formsConfig = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_forms_config") . " WHERE uniacid = :uniacid", array(":uniacid" => $uniacid));
        goto kZJRa;
        lzycx:
        $formsConfig["t6"] = unserialize($formsConfig["t6"]);
        goto U8mY3;
        EqWSI:
        $formsConfig["c2"]["c2num"] = 100;
        goto E1w2d;
        UOI2X:
        $formsConfig["s2"]["s2num"] = 100;
        goto ZqsuC;
        sTHwb:
        $formsConfig["checkbox_num"] = 100;
        goto hd0eH;
        WC1DD:
        YZuyu:
        goto f6CXZ;
        xVCHv:
        goto tJPsS;
        goto at5mq;
        THSE1:
        if ($checkbox_num > 100 or $checkbox_num < 20) {
            goto ZxffT;
        }
        goto vgi4F;
        iM93a:
        $checkbox_num = 100 / $formsConfig["checkbox_num"];
        goto THSE1;
        bIES2:
        rEU5b:
        goto pfZzK;
        aKGtt:
        if (!empty($formsConfig["c2"]["c2num"]) and $formsConfig["c2"]["c2num"] != 0) {
            goto g2Zez;
        }
        goto EqWSI;
        mH5Bd:
        D214J:
        goto FjvHG;
        FjvHG:
        if (!empty($formsConfig["s2"]["s2num"]) and $formsConfig["s2"]["s2num"] != 0) {
            goto rEU5b;
        }
        goto UOI2X;
        H3b7Z:
        goto usPfe;
        goto addCd;
        qSfoc:
        $checkbox_num2 = 100 / $formsConfig["c2"]["c2num"];
        goto De4E0;
        xTyMm:
        goto YZuyu;
        goto qYV7a;
        addCd:
        v2bGO:
        goto uINJy;
        E1w2d:
        goto lBheG;
        goto EX0bx;
        c2xlp:
    }
    public function doPageAddForms()
    {
        goto TwXVC;
        vQrV5:
        $result = "send_err";
        goto p5Nlf;
        pf6Fi:
        $d2ETI = !$formsConfig["c2"]["c2u"];
        goto ptn6o;
        Sp31N:
        $result = "form_err";
        goto iS_sB;
        PFDAy:
        $row_tel = $formsConfig["tel"] . "： " . $_GPC["tel"] . "<br />";
        goto VlGx4;
        TdSo_:
        tnJ01:
        goto St0yw;
        Z4bPx:
        $mail_sendto = explode(",", $formsConfig["mail_sendto"]);
        goto v3mi1;
        P_0xt:
        hcn54:
        goto Fa5Hv;
        jL0dn:
        if ($pWCBq) {
            goto tnJ01;
        }
        goto SWMy9;
        YVJUI:
        $mail->SMTPAuth = true;
        goto hVOci;
        ztmZ5:
        return $this->result(0, "success", $result);
        goto A1o6s;
        KtJBn:
        $row_t5 = $formsConfig["t5"]["t5n"] . "： " . $_GPC["t5"] . "<br />";
        goto P_0xt;
        aT5I2:
        $row_s2 = $formsConfig["s2"]["s2n"] . "： " . $_GPC["s2"] . "<br />";
        goto IcsiJ;
        I1M0m:
        $pWCBq = !$formsConfig["con2"]["con2u"];
        goto jL0dn;
        ptn6o:
        if ($d2ETI) {
            goto eGy9R;
        }
        goto znChd;
        znChd:
        $row_c2 = $formsConfig["c2"]["c2n"] . "： " . $_GPC["c2"] . "<br />";
        goto XOnli;
        v1I3g:
        if ($jlPgi) {
            goto qKHNP;
        }
        goto WrM0J;
        Q28Ds:
        $mail->isHTML(true);
        goto RS3to;
        B1msZ:
        $mail->Subject = date("m-d", time()) . " - " . $_GPC["name"];
        goto Q28Ds;
        hVOci:
        $mail->SMTPDebug = false;
        goto KGQu_;
        J2UMQ:
        Lc6rg:
        goto B1msZ;
        l6HuA:
        $row_mail_user = $formsConfig["mail_user"];
        goto tOcyp;
        dirTK:
        PN20B:
        goto tpMEu;
        VlGx4:
        m4V1O:
        goto cLIm2;
        TJiUp:
        $formsConfig["s2"] = unserialize($formsConfig["s2"]);
        goto sxnCP;
        Tc_bd:
        foreach ($mail_sendto as $v) {
            $mail->AddAddress($v);
            jSzxt:
        }
        goto J2UMQ;
        vTPXo:
        $formsConfig["t5"] = unserialize($formsConfig["t5"]);
        goto ary_3;
        Sb6Hf:
        $mail->Port = 465;
        goto gpef_;
        yr7qR:
        if ($O2MaP) {
            goto uaBq2;
        }
        goto mk2Or;
        WrM0J:
        $row_single = $formsConfig["single_n"] . "： " . $_GPC["single"] . "<br />";
        goto J5JZy;
        MgqkS:
        $mail->CharSet = "utf-8";
        goto vrbyT;
        HGrAR:
        $mail->setFrom($row_mail_user, $row_mail_name);
        goto Tc_bd;
        IcsiJ:
        jXBe1:
        goto vLJtx;
        GQUbc:
        if ($v4IMW) {
            goto m4V1O;
        }
        goto PFDAy;
        fSjMc:
        $data = array("uniacid" => $_W["uniacid"], "name" => $_GPC["name"], "tel" => $_GPC["tel"], "wechat" => $_GPC["wechat"], "address" => $_GPC["address"], "date" => $_GPC["date"], "timef" => $_GPC["time"], "single" => $_GPC["single"], "checkbox" => $_GPC["checkbox"], "content" => $_GPC["content"], "t5" => $_GPC["t5"], "t6" => $_GPC["t6"], "s2" => $_GPC["s2"], "c2" => $_GPC["c2"], "con2" => $_GPC["con2"], "time" => TIMESTAMP);
        goto vzV82;
        PNVXj:
        $row_content = $formsConfig["content_n"] . "： " . $_GPC["content"] . "<br />";
        goto YFd8z;
        YFd8z:
        xBCIJ:
        goto I1M0m;
        mk2Or:
        $row_checkbox = $formsConfig["checkbox_n"] . "： " . $_GPC["checkbox"] . "<br />";
        goto qmirG;
        P70zL:
        $VJzsm = !$formsConfig["time_use"];
        goto PK3cv;
        jfqWF:
        $formsConfig = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_forms_config") . " WHERE uniacid = :uniacid", array(":uniacid" => $uniacid));
        goto vTPXo;
        KrQqO:
        bX1x0:
        goto C3Hph;
        cLIm2:
        $Tht5L = !$formsConfig["wechat_use"];
        goto K4sFj;
        T9Z4b:
        $row_date = $formsConfig["date"] . "： " . $_GPC["date"] . "<br />";
        goto MH033;
        cXyLP:
        if ($dc3Hj) {
            goto rJU0a;
        }
        goto T9Z4b;
        tpMEu:
        $tiRQY = !$formsConfig["t5"]["t5u"];
        goto Mfyit;
        iS_sB:
        goto KcmAA;
        goto NLgml;
        J5JZy:
        qKHNP:
        goto Op9Ir;
        KGQu_:
        $mail->Username = $row_mail_user;
        goto kZstH;
        yBL15:
        Jyjwz:
        goto MMm4W;
        SWMy9:
        $row_con2 = $formsConfig["con2"]["con2n"] . "： " . $_GPC["con2"] . "<br />";
        goto TdSo_;
        q7_lz:
        $formsConfig["c2"] = unserialize($formsConfig["c2"]);
        goto TJiUp;
        Z_Jcv:
        if (!$mail->send()) {
            goto e5Txy;
        }
        goto pyq_O;
        K4sFj:
        if ($Tht5L) {
            goto bX1x0;
        }
        goto H9pCg;
        C3Hph:
        $Udppa = !$formsConfig["address_use"];
        goto GG5nD;
        K_RZd:
        if ($KHHMa) {
            goto xBCIJ;
        }
        goto PNVXj;
        ZXA_U:
        $row_name = $formsConfig["name"] . "： " . $_GPC["name"] . "<br />";
        goto a17CV;
        b5aBM:
        $uniacid = $_W["uniacid"];
        goto jfqWF;
        sxnCP:
        $formsConfig["con2"] = unserialize($formsConfig["con2"]);
        goto fSjMc;
        LpwyI:
        $mail->SMTPSecure = "ssl";
        goto Aqc5q;
        gpef_:
        $mail->Host = "smtp.qq.com";
        goto YVJUI;
        p5Nlf:
        KLt0i:
        goto i7kU6;
        H9pCg:
        $row_wechat = $formsConfig["wechat"] . "： " . $_GPC["wechat"] . "<br />";
        goto KrQqO;
        Aqc5q:
        $mail->IsSMTP();
        goto Sb6Hf;
        TwXVC:
        require_once "inc/class.phpmailer.php";
        goto N0lkh;
        kZstH:
        $mail->Password = $row_mail_pass;
        goto HGrAR;
        Mfyit:
        if ($tiRQY) {
            goto hcn54;
        }
        goto KtJBn;
        UfZfv:
        goto KLt0i;
        goto OxVO0;
        tOcyp:
        $row_mail_pass = $formsConfig["mail_password"];
        goto IXp83;
        z5zRL:
        global $_GPC, $_W;
        goto b5aBM;
        pyq_O:
        $result = "send_ok";
        goto UfZfv;
        waKlm:
        $jlPgi = !$formsConfig["single_use"];
        goto v1I3g;
        XOnli:
        eGy9R:
        goto TgG6G;
        qmirG:
        uaBq2:
        goto pf6Fi;
        vrbyT:
        $mail->Encoding = "base64";
        goto LpwyI;
        h2F0h:
        if ($RWHYh) {
            goto jXBe1;
        }
        goto aT5I2;
        TgG6G:
        $KHHMa = !$formsConfig["content_use"];
        goto K_RZd;
        Fa5Hv:
        $jc0zq = !$formsConfig["t6"]["t6u"];
        goto wvYzj;
        OxVO0:
        e5Txy:
        goto vQrV5;
        vLJtx:
        $O2MaP = !$formsConfig["checkbox_use"];
        goto yr7qR;
        MH033:
        rJU0a:
        goto P70zL;
        jmiVA:
        $row_t6 = $formsConfig["t6"]["t6n"] . "： " . $_GPC["t6"] . "<br />";
        goto yBL15;
        PK3cv:
        if ($VJzsm) {
            goto vXFQM;
        }
        goto dbHEB;
        IXp83:
        $row_mail_name = $formsConfig["mail_user_name"];
        goto ZXA_U;
        pS2La:
        $mail_sendto = array();
        goto Z4bPx;
        MMm4W:
        $dc3Hj = !$formsConfig["date_use"];
        goto cXyLP;
        St0yw:
        $mail = new PHPMailer();
        goto MgqkS;
        RS3to:
        $mail->Body = "<div style='height:40px;line-height:40px;font-size:16px;font-weight:bold;background:#7030A0;color:#fff;text-indent:10px;'>详细内容：</div><div style='line-height:30px;padding:15px;background:#f6f6f6'>" . $row_name . $row_tel . $row_wechat . $row_address . $row_t5 . $row_t6 . $row_date . $row_time . $row_single . $row_s2 . $row_checkbox . $row_c2 . $row_content . $row_con2 . "<div style='line-height:40px;margin-top:10px;text-align:center;color:#888;font-size:12px'>" . $row_mail_name . "</div></div>";
        goto Z_Jcv;
        a17CV:
        $v4IMW = !$formsConfig["tel_use"];
        goto GQUbc;
        i7kU6:
        KcmAA:
        goto ztmZ5;
        GG5nD:
        if ($Udppa) {
            goto PN20B;
        }
        goto m8C2R;
        ary_3:
        $formsConfig["t6"] = unserialize($formsConfig["t6"]);
        goto q7_lz;
        dbHEB:
        $row_time = $formsConfig["time"] . "： " . $_GPC["time"] . "<br />";
        goto OdOGY;
        v3mi1:
        if ($result !== false) {
            goto Do3bG;
        }
        goto Sp31N;
        NLgml:
        Do3bG:
        goto l6HuA;
        wvYzj:
        if ($jc0zq) {
            goto Jyjwz;
        }
        goto jmiVA;
        N0lkh:
        require_once "inc/class.smtp.php";
        goto z5zRL;
        OdOGY:
        vXFQM:
        goto waKlm;
        m8C2R:
        $row_address = $formsConfig["address"] . "： " . $_GPC["address"] . "<br />";
        goto dirTK;
        Op9Ir:
        $RWHYh = !$formsConfig["s2"]["s2u"];
        goto h2F0h;
        vzV82:
        $result = pdo_insert("sudu8_page_forms", $data);
        goto pS2La;
        A1o6s:
    }
    public function doPageNav()
    {
        goto J_li7;
        lfghg:
        $uniacid = $_W["uniacid"];
        goto gwTVP;
        d9yzX:
        $nav_list = explode(",", $nav["url"]);
        goto qabWg;
        xHUw3:
        return $this->result(0, "success", $nav);
        goto XQYca;
        Hx5dM:
        $type = $_GPC["type"];
        goto lfghg;
        qabWg:
        $nav["url"] = array();
        goto OK_2m;
        gwTVP:
        $nav = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_nav") . " WHERE uniacid = :uniacid and type = :type", array(":uniacid" => $uniacid, ":type" => $type));
        goto Xnmwc;
        Xnmwc:
        $nav["number"] = 100 / $nav["number"] - $nav["box_p_lr"] * 2;
        goto d9yzX;
        vAbal:
        SpDGb:
        goto xHUw3;
        OK_2m:
        foreach ($nav_list as $row) {
            goto FOHVq;
            hq8fx:
            $cate_list["list_type"] = "listPro";
            goto qsBmA;
            y06aW:
            if ($Zvn14) {
                goto fHMKd;
            }
            goto oO8eU;
            e9tIS:
            OPLPq:
            goto YPayW;
            EF5hs:
            if ($cate_list["type"] == "page") {
                goto qNeiK;
            }
            goto sJANr;
            H_nyb:
            fHMKd:
            goto E2Y3s;
            b3Ry4:
            $Zvn14 = !($cate_list["list_type"] == 1);
            goto y06aW;
            EqK0w:
            IMLVa:
            goto hbqzp;
            meDhN:
            l37QA:
            goto bWS2a;
            TpyOO:
            goto Ywhkf;
            goto WtA4w;
            qsBmA:
            Ywhkf:
            goto H_nyb;
            hGiID:
            $cate_list["name_n"] = $cate_list["name"];
            goto g1UYi;
            WtA4w:
            ZcxTO:
            goto hq8fx;
            zCPNg:
            h43uc:
            goto hazp1;
            g1UYi:
            boYwx:
            goto T5_Ok;
            STFMF:
            qNeiK:
            goto Sp_kD;
            TP993:
            if ($sZI4b) {
                goto boYwx;
            }
            goto hGiID;
            bWS2a:
            goto h43uc;
            goto STFMF;
            hazp1:
            $sZI4b = !empty($cate_list["name_n"]);
            goto TP993;
            Sp_kD:
            $cate_list["list_type"] = "page";
            goto zCPNg;
            zBIfC:
            array_push($nav["url"], $cate_list);
            goto EqK0w;
            YPayW:
            $cate_list["list_type"] = "listCate";
            goto meDhN;
            E2Y3s:
            goto l37QA;
            goto e9tIS;
            T5_Ok:
            $cate_list["catepic"] = HTTPSHOST . $cate_list["catepic"];
            goto zBIfC;
            DFgHS:
            $cate_list["list_type"] = "listPic";
            goto TpyOO;
            oO8eU:
            if ($cate_list["type"] == "showPro") {
                goto ZcxTO;
            }
            goto DFgHS;
            FOHVq:
            $cate_list = pdo_fetch("SELECT id,cid,catepic,name,name_n,type,list_type FROM " . tablename("sudu8_page_cate") . " WHERE uniacid = :uniacid and id = :id", array(":uniacid" => $uniacid, ":id" => $row));
            goto EF5hs;
            sJANr:
            if ($cate_list["list_type"] == 0) {
                goto OPLPq;
            }
            goto b3Ry4;
            hbqzp:
        }
        goto vAbal;
        J_li7:
        global $_GPC, $_W;
        goto Hx5dM;
        XQYca:
    }
    public function doPageIndex_hot()
    {
        goto dghTD;
        Hm6po:
        return $this->result(0, "success", $Index_hot);
        goto TnQd0;
        sYdlU:
        IQFsn:
        goto k_Zs9;
        DmVSC:
        foreach ($list_x as &$row) {
            goto sj06o;
            Co1wE:
            $row["type"] == "showPro_lv";
            goto fz0i2;
            pc73N:
            if ($AtGM5) {
                goto R7SDB;
            }
            goto Co1wE;
            sj06o:
            $row["thumb"] = HTTPSHOST . $row["thumb"];
            goto lpfzU;
            fz0i2:
            R7SDB:
            goto eSWjc;
            eSWjc:
            gkgU9:
            goto Q0Swa;
            lpfzU:
            $AtGM5 = !($row["type"] == "showPro" && $row["is_more"] == 1);
            goto pc73N;
            Q0Swa:
        }
        goto BUEZT;
        BUEZT:
        Uxld9:
        goto M463v;
        b1PjO:
        foreach ($list_y as &$row) {
            goto SHXfK;
            L020O:
            $AtGM5 = !($row["type"] == "showPro" && $row["is_more"] == 1);
            goto PKHXA;
            A69Ap:
            $row["ctime"] = date("y-m-d H:i:s", $row["ctime"]);
            goto L020O;
            HHv_B:
            $row["type"] == "showPro_lv";
            goto ENJDE;
            ENJDE:
            nYB9d:
            goto flgj6;
            SHXfK:
            $row["thumb"] = HTTPSHOST . $row["thumb"];
            goto A69Ap;
            PKHXA:
            if ($AtGM5) {
                goto nYB9d;
            }
            goto HHv_B;
            flgj6:
            hpfS0:
            goto jLyZ2;
            jLyZ2:
        }
        goto sYdlU;
        erg39:
        $Index_hot["list_x"] = $list_x;
        goto Fmw8h;
        dghTD:
        global $_GPC, $_W;
        goto bhoO6;
        bhoO6:
        $uniacid = $_W["uniacid"];
        goto qtt73;
        M463v:
        $list_y = pdo_fetchall("SELECT id,type,num,title,thumb,ctime,hits,`desc`,price,market_price,sale_num,buy_type FROM " . tablename("sudu8_page_products") . " WHERE type_y=1 and flag = 1 and uniacid = :uniacid ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid));
        goto b1PjO;
        Fmw8h:
        $Index_hot["list_y"] = $list_y;
        goto Hm6po;
        qtt73:
        $list_x = pdo_fetchall("SELECT id,type,num,title,thumb,`desc`,buy_type FROM " . tablename("sudu8_page_products") . " WHERE type_x=1 and uniacid = :uniacid ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid));
        goto DmVSC;
        k_Zs9:
        $Index_hot = array();
        goto erg39;
        TnQd0:
    }
    public function doPageIndex_cate()
    {
        goto dsDgY;
        dCp7s:
        $index_cate = pdo_fetchall("SELECT id,cid,num,name,ename,type,list_type,list_style,list_tstyle,list_stylet FROM " . tablename("sudu8_page_cate") . " WHERE cid=0 and uniacid = :uniacid and show_i = 1 and statue =1   ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid));
        goto s0lcH;
        dsDgY:
        global $_GPC, $_W;
        goto no1JU;
        no1JU:
        $uniacid = $_W["uniacid"];
        goto dCp7s;
        vxFfW:
        U8jHe:
        goto XLepb;
        s0lcH:
        foreach ($index_cate as $key => $row) {
            goto lwGKI;
            socno:
            A_n6r:
            goto motx9;
            Xy0jD:
            $index_cate[$key]["list"] = pdo_fetchall("SELECT id,num,title,type,thumb,appId,path,`desc` FROM " . tablename("sudu8_page_wxapps") . " WHERE  pcid= :pcid and uniacid = :uniacid and type_i = 1 ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid, ":pcid" => $id));
            goto IV7A_;
            hmq0V:
            $index_cate[$key]["list"] = pdo_fetchall("SELECT id,cid,num,name,ename,catepic,cdesc,list_style,list_tstyle FROM " . tablename("sudu8_page_cate") . " WHERE id=:cid and uniacid = :uniacid and statue =1 ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid, ":cid" => $id));
            goto pwEmw;
            pwEmw:
            foreach ($index_cate[$key]["list"] as $key2 => $row2) {
                goto Z45se;
                Z45se:
                $index_cate[$key]["list"][$key2]["catepic"] = HTTPSHOST . $row2["catepic"];
                goto J8ki7;
                wmy7y:
                ETnSU:
                goto FAU6D;
                J8ki7:
                $index_cate[$key]["list"][$key2]["type"] = "page";
                goto wmy7y;
                FAU6D:
            }
            goto jvCZ1;
            RVfKJ:
            $AUVrU = !($row["type"] == "page");
            goto NdCgd;
            JhIfc:
            Q793h:
            goto gcjlJ;
            mE4HU:
            if ($row["type"] == "showPic" or $row["type"] == "showArt" or $row["type"] == "showPro") {
                goto De5OM;
            }
            goto uO2EH;
            gGp5x:
            $qc4bi = !($row["list_type"] == 1);
            goto Qamch;
            YaafG:
            goto muibk;
            goto qxbOT;
            IV7A_:
            foreach ($index_cate[$key]["list"] as $key2 => $row2) {
                $index_cate[$key]["list"][$key2]["thumb"] = HTTPSHOST . $row2["thumb"];
                uMmqA:
            }
            goto zfmSk;
            dDlE8:
            $index_cate[$key]["list"] = array();
            goto wsHDr;
            zfmSk:
            jej6F:
            goto UNVII;
            hnYTz:
            De5OM:
            goto oGTbE;
            I0dJE:
            $index_cate[$key]["list"] = array();
            goto ImsLT;
            lTcWt:
            $index_cate[$key]["list"] = array();
            goto hmq0V;
            Q8u6l:
            txsor:
            goto gGq91;
            tq2W6:
            $index_cate[$key]["list"] = array();
            goto Xy0jD;
            yFipo:
            oDgDl:
            goto Q8u6l;
            m_da4:
            $index_cate[$key]["l_type"] = "listCate";
            goto I0dJE;
            dtV8n:
            mBEwV:
            goto gG8z0;
            GnTXq:
            wRs3o:
            goto RXsD2;
            yA4ke:
            $index_cate[$key]["list"] = pdo_fetchall("SELECT id,num,type_i,title,thumb,hits,type,ctime,`desc`,price,market_price,sale_num,is_more,buy_type,sale_tnum FROM " . tablename("sudu8_page_products") . " WHERE  pcid= :pcid and flag = 1 and uniacid = :uniacid and type_i = 1 ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid, ":pcid" => $id));
            goto ZH00M;
            HU63W:
            ilJdH:
            goto JFhKs;
            uO2EH:
            if ($row["type"] == "showWxapps") {
                goto wRs3o;
            }
            goto RVfKJ;
            lwGKI:
            $id = $row["id"];
            goto vAwhZ;
            os9gX:
            pXKQb:
            goto tdhGO;
            ZH00M:
            foreach ($index_cate[$key]["list"] as $key2 => $row2) {
                goto Dump3;
                UpmIM:
                if ($MSh79) {
                    goto YvTRF;
                }
                goto bEBhy;
                kfARw:
                OI9cX:
                goto YQfSv;
                b3oKN:
                $index_cate[$key]["list"][$key2]["sale_num"] = intval($index_cate[$key]["list"][$key2]["sale_num"]) + intval($index_cate[$key]["list"][$key2]["sale_tnum"]);
                goto id4kD;
                id4kD:
                $MSh79 = !($row2["is_more"] == 1 && $row2["type"] == "showPro");
                goto UpmIM;
                VtUNb:
                YvTRF:
                goto kfARw;
                bEBhy:
                $index_cate[$key]["list"][$key2]["type"] = "showPro_lv";
                goto VtUNb;
                Dump3:
                $index_cate[$key]["list"][$key2]["ctime"] = date("y-m-d H:i:s", $index_cate[$key]["list"][$key2]["ctime"]);
                goto PJz2q;
                PJz2q:
                $index_cate[$key]["list"][$key2]["thumb"] = HTTPSHOST . $row2["thumb"];
                goto b3oKN;
                YQfSv:
            }
            goto yFipo;
            NdCgd:
            if ($AUVrU) {
                goto HL_LE;
            }
            goto ptxtw;
            gcjlJ:
            goto mBEwV;
            goto hnYTz;
            gG8z0:
            sTqvW:
            goto iCFPx;
            smGDr:
            $index_cate[$key]["list"] = pdo_fetchall("SELECT id,cid,num,name,ename,catepic,cdesc,list_style,list_tstyle FROM " . tablename("sudu8_page_cate") . " WHERE cid=:cid and uniacid = :uniacid and statue =1 ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid, ":cid" => $id));
            goto FjMbC;
            p2S2v:
            vviGX:
            goto CMAs6;
            nGfHn:
            $index_cate[$key]["l_type"] = "page";
            goto dDlE8;
            vAwhZ:
            $row["catepic"] = HTTPSHOST . $row["catepic"];
            goto mE4HU;
            wsHDr:
            $index_cate[$key]["list"] = pdo_fetchall("SELECT id,cid,num,name,ename,catepic,cdesc,list_style,list_tstyle,type FROM " . tablename("sudu8_page_cate") . " WHERE cid=:cid and uniacid = :uniacid and statue =1 ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid, ":cid" => $id));
            goto Fbcgc;
            P3HyU:
            $qc4bi = !($row["list_type"] == 1);
            goto oUBnh;
            Wn85M:
            $qc4bi = !($row["list_type"] == 1);
            goto FQ_Yo;
            tdhGO:
            HL_LE:
            goto LaNBC;
            LaNBC:
            goto Q793h;
            goto GnTXq;
            ImsLT:
            $index_cate[$key]["list"] = pdo_fetchall("SELECT id,cid,num,name,ename,catepic,cdesc,list_style,list_tstyle,type FROM " . tablename("sudu8_page_cate") . " WHERE cid=:cid and uniacid = :uniacid and statue =1 ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid, ":cid" => $id));
            goto Eg_Ii;
            motx9:
            muibk:
            goto JhIfc;
            FQ_Yo:
            if ($qc4bi) {
                goto txsor;
            }
            goto i3ozb;
            qrtki:
            eQEAb:
            goto os9gX;
            UNVII:
            eVWKg:
            goto YaafG;
            pi3ka:
            goto blwM_;
            goto FqEkb;
            Eg_Ii:
            foreach ($index_cate[$key]["list"] as $key2 => $row2) {
                goto AxfLz;
                kAP1X:
                goto ImARi;
                goto eESop;
                asnVS:
                $index_cate[$key]["list"][$key2]["type"] = "listPic";
                goto kAP1X;
                R5DnE:
                $index_cate[$key]["list"][$key2]["catepic"] = HTTPSHOST . $row2["catepic"];
                goto Aksgs;
                eESop:
                CPAoS:
                goto jZmLc;
                AxfLz:
                if ($index_cate[$key]["list"][$key2]["type"] == "showPro") {
                    goto CPAoS;
                }
                goto asnVS;
                rc3FI:
                ImARi:
                goto R5DnE;
                Aksgs:
                beHtH:
                goto EBUv6;
                jZmLc:
                $index_cate[$key]["list"][$key2]["type"] = "listPro";
                goto rc3FI;
                EBUv6:
            }
            goto p2S2v;
            CAaLW:
            $index_cate[$key]["l_type"] = "listPic";
            goto pi3ka;
            c1wot:
            $index_cate[$key]["l_type"] = "listPro";
            goto aODWA;
            dOP8E:
            $index_cate[$key]["l_type"] = "page";
            goto lTcWt;
            i3ozb:
            if ($index_cate[$key]["type"] == "showPro") {
                goto JxKx7;
            }
            goto CAaLW;
            qxbOT:
            NavXI:
            goto t0tyY;
            CMAs6:
            x04uA:
            goto dtV8n;
            t0tyY:
            $index_cate[$key]["l_type"] = "listCate";
            goto z9RkA;
            FjMbC:
            foreach ($index_cate[$key]["list"] as $key2 => $row2) {
                $index_cate[$key]["list"][$key2]["catepic"] = HTTPSHOST . $row2["catepic"];
                nZ7xG:
            }
            goto socno;
            rINT1:
            FHEK3:
            goto nGfHn;
            oGTbE:
            if ($row["list_type"] == 0) {
                goto WvZlW;
            }
            goto Wn85M;
            aODWA:
            blwM_:
            goto TXfyP;
            FqEkb:
            JxKx7:
            goto c1wot;
            JFhKs:
            goto pXKQb;
            goto rINT1;
            oUBnh:
            if ($qc4bi) {
                goto ilJdH;
            }
            goto dOP8E;
            RXsD2:
            if ($row["list_type"] == 0) {
                goto NavXI;
            }
            goto gGp5x;
            TXfyP:
            $index_cate[$key]["list"] = array();
            goto yA4ke;
            jvCZ1:
            zMZiQ:
            goto HU63W;
            Fbcgc:
            foreach ($index_cate[$key]["list"] as $key2 => $row2) {
                $index_cate[$key]["list"][$key2]["catepic"] = HTTPSHOST . $row2["catepic"];
                dsAdC:
            }
            goto qrtki;
            Qamch:
            if ($qc4bi) {
                goto eVWKg;
            }
            goto MIfhi;
            G8deM:
            WvZlW:
            goto m_da4;
            z9RkA:
            $index_cate[$key]["list"] = array();
            goto smGDr;
            gGq91:
            goto x04uA;
            goto G8deM;
            ptxtw:
            if ($row["list_type"] == 0) {
                goto FHEK3;
            }
            goto P3HyU;
            MIfhi:
            $index_cate[$key]["l_type"] = "listPic";
            goto tq2W6;
            iCFPx:
        }
        goto vxFfW;
        XLepb:
        return $this->result(0, "success", $index_cate);
        goto BpZ9k;
        BpZ9k:
    }
    public function doPagelistPic()
    {
        goto nojfe;
        ZVwJQ:
        foreach ($cateinfo["list"] as &$row) {
            goto xfeKM;
            xfeKM:
            $row["ctime"] = date("y-m-d H:i:s", $row["ctime"]);
            goto BIyS2;
            YLUod:
            $row["type"] = "showPro_lv";
            goto Wia6E;
            wyunt:
            ZmwIB:
            goto vynfv;
            Wia6E:
            p002S:
            goto wyunt;
            BIyS2:
            $row["thumb"] = HTTPSHOST . $row["thumb"];
            goto Z_Vkh;
            i7SQr:
            if ($Zx5BZ) {
                goto p002S;
            }
            goto YLUod;
            Z_Vkh:
            $Zx5BZ = !($row["is_more"] == 1);
            goto i7SQr;
            vynfv:
        }
        goto haeTZ;
        CUq1o:
        $cateinfo = pdo_fetch("SELECT id,name,ename,type,list_type,list_style,list_tstyle,list_tstylel,list_stylet FROM " . tablename("sudu8_page_cate") . " WHERE uniacid = :uniacid and id = :cid", array(":uniacid" => $uniacid, ":cid" => $pcid));
        goto JPOBG;
        EbANI:
        goto a9Szc;
        goto NKUZZ;
        nDelj:
        foreach ($cateinfo["list"] as &$row) {
            $row["thumb"] = HTTPSHOST . $row["thumb"];
            F6JYD:
        }
        goto SCrqm;
        uwbp4:
        $cid = intval($_GPC["cid"]);
        goto g99tG;
        agha4:
        if ($OEfEK) {
            goto p2csc;
        }
        goto qvUcL;
        a2_L0:
        $cateinfo["list"] = pdo_fetchall("SELECT id,type,num,title,thumb,appId,path,`desc` FROM " . tablename("sudu8_page_wxapps") . "WHERE uniacid = :uniacid and " . $slid . " = :cid ORDER BY num DESC,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, array(":uniacid" => $uniacid, ":cid" => $cid));
        goto nDelj;
        SBofU:
        $pcid = $cateinfo["id"];
        goto RvQoJ;
        PZ6Q2:
        $cateinfo["cate"] = pdo_fetchall("SELECT id,num,name FROM " . tablename("sudu8_page_cate") . "WHERE uniacid = :uniacid and cid = :cid ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid, ":cid" => $pcid));
        goto Agytz;
        m3pY0:
        a9Szc:
        goto xLAFM;
        BwMe7:
        $cateinfo["this"] = pdo_fetch("SELECT id,name,ename,type,list_type,list_style,list_tstyle,list_tstylel,list_stylet FROM " . tablename("sudu8_page_cate") . " WHERE uniacid = :uniacid and id = :cid", array(":uniacid" => $uniacid, ":cid" => $cid));
        goto Fcat5;
        TavXt:
        AzUNM:
        goto CUq1o;
        JPOBG:
        $cate_first = pdo_fetch("SELECT id,name FROM " . tablename("sudu8_page_cate") . " WHERE uniacid = :uniacid and id = :cid", array(":uniacid" => $uniacid, ":cid" => $pcid));
        goto m2HPB;
        NKUZZ:
        c9goH:
        goto YHudw;
        InA8t:
        $pindex = max(1, intval($_GPC["page"]));
        goto uwbp4;
        pFjOq:
        if ($cateinfo["cid"] == 0) {
            goto tCCkV;
        }
        goto LgXuY;
        xLAFM:
        return $this->result(0, "success", $cateinfo);
        goto RUATW;
        LgXuY:
        $pcid = $cateinfo["cid"];
        goto Tlu11;
        SCrqm:
        PYnna:
        goto cu9lb;
        Tlu11:
        $slid = "cid";
        goto n2jwN;
        Fcat5:
        if ($cateinfo["type"] == "showArt" or $cateinfo["type"] == "showPic" or $cateinfo["type"] == "showPro") {
            goto c9goH;
        }
        goto D6G4H;
        g99tG:
        $psize = 10;
        goto l3E4d;
        m2HPB:
        $cate_first["name"] = "全部";
        goto PZ6Q2;
        n2jwN:
        goto AzUNM;
        goto g6J4S;
        cu9lb:
        p2csc:
        goto EbANI;
        myTFu:
        $cateinfo["list"] = pdo_fetchall("SELECT id,type,num,title,thumb,ctime,hits,`desc`,price,market_price,sale_num,is_more,buy_type FROM " . tablename("sudu8_page_products") . "WHERE uniacid = :uniacid and flag = 1 and " . $slid . " = :cid ORDER BY num DESC,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, array(":uniacid" => $uniacid, ":cid" => $cid));
        goto ZVwJQ;
        l3E4d:
        $cateinfo = pdo_fetch("SELECT id,cid,type FROM " . tablename("sudu8_page_cate") . " WHERE uniacid = :uniacid and id = :cid", array(":uniacid" => $uniacid, ":cid" => $cid));
        goto pFjOq;
        g6J4S:
        tCCkV:
        goto SBofU;
        haeTZ:
        jGn7z:
        goto m3pY0;
        nojfe:
        global $_GPC, $_W;
        goto Guey_;
        Guey_:
        $uniacid = $_W["uniacid"];
        goto InA8t;
        Agytz:
        array_unshift($cateinfo["cate"], $cate_first);
        goto BwMe7;
        RvQoJ:
        $slid = "pcid";
        goto TavXt;
        qvUcL:
        $cateinfo["num"] = pdo_fetchall("SELECT id FROM " . tablename("sudu8_page_wxapps") . "WHERE uniacid = :uniacid and " . $slid . " = :cid", array(":uniacid" => $uniacid, ":cid" => $cid));
        goto a2_L0;
        YHudw:
        $cateinfo["num"] = pdo_fetchall("SELECT id FROM " . tablename("sudu8_page_products") . "WHERE uniacid = :uniacid and flag = 1 and " . $slid . " = :cid", array(":uniacid" => $uniacid, ":cid" => $cid));
        goto myTFu;
        D6G4H:
        $OEfEK = !($cateinfo["type"] == "showWxapps");
        goto agha4;
        RUATW:
    }
    public function doPagelistCate()
    {
        goto UpWXr;
        LWCFb:
        $cateinfo = pdo_fetch("SELECT id,name,ename,type,list_type,type,list_style,list_tstyle,list_tstylel,list_stylet FROM " . tablename("sudu8_page_cate") . " WHERE uniacid = :uniacid and id = :cid", array(":uniacid" => $uniacid, ":cid" => $cid));
        goto T7s99;
        fSTdI:
        if ($cateinfo["type"] == "showPro") {
            goto w_Ged;
        }
        goto DZkeO;
        tMCIl:
        $pindex = max(1, intval($_GPC["page"]));
        goto LbJwB;
        NGOem:
        gaQVr:
        goto t25HY;
        haZn5:
        $cateinfo["l_type"] = "listPro";
        goto NGOem;
        DZkeO:
        $cateinfo["l_type"] = "listPic";
        goto YKKQg;
        LmyFC:
        w_Ged:
        goto haZn5;
        XemsS:
        $uniacid = $_W["uniacid"];
        goto tMCIl;
        UpWXr:
        global $_GPC, $_W;
        goto XemsS;
        t25HY:
        foreach ($cateinfo["list"] as &$row) {
            $row["catepic"] = HTTPSHOST . $row["catepic"];
            OagQY:
        }
        goto r_GnN;
        YKKQg:
        goto gaQVr;
        goto LmyFC;
        r_GnN:
        gX6NT:
        goto O5ukF;
        T7s99:
        $cateinfo["list"] = pdo_fetchall("SELECT id,name,catepic,cdesc,list_style,list_tstyle,list_stylet,list_tstylel FROM " . tablename("sudu8_page_cate") . " WHERE uniacid = :uniacid and cid = :cid ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid, ":cid" => $cid));
        goto fSTdI;
        LbJwB:
        $cid = intval($_GPC["cid"]);
        goto LWCFb;
        O5ukF:
        return $this->result(0, "success", $cateinfo);
        goto pM84p;
        pM84p:
    }
    public function doPageshowPic()
    {
        goto ECXuv;
        stguE:
        $num = count($pics["text"]);
        goto hTWEP;
        hTWEP:
        $i = 0;
        goto eExa2;
        RUjEZ:
        $pics["text"][$i] = HTTPSHOST . $pics["text"][$i];
        goto YKIB_;
        VFYcb:
        $pics["btn"] = pdo_fetch("SELECT pic_page_btn FROM " . tablename("sudu8_page_cate") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $pics["cid"], ":uniacid" => $uniacid));
        goto g2Rzk;
        ECXuv:
        global $_GPC, $_W;
        goto IpHGb;
        IpHGb:
        $uniacid = $_W["uniacid"];
        goto oWqVT;
        vJveK:
        goto Am197;
        goto wkCY6;
        eExa2:
        Am197:
        goto PRCG8;
        oWqVT:
        $id = intval($_GPC["id"]);
        goto FwgnE;
        ogTLh:
        $pics["text"] = unserialize($pics["text"]);
        goto stguE;
        FwgnE:
        $pics = pdo_fetch("SELECT title,text,hits,cid,`desc`,buy_type FROM " . tablename("sudu8_page_products") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $uniacid));
        goto VFYcb;
        YKIB_:
        NyY4O:
        goto mGYdm;
        yFy6J:
        pdo_update("sudu8_page_products", $data, array("id" => $id, "uniacid" => $uniacid));
        goto ogTLh;
        mGYdm:
        $i++;
        goto vJveK;
        wkCY6:
        SyB3M:
        goto zGcd9;
        zGcd9:
        return $this->result(0, "success", $pics);
        goto zKQVD;
        g2Rzk:
        $data = array("hits" => $pics["hits"] + 1);
        goto yFy6J;
        PRCG8:
        if (!($i < $num)) {
            goto SyB3M;
        }
        goto RUjEZ;
        zKQVD:
    }
    public function doPageshowPro()
    {
        goto i63fo;
        o5CHJ:
        $wiJuA = !$pro["more_type_x"];
        goto XxLFq;
        xn2QZ:
        pdo_update("sudu8_page_products", $ndata, array("id" => $id, "uniacid" => $uniacid));
        goto PKe22;
        W3yo8:
        if ($XzIJT) {
            goto dzF_1;
        }
        goto K5nFv;
        Nfjmc:
        $orders = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE pid = :id and uniacid = :uniacid and flag >0", array(":id" => $id, ":uniacid" => $uniacid));
        goto CI2Ky;
        p73qW:
        $sale_num = 0;
        goto MIBOf;
        fcIGd:
        dzF_1:
        goto MtNCM;
        i3FKs:
        if ($pro["sale_time"] == 0) {
            goto HLsJy;
        }
        goto FUxLZ;
        IuFd6:
        if (!($i < $num)) {
            goto e35Dy;
        }
        goto niaIS;
        MtNCM:
        S5RPF:
        goto NEbbW;
        Kn98o:
        $pro["text"] = unserialize($pro["text"]);
        goto em0Yh;
        kWuzX:
        $pro["my_num"] = $my_num;
        goto d19SH;
        c31j9:
        if ($pro["pro_kc"] >= 0 && $pro["is_more"] == 0) {
            goto DL71N;
        }
        goto Ay5rg;
        MIBOf:
        $d0_Ij = !$orders;
        goto oVB0Z;
        K5nFv:
        $collectcount = 1;
        goto fcIGd;
        A0Dik:
        foreach ($allorders as $rec) {
            goto SYhSY;
            ZAkE1:
            pdo_update("sudu8_page_order", $kdata, array("order_id" => $rec["order_id"], "uniacid" => $uniacid));
            goto Q4fB8;
            kNGRG:
            $kdata["reback"] = 1;
            goto ZAkE1;
            SYhSY:
            $kdata["flag"] = -1;
            goto kNGRG;
            Q4fB8:
            SkG6C:
            goto Cov1V;
            Cov1V:
        }
        goto yNfq4;
        CpOIa:
        SgxRX:
        goto zFeGa;
        txkRd:
        if ($xlv9Z) {
            goto YVpqh;
        }
        goto X0L2Q;
        d19SH:
        $now = time();
        goto i3FKs;
        JgMw2:
        $pro["labels"] = array();
        goto hSWt1;
        Lxj9k:
        if ($pJM4p) {
            goto VwJwj;
        }
        goto ZOqpa;
        ykIg_:
        UmUFR:
        goto qNuja;
        J5lBD:
        if ($d0_Ij) {
            goto MtdPq;
        }
        goto wExSI;
        vrLSg:
        Uxt3J:
        goto IuFd6;
        wExSI:
        $allnum = 0;
        goto xr5D2;
        iERZC:
        goto Euq1_;
        goto CpOIa;
        ZCxxc:
        HLsJy:
        goto ITSsm;
        CImAb:
        $uid = $user["id"];
        goto KRIr2;
        ZOqpa:
        $more_type = unserialize($pro["more_type"]);
        goto nq2Kl;
        ogAhY:
        $B24QK = !$allorders;
        goto WQHwa;
        FUxLZ:
        if ($pro["sale_time"] > $now) {
            goto SgxRX;
        }
        goto RGjAA;
        Y9fNf:
        $id = intval($_GPC["id"]);
        goto wcb43;
        zFeGa:
        $pro["is_sale"] = 0;
        goto XyrwN;
        RGjAA:
        $pro["is_sale"] = 1;
        goto iERZC;
        X0L2Q:
        $d0_Ij = !$orders;
        goto J5lBD;
        aAtvF:
        $pro["thumb"] = HTTPSHOST . $pro["thumb"];
        goto GAY90;
        itHlY:
        $more_type_x = unserialize($pro["more_type_x"]);
        goto ovLu1;
        ZS030:
        $labels = explode(",", $pro["labels"]);
        goto zNvz6;
        xUKVn:
        $arrkk = array();
        goto A2Fe6;
        v30bc:
        DL71N:
        goto o9HgV;
        wXt70:
        vs9Jr:
        goto YMIEc;
        ZHYtX:
        LOn_i:
        goto CYyHX;
        xdpUj:
        pdo_update("sudu8_page_order", $kdata, array("order_id" => $id, "uniacid" => $uniacid));
        goto cLgtK;
        lXUy9:
        goto Uxt3J;
        goto W5XJU;
        nq2Kl:
        $newmore = array_chunk($more_type, 4);
        goto J2CX2;
        U9Wn0:
        goto qsRnl;
        goto v30bc;
        BrJdd:
        goto hPQYA;
        goto m4NqZ;
        Agfru:
        $i++;
        goto lXUy9;
        tAJlS:
        $user = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_user") . " WHERE uniacid = :uniacid and openid = :openid", array(":uniacid" => $uniacid, ":openid" => $openid));
        goto CImAb;
        CCO1g:
        $my_num = 0;
        goto wpXIP;
        CI2Ky:
        $xlv9Z = !($pro["is_more"] == 1);
        goto txkRd;
        Kc9Bj:
        $uniacid = $_W["uniacid"];
        goto Y9fNf;
        wcb43:
        $pro = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_products") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $uniacid));
        goto E_RLz;
        Jiv3E:
        EmFZh:
        goto ZS030;
        ULAEO:
        if ($ngZ5L) {
            goto y4lZc;
        }
        goto PpUSv;
        sUWxw:
        xexJh:
        goto OaMzu;
        XyrwN:
        Euq1_:
        goto GQFq3;
        qNuja:
        MtdPq:
        goto ZEdjt;
        A2Fe6:
        foreach ($labels as $key => $res) {
            goto XBVAU;
            bS5xA:
            array_push($arrkk, $vvkk);
            goto a_XH0;
            XBVAU:
            $vvkk = array($key, $res);
            goto bS5xA;
            a_XH0:
            YNF65:
            goto oLjkp;
            oLjkp:
        }
        goto wXt70;
        niaIS:
        $pro["text"][$i] = HTTPSHOST . $pro["text"][$i];
        goto lYXn7;
        GcgjO:
        foreach ($orders as $rec) {
            $sale_num += $rec["num"];
            Iaart:
        }
        goto RSbOh;
        HErfd:
        qsRnl:
        goto xMyRm;
        urRLq:
        kdmlG:
        goto kugWx;
        uHgO6:
        $more_type_num = unserialize($pro["more_type_num"]);
        goto ZKiUo;
        ZhV8x:
        PRGlh:
        goto uT1Cs;
        dk_RL:
        y4lZc:
        goto U9Wn0;
        PKe22:
        bTyZS:
        goto HErfd;
        YGL2X:
        $kdata["flag"] = -1;
        goto xdpUj;
        DsEiG:
        $xuVXW = !$openid;
        goto nSXHD;
        ovLu1:
        $pro["more_type_x"] = $more_type_x;
        goto rOwg1;
        xr5D2:
        foreach ($orders as $rec) {
            goto wdiaz;
            wdiaz:
            $duo = $rec["order_duo"];
            goto DE6Gd;
            DE6Gd:
            $newduo = unserialize($duo);
            goto jbMyT;
            jbMyT:
            foreach ($newduo as $key => &$res) {
                $allnum[$key] += $res[5];
                opC9z:
            }
            goto uXlp5;
            uXlp5:
            t7RwZ:
            goto IixfQ;
            IixfQ:
            epabQ:
            goto KXqSC;
            KXqSC:
        }
        goto ykIg_;
        yNfq4:
        E64br:
        goto wtpYw;
        zNvz6:
        $pro["labels"] = $labels;
        goto BrJdd;
        znhmo:
        if ($pro["labels"] && $pro["is_more"] == 1) {
            goto mjtR_;
        }
        goto JgMw2;
        Ay5rg:
        $ngZ5L = !($pro["pro_kc"] < 0 && $pro["is_more"] == 0);
        goto ULAEO;
        WQHwa:
        if ($B24QK) {
            goto DDGaO;
        }
        goto A0Dik;
        J2CX2:
        $pro["more_type"] = $newmore;
        goto v_4Yq;
        xMyRm:
        $xlv9Z = !($pro["is_more"] == 1);
        goto RbN0O;
        RbN0O:
        if ($xlv9Z) {
            goto LOn_i;
        }
        goto UYfrT;
        c8mN4:
        $onum = 0;
        goto sKMUD;
        rOwg1:
        WZRpi:
        goto ALzfk;
        PpUSv:
        $now = time();
        goto e2WQs;
        v_4Yq:
        VwJwj:
        goto o5CHJ;
        ZEdjt:
        YVpqh:
        goto p73qW;
        Bg0Qr:
        if ($B24QK) {
            goto bTyZS;
        }
        goto xPhBX;
        ITSsm:
        $pro["is_sale"] = 1;
        goto PRJSO;
        GAY90:
        if ($pro["labels"] && $pro["is_more"] == 0) {
            goto EmFZh;
        }
        goto znhmo;
        ccvlW:
        $B24QK = !$allorders;
        goto Bg0Qr;
        CynpS:
        if ($yX0F3) {
            goto Xi58J;
        }
        goto eX_HJ;
        cIud9:
        $XzIJT = !($collect["n"] > 0);
        goto W3yo8;
        o9HgV:
        $now = time();
        goto c8mN4;
        OaMzu:
        Xi58J:
        goto kWuzX;
        NEbbW:
        $pro["collectcount"] = $collectcount;
        goto c31j9;
        wtpYw:
        DDGaO:
        goto dk_RL;
        xPhBX:
        foreach ($allorders as $rec) {
            goto KU0GP;
            o1252:
            pdo_update("sudu8_page_order", $kdata, array("order_id" => $rec["order_id"], "uniacid" => $uniacid));
            goto SZldt;
            ubscd:
            $kdata["reback"] = 1;
            goto o1252;
            KU0GP:
            $onum += $rec["num"];
            goto oRtns;
            SZldt:
            tYZqL:
            goto j1I3e;
            oRtns:
            $kdata["flag"] = -1;
            goto ubscd;
            j1I3e:
        }
        goto ZhV8x;
        cLgtK:
        eRm5g:
        goto ZHYtX;
        jmV6J:
        if ($yLt2K) {
            goto K0nFK;
        }
        goto uHgO6;
        ALzfk:
        $yLt2K = !$pro["more_type_num"];
        goto jmV6J;
        KRIr2:
        $collect = pdo_fetch("SELECT count(*) as n FROM " . tablename("sudu8_page_collect") . " WHERE uniacid = :uniacid and uid = :uid and type = :type and cid = :cid", array(":uniacid" => $uniacid, ":uid" => $uid, ":type" => "showPro", ":cid" => $id));
        goto cIud9;
        UYfrT:
        $now = time();
        goto pm3OW;
        PRJSO:
        PZD3m:
        goto aAtvF;
        YMIEc:
        $pro["labels"] = $arrkk;
        goto ON7xl;
        lYXn7:
        jxU_C:
        goto Agfru;
        wpXIP:
        $collectcount = 0;
        goto DsEiG;
        m4NqZ:
        mjtR_:
        goto QrldG;
        U0eeh:
        return $this->result(0, "success", $pro);
        goto I2B2n;
        XxLFq:
        if ($wiJuA) {
            goto WZRpi;
        }
        goto itHlY;
        jBPZF:
        pdo_update("sudu8_page_products", $data, array("id" => $id, "uniacid" => $uniacid));
        goto Kn98o;
        RSbOh:
        CbQ5J:
        goto urRLq;
        XiCpL:
        $openid = $_GPC["openid"];
        goto cScFJ;
        pm3OW:
        $f9V02 = !($now > $orders["overtime"] && $orders["flag"] == 0);
        goto I88a4;
        nSXHD:
        if ($xuVXW) {
            goto S5RPF;
        }
        goto tAJlS;
        GQFq3:
        goto PZD3m;
        goto ZCxxc;
        uT1Cs:
        $ndata["pro_kc"] = $pro["pro_kc"] + $onum;
        goto xn2QZ;
        W5XJU:
        e35Dy:
        goto Nfjmc;
        E_RLz:
        $data = array("hits" => $pro["hits"] + 1);
        goto jBPZF;
        sKMUD:
        $allorders = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE pid = :pid  and uniacid = :uniacid and flag = 0 and overtime < :nowtime", array(":pid" => $id, ":uniacid" => $uniacid, ":nowtime" => $now));
        goto ccvlW;
        ZKiUo:
        $pro["more_type_num"] = $more_type_num;
        goto Mlsjd;
        em0Yh:
        $num = count($pro["text"]);
        goto YTrZJ;
        i63fo:
        global $_GPC, $_W;
        goto Kc9Bj;
        jw4io:
        $pJM4p = !$pro["more_type"];
        goto Lxj9k;
        YTrZJ:
        $i = 0;
        goto vrLSg;
        oVB0Z:
        if ($d0_Ij) {
            goto kdmlG;
        }
        goto GcgjO;
        Mlsjd:
        K0nFK:
        goto U0eeh;
        kugWx:
        $pro["sale_num"] = $pro["sale_num"] + $sale_num;
        goto XiCpL;
        ON7xl:
        hPQYA:
        goto jw4io;
        e2WQs:
        $allorders = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE pid = :pid  and uniacid = :uniacid and flag = 0 and overtime < :nowtime", array(":pid" => $id, ":uniacid" => $uniacid, ":nowtime" => $now));
        goto ogAhY;
        I88a4:
        if ($f9V02) {
            goto eRm5g;
        }
        goto YGL2X;
        hSWt1:
        goto hPQYA;
        goto Jiv3E;
        cScFJ:
        $myorders = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE pid = :id and openid = :openid and uniacid = :uniacid and flag>=0", array(":id" => $id, ":openid" => $openid, ":uniacid" => $uniacid));
        goto CCO1g;
        CYyHX:
        $yX0F3 = !$myorders;
        goto CynpS;
        eX_HJ:
        foreach ($myorders as $res) {
            $my_num += $res["num"];
            lj9GJ:
        }
        goto sUWxw;
        QrldG:
        $labels = unserialize($pro["labels"]);
        goto xUKVn;
        I2B2n:
    }
    public function doPageDingd()
    {
        goto Yoxmx;
        F1M9_:
        goto wOQuc;
        goto cwxVt;
        HnlPv:
        $data["success"] = 1;
        goto QXQjW;
        AyzxU:
        $order = $_GPC["order"];
        goto Ia3OV;
        Qb0Et:
        sih3A:
        goto M7FxL;
        OT_OP:
        if ($bPshy) {
            goto rMuYh;
        }
        goto HnlPv;
        MtyDh:
        pwK2j:
        goto FNTDS;
        Fdork:
        $data["success"] = 1;
        goto T0ynO;
        bAps9:
        goto aNK5X;
        goto nITS1;
        a2KtV:
        $data = array("uniacid" => $_W["uniacid"], "order_id" => $order, "uid" => $user["id"], "openid" => $_GPC["openid"], "pid" => $_GPC["id"], "thumb" => $pro["thumb"], "product" => $pro["title"], "price" => $_GPC["price"], "num" => $_GPC["count"], "yhq" => $_GPC["youhui"], "true_price" => $_GPC["zhifu"], "creattime" => time(), "flag" => 0, "pro_user_name" => $_GPC["pro_name"], "pro_user_tel" => $_GPC["pro_tel"], "pro_user_txt" => $_GPC["pro_txt"], "overtime" => $overtime, "coupon" => $_GPC["yhqid"]);
        goto CBqyp;
        VxYq3:
        goto sih3A;
        goto Om8XW;
        ltvjt:
        $yX0F3 = !$myorders;
        goto CYBJ5;
        No9FH:
        $absnum = abs($new_num);
        goto Y2AS4;
        tmoja:
        vRiKK:
        goto No9FH;
        QBjdn:
        $DHJ22 = !($flags && $pro["pro_kc"] < 0);
        goto UWP2R;
        wgOo0:
        GVLOF:
        goto w2sUx;
        rrowz:
        $xlv9Z = !($pro["is_more"] == 1);
        goto UjC9h;
        pFo0l:
        vJpXS:
        goto yau10;
        ISoiH:
        $oinfo = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE order_id = :order and uniacid = :uniacid", array(":order" => $orderid, ":uniacid" => $uniacid));
        goto HTjaq;
        nXtB6:
        goto vJpXS;
        goto STco5;
        k8HYz:
        goto iBAOt;
        goto MtyDh;
        UWP2R:
        if ($DHJ22) {
            goto wKD88;
        }
        goto RI2Uw;
        gGqev:
        $ZNQam = !($num > $pro["pro_kc"]);
        goto IIGSg;
        tpCCR:
        $cydat = $_GPC["chuydate"] . " " . $_GPC["chuytime"];
        goto t0M1s;
        o2Orv:
        goto YIKxn;
        goto tmoja;
        AtHzr:
        $order = $_GPC["order"];
        goto cddlk;
        XUPdD:
        $order = $_GPC["order"];
        goto YGDZM;
        hlcw6:
        foreach ($newnum as $rec) {
            goto OAUXb;
            fjZrv:
            WARpz:
            goto isLev;
            OAUXb:
            $nnn = explode(":", $rec);
            goto zTN7W;
            zTN7W:
            $numarr[] = $nnn[1];
            goto fjZrv;
            isLev:
        }
        goto klMx6;
        FtaiM:
        if ($yLt2K) {
            goto JVoF9;
        }
        goto a69TV;
        NfWq0:
        FX5Ms:
        goto nthfv;
        CYBJ5:
        if ($yX0F3) {
            goto h3iU8;
        }
        goto CMjtr;
        rSUSw:
        $now = time();
        goto yUxWJ;
        xoznv:
        $myorders = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE pid = :pid and openid = :openid and uniacid = :uniacid and flag>=0", array(":pid" => $pid, ":openid" => $openid, ":uniacid" => $uniacid));
        goto RRihH;
        nITS1:
        scbn8:
        goto q5Gn3;
        lUGZx:
        $flags = true;
        goto pKjlt;
        mDUgc:
        $m_HD1 = !($pro["pro_kc"] + $my_num != 0);
        goto yusvA;
        pKjlt:
        $syl = $pro["pro_kc"];
        goto pFo0l;
        AR0aB:
        $newnum = $num - $onum;
        goto jSRI5;
        KxNE_:
        $now = time();
        goto cT9G1;
        IIGSg:
        if ($ZNQam) {
            goto Rt0qy;
        }
        goto nTL6b;
        NyqOQ:
        $pro = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_products") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $uniacid));
        goto e_yKE;
        QlVVU:
        foreach ($guig as $key => &$rec) {
            $rec[] = $numarr[$key];
            rJMZN:
        }
        goto fbEBj;
        i05i0:
        $pid = $orders["pid"];
        goto xoznv;
        tS2nD:
        fw_VD:
        goto BQWjk;
        mvX12:
        if ($L7C0J) {
            goto CXDZ4;
        }
        goto LU4vB;
        As31R:
        $order = $_GPC["order"];
        goto o3M7k;
        pDT7G:
        $AIUzP = $flags;
        goto J5mcB;
        cT9G1:
        $order = date("Y", $now) . date("m", $now) . date("d", $now) . date("H", $now) . date("i", $now) . date("s", $now) . rand(1000, 9999);
        goto f92Pi;
        nWdkg:
        $flags = true;
        goto abP0d;
        Zhw0E:
        $L7C0J = !($pro["pro_kc"] + $my_num == 0);
        goto mvX12;
        RdizC:
        $data = array("uniacid" => $_W["uniacid"], "order_id" => $order, "uid" => $user["id"], "openid" => $_GPC["openid"], "pid" => $_GPC["id"], "thumb" => $pro["thumb"], "product" => $pro["title"], "price" => $_GPC["price"], "num" => $_GPC["count"], "yhq" => $_GPC["youhui"], "true_price" => $_GPC["zhifu"], "creattime" => time(), "flag" => 0, "pro_user_name" => $_GPC["pro_name"], "pro_user_tel" => $_GPC["pro_tel"], "pro_user_txt" => $_GPC["pro_txt"], "overtime" => $overtime, "coupon" => $_GPC["yhqid"]);
        goto UAh2i;
        STco5:
        bNMyu:
        goto lUGZx;
        GCnJv:
        $onum = $oinfo["num"];
        goto AR0aB;
        CMjtr:
        foreach ($myorders as $res) {
            $my_num += $res["num"];
            V1hDA:
        }
        goto B4ulm;
        CLQOx:
        $bPshy = !$res;
        goto gl2BI;
        utSuD:
        Rt0qy:
        goto Qb0Et;
        MBVxo:
        $cha = $ddnum - $num;
        goto UZzxB;
        Y2AS4:
        $WFDdn = !($my_num + $absnum > $pro["pro_xz"] || $absnum > $pro["pro_kc"]);
        goto tE3BP;
        jSRI5:
        $ndata["pro_kc"] = $pro["pro_kc"] - $newnum;
        goto hGMDN;
        WkO4Y:
        $newnum = explode(",", substr($num, 1, strlen($num) - 2));
        goto ABTDJ;
        Om8XW:
        RANJ6:
        goto gGqev;
        v950D:
        $nS9XS = !($flags && $pro["pro_kc"] >= 0);
        goto iUw3k;
        o3M7k:
        mud3H:
        goto j6o5A;
        obFHU:
        $num = $_GPC["count"];
        goto aoRjo;
        HF7w3:
        wKD88:
        goto pDT7G;
        w2sUx:
        $res = pdo_update("sudu8_page_order", $data, array("order_id" => $order, "uniacid" => $uniacid));
        goto AsISj;
        yau10:
        iBAOt:
        goto v950D;
        gtkUd:
        $id = $_GPC["id"];
        goto wecAm;
        ABTDJ:
        $numarr = array();
        goto hlcw6;
        lYjxT:
        if ($new_num < 0) {
            goto vRiKK;
        }
        goto LcUew;
        yFI4i:
        if (!$orderid) {
            goto pwK2j;
        }
        goto ISoiH;
        nFbNi:
        if ($bPshy) {
            goto QX6CT;
        }
        goto Fdork;
        Yoxmx:
        global $_GPC, $_W;
        goto kxYYs;
        AsISj:
        TwHjH:
        goto JuLdH;
        Ytdip:
        qmpjY:
        goto xj3Xc;
        RI2Uw:
        if ($_GPC["order"]) {
            goto T7DtT;
        }
        goto rSUSw;
        JN1YT:
        $now = time();
        goto Gfi3S;
        G50BA:
        goto TwHjH;
        goto wgOo0;
        nTL6b:
        $syl = $pro["pro_kc"];
        goto dG0rD;
        LcUew:
        $flags = true;
        goto o2Orv;
        f92Pi:
        $ndata["pro_kc"] = $pro["pro_kc"] - $num;
        goto ZOmwD;
        klMx6:
        lI7Pz:
        goto vPW0Y;
        TvznH:
        $pqfAW = !($my_num + $num > $pro["pro_xz"] || $num > $pro["pro_kc"]);
        goto hi_0g;
        BQWjk:
        $openid = $_GPC["openid"];
        goto i05i0;
        Y5bIi:
        if ($_GPC["order"]) {
            goto Rcj91;
        }
        goto PP3YH;
        gl2BI:
        if ($bPshy) {
            goto eVnf4;
        }
        goto MPr0K;
        PI8xD:
        wOQuc:
        goto k8HYz;
        t0M1s:
        $zzcy = strtotime($cydat);
        goto Ir4X0;
        QXQjW:
        $data["xg"] = $pro["pro_xz"];
        goto Yilbc;
        M7FxL:
        u5U6d:
        goto nXtB6;
        J5mcB:
        if ($AIUzP) {
            goto qmpjY;
        }
        goto xmIPp;
        mCil7:
        $overtime = time() + 30 * 60;
        goto a2KtV;
        WjhPN:
        return $this->result(0, "success", $data);
        goto ZZKL5;
        yusvA:
        if ($m_HD1) {
            goto u5U6d;
        }
        goto gQw4B;
        UAh2i:
        if ($_GPC["order"]) {
            goto scbn8;
        }
        goto f_LOg;
        B4ulm:
        BESsl:
        goto T5ZbZ;
        vPW0Y:
        $guig = unserialize($pro["more_type_x"]);
        goto QlVVU;
        ARXou:
        if ($pro["pro_kc"] == -1) {
            goto ySqjv;
        }
        goto MBVxo;
        cddlk:
        if ($_GPC["order"]) {
            goto Mcl45;
        }
        goto JN1YT;
        Yilbc:
        return $this->result(0, "success", $data);
        goto dW0Ry;
        T0ynO:
        $data["xg"] = $pro["pro_xz"];
        goto WjhPN;
        NmpJV:
        goto mud3H;
        goto Hc7gH;
        gQw4B:
        if ($pro["pro_xz"] == 0) {
            goto RANJ6;
        }
        goto TvznH;
        OupI9:
        Mcl45:
        goto XUPdD;
        tE3BP:
        if ($WFDdn) {
            goto FX5Ms;
        }
        goto B61AG;
        PZbCz:
        Rcj91:
        goto aJCW0;
        Gfi3S:
        $order = date("Y", $now) . date("m", $now) . date("d", $now) . date("H", $now) . date("i", $now) . date("s", $now) . rand(1000, 9999);
        goto KXJ1G;
        uP3od:
        return $this->result(0, "error", $data);
        goto Ytdip;
        q5Gn3:
        $res = pdo_update("sudu8_page_order", $data, array("order_id" => $order, "uniacid" => $uniacid));
        goto UhHbC;
        f83Ya:
        goto TiJbh;
        goto PZbCz;
        xkiJ_:
        if ($_GPC["order"]) {
            goto FcW8x;
        }
        goto KxNE_;
        aoRjo:
        $orderid = $_GPC["order"];
        goto yFI4i;
        VKaP1:
        $syl = $pro["pro_kc"];
        goto Ry9u1;
        ZOmwD:
        pdo_update("sudu8_page_products", $ndata, array("id" => $id, "uniacid" => $uniacid));
        goto BVUd5;
        ZZKL5:
        QX6CT:
        goto nZRXZ;
        JuLdH:
        $bPshy = !$res;
        goto nFbNi;
        f_LOg:
        $res = pdo_insert("sudu8_page_order", $data);
        goto bAps9;
        nZRXZ:
        hi8jF:
        goto QBjdn;
        T5ZbZ:
        h3iU8:
        goto obFHU;
        UhHbC:
        aNK5X:
        goto emW8X;
        GuvR4:
        return $this->result(0, "success", $data);
        goto Z3neB;
        BVUd5:
        goto goX49;
        goto q2mFs;
        NUCwG:
        $num = $_GPC["num"];
        goto WkO4Y;
        xmIPp:
        $data["success"] = 0;
        goto ytMaS;
        pcb3T:
        rcyF0:
        goto VxYq3;
        CBqyp:
        if ($_GPC["order"]) {
            goto GVLOF;
        }
        goto wIdKR;
        MPr0K:
        $data["success"] = 1;
        goto q4tTU;
        emW8X:
        $bPshy = !$res;
        goto OT_OP;
        hi_0g:
        if ($pqfAW) {
            goto rcyF0;
        }
        goto VKaP1;
        RRihH:
        $my_num = 0;
        goto ltvjt;
        dW0Ry:
        rMuYh:
        goto HF7w3;
        emOO3:
        $flags = false;
        goto nl1Xb;
        KXJ1G:
        goto E2aCv;
        goto OupI9;
        SPwVX:
        $flags = false;
        goto NfWq0;
        ELsyp:
        $user = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_user") . " WHERE openid = :id and uniacid = :uniacid", array(":id" => $_GPC["openid"], ":uniacid" => $uniacid));
        goto NyqOQ;
        tpl22:
        $newgg = serialize($guig);
        goto AtHzr;
        q4tTU:
        $data["xg"] = $pro["pro_xz"];
        goto GuvR4;
        c4_P5:
        goX49:
        goto mCil7;
        Ry9u1:
        $flags = false;
        goto pcb3T;
        cwxVt:
        ySqjv:
        goto nWdkg;
        ytMaS:
        $data["syl"] = $syl;
        goto fQb8j;
        Z3neB:
        eVnf4:
        goto tS2nD;
        Ia3OV:
        $oinfo = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE order_id = :order and uniacid = :uniacid", array(":order" => $order, ":uniacid" => $uniacid));
        goto GCnJv;
        iUw3k:
        if ($nS9XS) {
            goto hi8jF;
        }
        goto xkiJ_;
        kxYYs:
        $uniacid = $_W["uniacid"];
        goto gtkUd;
        a69TV:
        $more_type_num = unserialize($pro["more_type_num"]);
        goto A_w9I;
        nl1Xb:
        CXDZ4:
        goto mDUgc;
        Hc7gH:
        T7DtT:
        goto As31R;
        aJCW0:
        $res = pdo_update("sudu8_page_order", $data, array("order_id" => $order, "uniacid" => $uniacid));
        goto EMULy;
        nthfv:
        YIKxn:
        goto F1M9_;
        fQb8j:
        $data["id"] = $id;
        goto uP3od;
        yUxWJ:
        $order = date("Y", $now) . date("m", $now) . date("d", $now) . date("H", $now) . date("i", $now) . date("s", $now) . rand(1000, 9999);
        goto NmpJV;
        Ir4X0:
        $data = array("uniacid" => $_W["uniacid"], "order_id" => $order, "uid" => $user["id"], "openid" => $_GPC["openid"], "pid" => $_GPC["id"], "thumb" => $pro["thumb"], "product" => $pro["title"], "yhq" => $_GPC["youhui"], "true_price" => $_GPC["zhifu"], "creattime" => time(), "flag" => 0, "pro_user_name" => $_GPC["pro_name"], "pro_user_tel" => $_GPC["pro_tel"], "pro_user_txt" => $_GPC["pro_txt"], "overtime" => $zzcy, "is_more" => 1, "order_duo" => $newgg, "coupon" => $_GPC["yhqid"]);
        goto Y5bIi;
        HTjaq:
        $ddnum = $oinfo["num"];
        goto ARXou;
        UjC9h:
        if ($xlv9Z) {
            goto fw_VD;
        }
        goto NUCwG;
        abP0d:
        $syl = $pro["pro_kc"];
        goto PI8xD;
        wecAm:
        $flags = true;
        goto ELsyp;
        j6o5A:
        $overtime = time() + 30 * 60;
        goto RdizC;
        PP3YH:
        $res = pdo_insert("sudu8_page_order", $data);
        goto f83Ya;
        A_w9I:
        JVoF9:
        goto rrowz;
        YGDZM:
        E2aCv:
        goto tpCCR;
        LU4vB:
        $syl = 0;
        goto emOO3;
        dG0rD:
        $flags = false;
        goto utSuD;
        e_yKE:
        $yLt2K = !$pro["more_type_num"];
        goto FtaiM;
        EMULy:
        TiJbh:
        goto CLQOx;
        fbEBj:
        Z8fai:
        goto tpl22;
        FNTDS:
        if ($pro["pro_kc"] == -1) {
            goto bNMyu;
        }
        goto Zhw0E;
        wIdKR:
        $res = pdo_insert("sudu8_page_order", $data);
        goto G50BA;
        B61AG:
        $syl = $pro["pro_kc"];
        goto SPwVX;
        UZzxB:
        $new_num = $my_num - $cha;
        goto lYjxT;
        q2mFs:
        FcW8x:
        goto AyzxU;
        hGMDN:
        pdo_update("sudu8_page_products", $ndata, array("id" => $id, "uniacid" => $uniacid));
        goto c4_P5;
        xj3Xc:
    }
    public function doPageOrderinfo()
    {
        goto dEifE;
        uPsTL:
        foreach ($orders_l as $rec) {
            $sale_num += $rec["num"];
            lQyL7:
        }
        goto uYJqp;
        ffR52:
        $kdata["reback"] = 1;
        goto aGllR;
        LUpcX:
        $openid = $_GPC["openid"];
        goto r0EDQ;
        k35Gu:
        if ($JgEme) {
            goto uwEeJ;
        }
        goto ngA2g;
        uYJqp:
        dZlGU:
        goto ALsdO;
        wYkwb:
        $new_orders["pro_flag_tel"] = $pro["pro_flag_tel"];
        goto sc15z;
        tyfZ3:
        $new_orders["pro_kc"] = $pro["pro_kc"];
        goto wFGRr;
        czFLO:
        hYiT6:
        goto ryCrC;
        CdUTp:
        goto hYiT6;
        goto NKyqr;
        x7xGl:
        $uniacid = $_W["uniacid"];
        goto gwAyj;
        GX4Zv:
        $new_orders["pro_flag_time"] = $pro["pro_flag_time"];
        goto NJo4Y;
        Ketdh:
        if ($llrV4) {
            goto mrTmh;
        }
        goto uPsTL;
        OHkjk:
        $f9V02 = !($now > $orders["overtime"] && $orders["flag"] == 0);
        goto iqlq7;
        p1QqV:
        GkoIF:
        goto Sd25P;
        lPsNR:
        if ($xlv9Z) {
            goto b6pR7;
        }
        goto Ll6K5;
        ByAIL:
        $cdd = count($myorders);
        goto GAa2J;
        mBIr9:
        $JgEme = !($now > $orders["overtime"] && $orders["reback"] == 0 && $orders["flag"] == 0);
        goto qkQhA;
        Sd25P:
        b6pR7:
        goto YFWRP;
        Ll6K5:
        $now = time();
        goto OHkjk;
        DJewZ:
        o5mNA:
        goto A0xxg;
        ASBUl:
        $my_num = 0;
        goto oIhSn;
        LkwHI:
        $new_orders["more_type_num"] = unserialize($pro["more_type_num"]);
        goto zLQy_;
        I3Vma:
        $new_orders["mcount"] = $cdd;
        goto PFEjK;
        r0EDQ:
        $pid = $new_orders["pid"];
        goto FOj1I;
        oz2BT:
        ERrHs:
        goto RBuEN;
        iqlq7:
        if ($f9V02) {
            goto GkoIF;
        }
        goto FG5Yv;
        sSFek:
        goto o5mNA;
        goto t4ctu;
        fNC0q:
        if ($pro["pro_kc"] >= 0 && $pro["is_more"] == 0) {
            goto TCQ0K;
        }
        goto zXDjk;
        qCJTx:
        pdo_update("sudu8_page_products", $ndata, array("id" => $orders["pid"], "uniacid" => $uniacid));
        goto UXSJ6;
        GAa2J:
        $orders_l = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE pid = :pid and uniacid = :uniacid and flag>0", array(":pid" => $pid, ":uniacid" => $uniacid));
        goto xfurL;
        FOj1I:
        $myorders = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE pid = :pid and openid = :openid and uniacid = :uniacid and flag>=0", array(":pid" => $pid, ":openid" => $openid, ":uniacid" => $uniacid));
        goto ASBUl;
        xsb52:
        $now = time();
        goto mBIr9;
        gwAyj:
        $id = $_GPC["order"];
        goto Hym5K;
        JaFKg:
        return $this->result(0, "success", $new_orders);
        goto jg878;
        zLQy_:
        $new_orders["couponid"] = $new_orders["coupon"];
        goto vnJwM;
        aGllR:
        pdo_update("sudu8_page_order", $kdata, array("order_id" => $id, "uniacid" => $uniacid));
        goto Z6jBZ;
        zGhUZ:
        $new_orders["sale_num"] = $new_orders["sale_num"] + $sale_num;
        goto o2NWF;
        kBL3V:
        $kdata["reback"] = 1;
        goto dztQ0;
        NJo4Y:
        $new_orders["pro_flag_ding"] = $pro["pro_flag_ding"];
        goto tyfZ3;
        lNwyB:
        $pro = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_products") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $orders["pid"], ":uniacid" => $uniacid));
        goto tge67;
        RBuEN:
        WxXkH:
        goto ByAIL;
        yj9Jk:
        $onum = $orders["num"];
        goto eih84;
        ngA2g:
        $kdata["flag"] = -1;
        goto kBL3V;
        eih84:
        $kdata["flag"] = -1;
        goto ffR52;
        sc15z:
        $new_orders["pro_flag_data"] = $pro["pro_flag_data"];
        goto yrggB;
        GTFwU:
        $new_orders["chuydate"] = date("Y-m-d", $new_orders["overtime"]);
        goto pRsjA;
        pDPzY:
        $JgEme = !($now > $orders["overtime"] && $orders["reback"] == 0 && $orders["flag"] == 0);
        goto k35Gu;
        A0xxg:
        $new_orders["my_num"] = $my_num;
        goto I3Vma;
        kOqM9:
        goto hYiT6;
        goto rN0_s;
        JuA9y:
        $mycoupon = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_coupon_user") . " WHERE id = :cid and uniacid = :uniacid", array(":cid" => $new_orders["coupon"], ":uniacid" => $uniacid));
        goto JGcS0;
        zXDjk:
        if ($pro["pro_kc"] < 0 && $pro["is_more"] == 0) {
            goto zJWKh;
        }
        goto kOqM9;
        rN0_s:
        TCQ0K:
        goto xsb52;
        dEifE:
        global $_GPC, $_W;
        goto x7xGl;
        XlpaE:
        $now = time();
        goto pDPzY;
        vld5Z:
        if ($yX0F3) {
            goto WxXkH;
        }
        goto lwBNL;
        Hym5K:
        $orders = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE order_id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $uniacid));
        goto lNwyB;
        rvW19:
        $new_orders["isover"] = 0;
        goto sSFek;
        JGcS0:
        $coupon = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_coupon") . " WHERE id = :cid and uniacid = :uniacid", array(":cid" => $mycoupon["cid"], ":uniacid" => $uniacid));
        goto LUpcX;
        ryCrC:
        $xlv9Z = !($pro["is_more"] == 1);
        goto lPsNR;
        UXSJ6:
        nWOR4:
        goto CdUTp;
        HxJBk:
        $new_orders["isover"] = 1;
        goto DJewZ;
        NKyqr:
        zJWKh:
        goto XlpaE;
        tge67:
        $orders["thumb"] = HTTPSHOST . $orders["thumb"];
        goto fNC0q;
        YFWRP:
        $new_orders = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE order_id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $uniacid));
        goto JuA9y;
        xnRSk:
        $llrV4 = !$orders_l;
        goto Ketdh;
        FG5Yv:
        $kdata["flag"] = -1;
        goto ihPwx;
        ALsdO:
        mrTmh:
        goto zGhUZ;
        uM7V6:
        uwEeJ:
        goto czFLO;
        PFEjK:
        $new_orders["pro_flag"] = $pro["pro_flag"];
        goto wYkwb;
        Z6jBZ:
        $ndata["pro_kc"] = $pro["pro_kc"] + $onum;
        goto qCJTx;
        ihPwx:
        pdo_update("sudu8_page_order", $kdata, array("order_id" => $id, "uniacid" => $uniacid));
        goto p1QqV;
        yrggB:
        $new_orders["pro_flag_data_name"] = $pro["pro_flag_data_name"];
        goto GX4Zv;
        QlkDT:
        $overtime = $orders["overtime"];
        goto bto7b;
        pRsjA:
        $new_orders["chuytime"] = date("H:i", $new_orders["overtime"]);
        goto LkwHI;
        vnJwM:
        $new_orders["coupon"] = $coupon;
        goto JaFKg;
        wFGRr:
        $new_orders["pro_xz"] = $pro["pro_xz"];
        goto ayE4Q;
        oIhSn:
        $yX0F3 = !$myorders;
        goto vld5Z;
        xfurL:
        $sale_num = 0;
        goto xnRSk;
        lwBNL:
        foreach ($myorders as $res) {
            $my_num += $res["num"];
            GeRGC:
        }
        goto oz2BT;
        Xbsr8:
        $new_orders["more_type_x"] = unserialize($new_orders["order_duo"]);
        goto GTFwU;
        t4ctu:
        gm9jJ:
        goto HxJBk;
        o2NWF:
        $now = time();
        goto QlkDT;
        dztQ0:
        pdo_update("sudu8_page_order", $kdata, array("order_id" => $id, "uniacid" => $uniacid));
        goto uM7V6;
        ayE4Q:
        $new_orders["thumb"] = HTTPSHOST . $new_orders["thumb"];
        goto Xbsr8;
        bto7b:
        if ($now > $overtime) {
            goto gm9jJ;
        }
        goto rvW19;
        qkQhA:
        if ($JgEme) {
            goto nWOR4;
        }
        goto yj9Jk;
        jg878:
    }
    public function doPageorderpayover()
    {
        goto OY7rp;
        kpi5e:
        $numarr = array();
        goto UXcGe;
        QJmKs:
        IwCJ6:
        goto PWlxX;
        yPMjS:
        $cdata["reback"] = 1;
        goto sAaO5;
        IKyit:
        $coupondata = array("flag" => 1);
        goto YSktQ;
        ZiUKw:
        if ($pro["pro_kc"] >= 0 && $pro["is_more"] == 0) {
            goto y06I0;
        }
        goto O35qe;
        Llu_9:
        pdo_update("sudu8_page_products", $prodata, array("id" => $pid, "uniacid" => $uniacid));
        goto XJ9VW;
        x15T2:
        $data = array("flag" => 1);
        goto KsF4S;
        PrYDv:
        return $this->result(0, "success", 1);
        goto vzxll;
        NYPdG:
        y06I0:
        goto bVFYE;
        O4vv0:
        $bPshy = !$res;
        goto rP2o2;
        tTiE9:
        QydSy:
        goto l5a2M;
        IKdu1:
        $data = array("flag" => 3);
        goto fpGID;
        ZQ9N3:
        pdo_update("sudu8_page_products", $ndata, array("id" => $orders["pid"], "uniacid" => $uniacid));
        goto yPMjS;
        fpGID:
        t5qWC:
        goto hYgUh;
        AbVZ_:
        $more_type_num = unserialize($pro["more_type_num"]);
        goto AEFV9;
        auVnr:
        $data = array("flag" => -2);
        goto Lq56x;
        PWlxX:
        ed6j2:
        goto ZiUKw;
        XJ9VW:
        $yyfOX = !($pro["pro_flag_ding"] == 0);
        goto pJ7he;
        O35qe:
        if ($pro["pro_kc"] < 0 && $pro["is_more"] == 0) {
            goto zb5NJ;
        }
        goto Tdxp6;
        z87BO:
        $data = array("flag" => 1);
        goto xNG9S;
        FJ4vQ:
        if ($xlv9Z) {
            goto ed6j2;
        }
        goto pEr20;
        D8nqs:
        $xlv9Z = !($pro["is_more"] == 1);
        goto FJ4vQ;
        IzZ_Z:
        AwJW9:
        goto auVnr;
        DqR_s:
        $pro = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_products") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $orders["pid"], ":uniacid" => $uniacid));
        goto IKyit;
        YSktQ:
        pdo_update("sudu8_page_coupon_user", $coupondata, array("id" => $orders["coupon"], "uniacid" => $uniacid));
        goto D8nqs;
        tH4ig:
        $xvwT5 = !($orders["reback"] == 0);
        goto LGE09;
        fE83L:
        eA4RD:
        goto tH4ig;
        wUz6Z:
        goto FsNdY;
        goto PLvDq;
        OY7rp:
        global $_GPC, $_W;
        goto bpFMA;
        F1dyx:
        nB95T:
        goto z3lYx;
        aujCD:
        return $this->result(0, "error", $data);
        goto Hcf4M;
        KsF4S:
        $res = pdo_update("sudu8_page_order", $data, array("order_id" => $id, "uniacid" => $uniacid));
        goto uaofM;
        pJ7he:
        if ($yyfOX) {
            goto nB95T;
        }
        goto j3COc;
        uaofM:
        FsNdY:
        goto O4vv0;
        Tdxp6:
        goto FsNdY;
        goto NYPdG;
        j3COc:
        $data = array("flag" => 1);
        goto F1dyx;
        EFad2:
        if ($Nvz3H) {
            goto t5qWC;
        }
        goto IKdu1;
        UXcGe:
        foreach ($num as $res) {
            array_push($numarr, $res[5]);
            CGnYn:
        }
        goto wPJGs;
        PLvDq:
        zb5NJ:
        goto x15T2;
        wPJGs:
        O9lub:
        goto Kvela;
        bVFYE:
        $now = time();
        goto did85;
        sAaO5:
        pdo_update("sudu8_page_order", $cdata, array("order_id" => $id, "uniacid" => $uniacid));
        goto IzZ_Z;
        did85:
        if ($orders["overtime"] < $now) {
            goto eA4RD;
        }
        goto z87BO;
        zQrxj:
        $prodata["more_type_num"] = serialize($more_type_num);
        goto Llu_9;
        Hcf4M:
        goto IwCJ6;
        goto E6NeP;
        w3flL:
        $ndata["pro_kc"] = $pro["pro_kc"] + $orders["num"];
        goto ZQ9N3;
        Kvela:
        foreach ($more_type_num as $key => &$value) {
            goto CQk3x;
            rf_zw:
            HYX6b:
            goto l6Knu;
            cysbV:
            goto Z2Tr4;
            goto rf_zw;
            cBpGi:
            biUeb:
            goto N6Yxj;
            CQk3x:
            if ($value["shennum"] >= $numarr[$key]) {
                goto HYX6b;
            }
            goto mbSpY;
            fZRxq:
            $value["salenum"] = $value["salenum"] + $numarr[$key];
            goto OVpuf;
            ASmGT:
            Z2Tr4:
            goto cBpGi;
            mbSpY:
            $duock = 0;
            goto cysbV;
            OVpuf:
            $duock = 1;
            goto ASmGT;
            l6Knu:
            $value["shennum"] = $value["shennum"] - $numarr[$key];
            goto fZRxq;
            N6Yxj:
        }
        goto tTiE9;
        hYgUh:
        $res = pdo_update("sudu8_page_order", $data, array("order_id" => $_GPC["order_id"], "uniacid" => $uniacid));
        goto QJmKs;
        vzxll:
        GvEOc:
        goto ZrRJq;
        Wp1ZK:
        $id = $_GPC["order_id"];
        goto O4Dlh;
        l5a2M:
        if ($duock == 1) {
            goto qcZFZ;
        }
        goto aujCD;
        AEFV9:
        $num = unserialize($orders["order_duo"]);
        goto kpi5e;
        O4Dlh:
        $orders = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE order_id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $uniacid));
        goto DqR_s;
        bpFMA:
        $uniacid = $_W["uniacid"];
        goto Wp1ZK;
        hLslh:
        $pid = $orders["pid"];
        goto zQrxj;
        z3lYx:
        $Nvz3H = !($pro["pro_flag_ding"] == 1);
        goto EFad2;
        xNG9S:
        goto kkUU7;
        goto fE83L;
        Lq56x:
        kkUU7:
        goto nWy_3;
        rP2o2:
        if ($bPshy) {
            goto GvEOc;
        }
        goto PrYDv;
        nWy_3:
        $res = pdo_update("sudu8_page_order", $data, array("order_id" => $id, "uniacid" => $uniacid));
        goto wUz6Z;
        pEr20:
        $duock = 0;
        goto AbVZ_;
        E6NeP:
        qcZFZ:
        goto hLslh;
        LGE09:
        if ($xvwT5) {
            goto AwJW9;
        }
        goto w3flL;
        ZrRJq:
    }
    public function doPageDpass()
    {
        goto BeGUA;
        i8nW4:
        WIg4g:
        goto TGw0U;
        qXZ9f:
        $id = $_GPC["order"];
        goto ZmsGV;
        Ggi4d:
        $orders = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE order_id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $uniacid));
        goto heokK;
        ZmsGV:
        $data = array("flag" => 1);
        goto Ggi4d;
        g1com:
        $kc = $pro["pro_kc"];
        goto mTyAj;
        BeGUA:
        global $_GPC, $_W;
        goto KPb_7;
        e6Q72:
        $num = $orders["num"];
        goto fw8oF;
        W12GI:
        MqFDG:
        goto hQRkz;
        va6EY:
        return $this->result(0, "success", 1);
        goto i8nW4;
        hQRkz:
        $res = pdo_delete("sudu8_page_order", array("order_id" => $id, "uniacid" => $uniacid));
        goto iL7eV;
        mTyAj:
        $ndata["pro_kc"] = $num + $kc;
        goto Q1HNr;
        KPb_7:
        $uniacid = $_W["uniacid"];
        goto qXZ9f;
        NBR8l:
        $pro = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_products") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $pid, ":uniacid" => $uniacid));
        goto PCCE7;
        fw8oF:
        $pid = $orders["pid"];
        goto NBR8l;
        Q1HNr:
        pdo_update("sudu8_page_products", $ndata, array("id" => $pid, "uniacid" => $uniacid));
        goto lDGSt;
        j3O9D:
        $over = $orders["overtime"];
        goto d4BQM;
        lDGSt:
        DPLB1:
        goto W12GI;
        FEV9N:
        $a2fNv = !($flag == 0 && $over > $now);
        goto f1NEa;
        heokK:
        $now = time();
        goto j3O9D;
        HBph0:
        if ($Jsw70) {
            goto MqFDG;
        }
        goto FEV9N;
        e1P73:
        if ($bPshy) {
            goto WIg4g;
        }
        goto va6EY;
        iL7eV:
        $bPshy = !$res;
        goto e1P73;
        d4BQM:
        $flag = $orders["flag"];
        goto e6Q72;
        PCCE7:
        $Jsw70 = !((int) $pro["pro_kc"] >= 0);
        goto HBph0;
        f1NEa:
        if ($a2fNv) {
            goto DPLB1;
        }
        goto g1com;
        TGw0U:
    }
    public function doPageMyorder()
    {
        goto R6nBo;
        mMTh9:
        mh9Io:
        goto Rv_dJ;
        QUTtL:
        $psize = 10;
        goto bHL_9;
        R6nBo:
        global $_GPC, $_W;
        goto LaTrw;
        pO1As:
        $hzWoz = !($type != 9);
        goto EsiAh;
        laHy2:
        $OrdersList["allnum"] = count($alls);
        goto kWnoj;
        Rv_dJ:
        $alls = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_order") . "WHERE uniacid = :uniacid and openid = :openid", array(":uniacid" => $uniacid, ":openid" => $openid));
        goto laHy2;
        EsiAh:
        if ($hzWoz) {
            goto jRTJI;
        }
        goto xPN6W;
        nCxiq:
        $openid = $_GPC["openid"];
        goto mF1rS;
        wSw8p:
        $where = '';
        goto pO1As;
        LaTrw:
        $uniacid = $_W["uniacid"];
        goto nCxiq;
        QxBgf:
        foreach ($OrdersList["list"] as &$row) {
            $row["thumb"] = HTTPSHOST . $row["thumb"];
            khg4C:
        }
        goto mMTh9;
        bHL_9:
        $OrdersList["list"] = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_order") . "WHERE uniacid = :uniacid and openid = :openid " . $where . " ORDER BY creattime DESC,flag  LIMIT " . ($pindex - 1) * $psize . "," . $psize, array(":uniacid" => $uniacid, ":openid" => $openid));
        goto QxBgf;
        mF1rS:
        $type = $_GPC["type"];
        goto wSw8p;
        FOcTx:
        jRTJI:
        goto FwXJH;
        kWnoj:
        return $this->result(0, "success", $OrdersList);
        goto rAntb;
        FwXJH:
        $pindex = max(1, intval($_GPC["page"]));
        goto QUTtL;
        xPN6W:
        $where = " and flag = " . $type;
        goto FOcTx;
        rAntb:
    }
    public function doPageweixinpay()
    {
        goto QTWCw;
        hFU0Z:
        include "WeixinPay.php";
        goto j_mwq;
        dPbg4:
        $return = $weixinpay->pay();
        goto L26kr;
        ryrkW:
        $total_fee = $_GPC["price"] * 100;
        goto mXxUA;
        v2deo:
        if ($Ew8jM) {
            goto ZHUpc;
        }
        goto kcpiX;
        kc4Jy:
        otG8y:
        goto HfCJ9;
        rnSbJ:
        $key = $datas["wechat"]["signkey"];
        goto y6X57;
        HaQfp:
        return $this->result(0, "error", $data);
        goto lBZI3;
        HfCJ9:
        $duock = 0;
        goto ghwEy;
        ao_0N:
        goto Nib9w;
        goto ztCkS;
        jh0xk:
        $orders = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_order") . " WHERE order_id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $uniacid));
        goto f3FwZ;
        o7kiS:
        $newtrueprice = $true_price + $yhjg;
        goto GQZR7;
        f3FwZ:
        $pro = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_products") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $orders["pid"], ":uniacid" => $uniacid));
        goto z53CP;
        AZ0cx:
        if ($duock == 1) {
            goto vsZkJ;
        }
        goto rliw6;
        NTPBV:
        pdo_update("sudu8_page_order", $dataorder, array("order_id" => $id, "uniacid" => $uniacid));
        goto yoAj3;
        eQ24z:
        $datas = unserialize($paycon["payment"]);
        goto hFU0Z;
        v1u14:
        $PmGIU = !($coupon["flag"] == 2);
        goto WiJUM;
        r2xz_:
        $user = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_user") . " WHERE openid = :id and uniacid = :uniacid", array(":id" => $_GPC["openid"], ":uniacid" => $uniacid));
        goto r0P2S;
        tT88e:
        $BMN4V = !($coupon["flag"] == 1);
        goto whNlx;
        EeOb3:
        goto uNzPP;
        goto aAfpG;
        r0P2S:
        $uid = $user["id"];
        goto TJH4y;
        rLzY2:
        JIUte:
        goto tT88e;
        HdWEs:
        Nib9w:
        goto Q1hzA;
        pWApQ:
        $out_trade_no = $_GPC["order_id"];
        goto sWM_v;
        jTLZC:
        $key = $datas["wechat"]["signkey"];
        goto pWApQ;
        FTVWx:
        $yhjg = $couponinfo["price"];
        goto OYvsq;
        VohSo:
        $true_price = $orders["true_price"];
        goto f00PH;
        WiJUM:
        if ($PmGIU) {
            goto JIUte;
        }
        goto q1fSe;
        aQQFk:
        $appid = $app["key"];
        goto Hxf3y;
        tAz8U:
        $body = "商品支付";
        goto ryrkW;
        vTdsV:
        $datas = unserialize($paycon["payment"]);
        goto QuQYK;
        y6X57:
        $out_trade_no = $_GPC["order_id"];
        goto tAz8U;
        z53CP:
        $more_type_num = unserialize($pro["more_type_num"]);
        goto klNTh;
        whNlx:
        if ($BMN4V) {
            goto Kr7kz;
        }
        goto tuhTT;
        j3NGZ:
        $couponinfo = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_coupon") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $coupon["cid"], ":uniacid" => $uniacid));
        goto v1u14;
        vQ1Ml:
        $total_fee = $_GPC["price"] * 100;
        goto b7gET;
        j_mwq:
        $appid = $app["key"];
        goto euDej;
        f00PH:
        $yhjg = $couponinfo["price"];
        goto o7kiS;
        Nisgs:
        $openid = $_GPC["openid"];
        goto r2xz_;
        ztCkS:
        qjCWJ:
        goto ux_wt;
        euDej:
        $openid = $_GPC["openid"];
        goto FNzbf;
        AUOUY:
        $dataorder = array("true_price" => $newtrueprice, "coupon" => 0);
        goto NTPBV;
        XKJsj:
        $mch_id = $datas["wechat"]["mchid"];
        goto jTLZC;
        lBZI3:
        Kr7kz:
        goto QlWaf;
        rliw6:
        $data = array("message" => "库存不足！");
        goto pGK9H;
        o5WIb:
        $app = pdo_fetch("SELECT * FROM " . tablename("account_wxapp") . " WHERE uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
        goto FwmZM;
        QlWaf:
        ZHUpc:
        goto njzHY;
        L26kr:
        return $this->result(0, "success", $return);
        goto ad_SH;
        dO21Q:
        $uniacid = $_W["uniacid"];
        goto xdNi8;
        b7gET:
        $weixinpay = new WeixinPay($appid, $openid, $mch_id, $key, $out_trade_no, $body, $total_fee);
        goto dPbg4;
        cAEr8:
        $return = $weixinpay->pay();
        goto h9hwu;
        tuhTT:
        $data = array("message" => "该优惠券已经使用过了！");
        goto VohSo;
        d9MlG:
        $app = pdo_fetch("SELECT * FROM " . tablename("account_wxapp") . " WHERE uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
        goto sY1P1;
        q0Dzc:
        foreach ($num as $res) {
            array_push($numarr, $res[5]);
            QfRkW:
        }
        goto kc4Jy;
        QSqHD:
        pdo_update("sudu8_page_order", $dataorder, array("order_id" => $id, "uniacid" => $uniacid));
        goto HaQfp;
        akB86:
        foreach ($yhqs as $key => &$res) {
            goto KmKvj;
            jeZ_o:
            if ($HzEm7) {
                goto RC5yu;
            }
            goto YCc3c;
            KmKvj:
            $HzEm7 = !($nowtime > $res["etime"]);
            goto jeZ_o;
            ebihC:
            pdo_update("sudu8_page_coupon_user", $updatas, array("id" => $res["id"], "uniacid" => $uniacid));
            goto BNoaT;
            BNoaT:
            RC5yu:
            goto bJlKf;
            bJlKf:
            XZAG1:
            goto cduK_;
            YCc3c:
            $updatas = array("flag" => 2);
            goto ebihC;
            cduK_:
        }
        goto MA5uE;
        Hxf3y:
        $openid = $_GPC["openid"];
        goto XKJsj;
        FNzbf:
        $mch_id = $datas["wechat"]["mchid"];
        goto rnSbJ;
        n37vg:
        $nowtime = time();
        goto akB86;
        MYV12:
        $true_price = $orders["true_price"];
        goto FTVWx;
        MA5uE:
        eJbtq:
        goto p0nbx;
        njzHY:
        if ($pro["is_more"] == 1) {
            goto qjCWJ;
        }
        goto o5WIb;
        mXxUA:
        $weixinpay = new WeixinPay($appid, $openid, $mch_id, $key, $out_trade_no, $body, $total_fee);
        goto cAEr8;
        h9hwu:
        return $this->result(0, "success", $return);
        goto ao_0N;
        p0nbx:
        $Ew8jM = !($orders["coupon"] != 0);
        goto v2deo;
        sY1P1:
        $paycon = pdo_fetch("SELECT * FROM " . tablename("uni_settings") . " WHERE uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
        goto vTdsV;
        QuQYK:
        include "WeixinPay.php";
        goto aQQFk;
        yoAj3:
        return $this->result(0, "error", $data);
        goto rLzY2;
        xYtvZ:
        Zf6Z7:
        goto AZ0cx;
        FwmZM:
        $paycon = pdo_fetch("SELECT * FROM " . tablename("uni_settings") . " WHERE uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
        goto eQ24z;
        kcpiX:
        $coupon = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_coupon_user") . " WHERE id = :id and uniacid = :uniacid", array(":id" => $orders["coupon"], ":uniacid" => $uniacid));
        goto j3NGZ;
        klNTh:
        $num = unserialize($orders["order_duo"]);
        goto Nisgs;
        q1fSe:
        $data = array("message" => "该优惠券已过期！");
        goto MYV12;
        ad_SH:
        uNzPP:
        goto HdWEs;
        xdNi8:
        $id = $_GPC["order_id"];
        goto jh0xk;
        OYvsq:
        $newtrueprice = $true_price + $yhjg;
        goto AUOUY;
        aAfpG:
        vsZkJ:
        goto d9MlG;
        TJH4y:
        $yhqs = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_coupon_user") . " WHERE uniacid = :uniacid and uid = :uid and flag = 0 and etime>0", array(":uniacid" => $_W["uniacid"], ":uid" => $uid));
        goto n37vg;
        ghwEy:
        foreach ($more_type_num as $key => &$value) {
            goto X2aza;
            x2BdA:
            fDtVE:
            goto aU4Zc;
            kN2P4:
            xqiSt:
            goto SghJp;
            aU4Zc:
            $duock = 1;
            goto y1dsu;
            y1dsu:
            bn3se:
            goto kN2P4;
            xC53x:
            goto bn3se;
            goto x2BdA;
            X2aza:
            if ($value["shennum"] >= $numarr[$key]) {
                goto fDtVE;
            }
            goto V1mem;
            V1mem:
            $duock = 0;
            goto xC53x;
            SghJp:
        }
        goto xYtvZ;
        GQZR7:
        $dataorder = array("true_price" => $newtrueprice, "coupon" => 0);
        goto QSqHD;
        ux_wt:
        $numarr = array();
        goto q0Dzc;
        QTWCw:
        global $_GPC, $_W;
        goto dO21Q;
        sWM_v:
        $body = "商品支付";
        goto vQ1Ml;
        pGK9H:
        return $this->result(0, "error", $data);
        goto EeOb3;
        Q1hzA:
    }
    public function doPagePage()
    {
        goto IOqxC;
        fVE7O:
        $pageInfo = pdo_fetch("SELECT name,ename,content FROM " . tablename("sudu8_page_cate") . " WHERE uniacid = :uniacid and id = :id ", array(":uniacid" => $uniacid, ":id" => $id));
        goto GUzHQ;
        GUzHQ:
        return $this->result(0, "success", $pageInfo);
        goto lNFI9;
        IOqxC:
        global $_GPC, $_W;
        goto gmWSy;
        Zcf37:
        $id = intval($_GPC["id"]);
        goto fVE7O;
        gmWSy:
        $uniacid = $_W["uniacid"];
        goto Zcf37;
        lNFI9:
    }
    public function doPagecopycon()
    {
        goto uOfbH;
        WJiO8:
        $copycon = pdo_fetch("SELECT copycon FROM " . tablename("sudu8_page_copyright") . " WHERE uniacid = :uniacid ", array(":uniacid" => $uniacid));
        goto v2NDS;
        v2NDS:
        return $this->result(0, "success", $copycon);
        goto hX03r;
        uOfbH:
        global $_GPC, $_W;
        goto PsqWx;
        PsqWx:
        $uniacid = $_W["uniacid"];
        goto WJiO8;
        hX03r:
    }
    public function doPagesendMail_order()
    {
        goto z5ZjP;
        AcGIA:
        $row_mail_user = $formsConfig["mail_user"];
        goto e278h;
        qg7eD:
        $mail->CharSet = "utf-8";
        goto suNgG;
        WYnL5:
        $mail->Password = $row_mail_pass;
        goto Tbp16;
        bTsZn:
        $result = "send_err";
        goto qe8Np;
        tU_UT:
        $row_prc = "购买金额：" . $ord["price"] . " x " . $ord["num"] . " = " . $ord["true_price"] . "<br />";
        goto hxbD1;
        z5ZjP:
        require_once "inc/class.phpmailer.php";
        goto fm_ai;
        hxbD1:
        $row_nam = "联系姓名：" . $ord["pro_user_name"] . "<br />";
        goto h7Nz3;
        qe8Np:
        vuYI4:
        goto fpMMF;
        vUzX4:
        $mail->Body = "<div style='height:40px;line-height:40px;font-size:16px;font-weight:bold;background:#7030A0;color:#fff;text-indent:10px;'>订单详情：</div><div style='line-height:30px;padding:15px;background:#f6f6f6'>" . $row_oid . $row_tim . $row_pro . $row_prc . $row_nam . $row_tel . $row_txt . "<div style='line-height:40px;margin-top:10px;text-align:center;color:#888;font-size:12px'>" . $row_mail_name . "</div></div>";
        goto BUSa6;
        Uh0Rk:
        $mail->SMTPDebug = false;
        goto gjKYr;
        B6svb:
        global $_GPC, $_W;
        goto GiwsQ;
        AMX13:
        $mail->Port = 465;
        goto ANXGX;
        kwIuM:
        $row_txt = "留言备注：" . $ord["pro_user_txt"] . "<br />";
        goto t1miY;
        h7Nz3:
        $row_tel = "联系电话：" . $ord["pro_user_tel"] . "<br />";
        goto kwIuM;
        ncVOO:
        $mail->Subject = "新订单 - " . date("Y-m-d H:i:s", time());
        goto sSPU4;
        jWzOx:
        goto vuYI4;
        goto oHQwX;
        Tbp16:
        $mail->setFrom($row_mail_user, $row_mail_name);
        goto wr9rC;
        VkoNU:
        $mail->IsSMTP();
        goto AMX13;
        gjKYr:
        $mail->Username = $row_mail_user;
        goto WYnL5;
        BUSa6:
        if (!$mail->send()) {
            goto J9evo;
        }
        goto ehnLy;
        LUJiC:
        $mail->SMTPSecure = "ssl";
        goto VkoNU;
        ehnLy:
        $result = "send_ok";
        goto jWzOx;
        G9D5u:
        $mail_sendto = explode(",", $formsConfig["mail_sendto"]);
        goto AcGIA;
        ANXGX:
        $mail->Host = "smtp.qq.com";
        goto z1ami;
        wr9rC:
        foreach ($mail_sendto as $v) {
            $mail->AddAddress($v);
            m8rCZ:
        }
        goto XDJDO;
        e278h:
        $row_mail_pass = $formsConfig["mail_password"];
        goto D2W3s;
        WxfnY:
        $mail_sendto = array();
        goto G9D5u;
        t1miY:
        $mail = new PHPMailer();
        goto qg7eD;
        GJYMt:
        $order_id = $_GPC["order_id"];
        goto BeBkw;
        suNgG:
        $mail->Encoding = "base64";
        goto LUJiC;
        fpMMF:
        return $this->result(0, "success", $result);
        goto nnLFo;
        GiwsQ:
        $uniacid = $_W["uniacid"];
        goto GJYMt;
        D2W3s:
        $row_mail_name = $formsConfig["mail_user_name"];
        goto HkYcr;
        z1ami:
        $mail->SMTPAuth = true;
        goto Uh0Rk;
        sSPU4:
        $mail->isHTML(true);
        goto vUzX4;
        e0gmc:
        $row_oid = "订单编号：" . $ord["order_id"] . "<br />";
        goto bv7G2;
        bv7G2:
        $row_pro = "产品名称：" . $ord["product"] . "<br />";
        goto tU_UT;
        BeBkw:
        $formsConfig = pdo_fetch("SELECT mail_sendto,mail_user,mail_password,mail_user_name FROM " . tablename("sudu8_page_forms_config") . " WHERE uniacid = :uniacid", array(":uniacid" => $uniacid));
        goto WxfnY;
        oHQwX:
        J9evo:
        goto bTsZn;
        XDJDO:
        rd_mu:
        goto ncVOO;
        fm_ai:
        require_once "inc/class.smtp.php";
        goto B6svb;
        HkYcr:
        $ord = pdo_fetch("SELECT order_id,product,price,num,true_price,pro_user_name,pro_user_tel,pro_user_txt FROM " . tablename("sudu8_page_order") . " WHERE uniacid = :uniacid AND order_id = :oid", array(":uniacid" => $uniacid, ":oid" => $order_id));
        goto e0gmc;
        nnLFo:
    }
    public function doPagecoupon()
    {
        goto A60Jr;
        Zh_2e:
        foreach ($coupon as $key => &$res) {
            goto wG8KL;
            Pyyfv:
            wp2MB:
            goto NZ4Of;
            SPi6F:
            $nowtime = time();
            goto vGPc7;
            SfNXD:
            $isover_time = 1;
            goto BsB9V;
            TyOwS:
            $isover = 1;
            goto CXD3f;
            te3Wy:
            qlGvt:
            goto SfNXD;
            MmQBv:
            mCS4Y:
            goto aU8nv;
            L7rIu:
            if ($WSw9C) {
                goto McVUv;
            }
            goto rtHvd;
            Q5Asd:
            goto HRUrP;
            goto CMHDK;
            L3xBh:
            $is_get = 1;
            goto U_DBS;
            hJAYg:
            goto RwBTB;
            goto te3Wy;
            OqxXD:
            if ($gp3nk) {
                goto pxzBx;
            }
            goto X0m3e;
            bSFxu:
            $isover_time = 0;
            goto i6qyr;
            i6qyr:
            goto mCS4Y;
            goto YZuAK;
            wG8KL:
            $isover = 1;
            goto CXE2T;
            CMHDK:
            bRqid:
            goto w4BlM;
            vGPc7:
            $gp3nk = !($res["btime"] != 0 && $res["etime"] != 0);
            goto OqxXD;
            JZLQG:
            if ($K51rD) {
                goto oPeu9;
            }
            goto qwBcn;
            qwBcn:
            $res["btime"] = date("Y-m-d", $res["btime"]);
            goto uz55P;
            BPLCG:
            HRUrP:
            goto Pyyfv;
            aU8nv:
            pxzBx:
            goto u0MlH;
            z8JSV:
            $K51rD = !($res["btime"] != 0);
            goto JZLQG;
            N4iQ6:
            if ($VA83A) {
                goto HN1j3;
            }
            goto VXd93;
            kkvCB:
            USrP8:
            goto UiMt1;
            VXd93:
            $isover_time = 1;
            goto CdKtR;
            UiMt1:
            $res["is_get"] = $is_get;
            goto lUCCH;
            U_DBS:
            $yhqbuy = pdo_fetch("SELECT count(*) as n FROM " . tablename("sudu8_page_coupon_user") . " WHERE uniacid = :uniacid and cid = :id and uid = :uid", array(":uniacid" => $_W["uniacid"], ":id" => $res["id"], ":uid" => $uid));
            goto ckoAa;
            Vdtw_:
            $isover_time = 1;
            goto SPi6F;
            onfhU:
            $VA83A = !($res["btime"] == 0 && $res["etime"] == 0);
            goto N4iQ6;
            BsB9V:
            RwBTB:
            goto Ys01O;
            KNeKs:
            $isover_time = 1;
            goto MmQBv;
            jkm81:
            $coupon[$key]["nowCount"] = intval($res["xz_count"]) - intval($yhqbuy["n"]);
            goto sNSoR;
            NZ4Of:
            $WSw9C = !($res["btime"] == 0 && $res["etime"] != 0);
            goto L7rIu;
            BgdjJ:
            $res["kc"] = $res["counts"] - $yhqs["n"];
            goto z8JSV;
            XazR1:
            if ($sRr_A) {
                goto USrP8;
            }
            goto r7gnJ;
            wUMxj:
            if ($kAMsi) {
                goto wp2MB;
            }
            goto vd7zr;
            X0m3e:
            if ($nowtime >= $res["btime"] && $nowtime <= $res["etime"]) {
                goto AAi04;
            }
            goto bSFxu;
            sG_S9:
            ihpsT:
            goto J6Wb4;
            PDAhw:
            $isover_time = 0;
            goto Q5Asd;
            uz55P:
            oPeu9:
            goto E86PE;
            qzHMv:
            hdH_l:
            goto nml1v;
            rtHvd:
            if ($nowtime <= $res["btime"]) {
                goto qlGvt;
            }
            goto AmMRv;
            VsqH_:
            $sRr_A = !($res["xz_count"] > 0 && $yhqbuy["n"] == $res["xz_count"]);
            goto XazR1;
            vd7zr:
            if ($nowtime >= $res["btime"]) {
                goto bRqid;
            }
            goto PDAhw;
            CXE2T:
            if ($res["counts"] == 0) {
                goto ihpsT;
            }
            goto TyOwS;
            MtLPl:
            d6H7l:
            goto niv6m;
            QbwMV:
            $res["isover_time"] = $isover_time;
            goto L3xBh;
            aIc74:
            $res["isover"] = $isover;
            goto Vdtw_;
            sNSoR:
            goto jO6ZA;
            goto MtLPl;
            r7gnJ:
            $is_get = 0;
            goto kkvCB;
            Cf4Cb:
            if ($HIYQf) {
                goto hdH_l;
            }
            goto RYddu;
            CdKtR:
            HN1j3:
            goto QbwMV;
            a2Z4w:
            LmCh2:
            goto aIc74;
            YZuAK:
            AAi04:
            goto KNeKs;
            CXD3f:
            goto LmCh2;
            goto sG_S9;
            RYddu:
            $res["etime"] = date("Y-m-d", $res["etime"]);
            goto qzHMv;
            lUCCH:
            $yhqs = pdo_fetch("SELECT count(*) as n FROM " . tablename("sudu8_page_coupon_user") . " WHERE uniacid = :uniacid and cid = :id", array(":uniacid" => $_W["uniacid"], ":id" => $res["id"]));
            goto BgdjJ;
            Ys01O:
            McVUv:
            goto onfhU;
            ckoAa:
            if ($res["xz_count"] == 0) {
                goto d6H7l;
            }
            goto jkm81;
            E86PE:
            $HIYQf = !($res["etime"] != 0);
            goto Cf4Cb;
            AmMRv:
            $isover_time = 0;
            goto hJAYg;
            w4BlM:
            $isover_time = 1;
            goto BPLCG;
            niv6m:
            $coupon[$key]["nowCount"] = "无限";
            goto RJn3a;
            u0MlH:
            $kAMsi = !($res["btime"] != 0 && $res["etime"] == 0);
            goto wUMxj;
            RJn3a:
            jO6ZA:
            goto VsqH_;
            J6Wb4:
            $isover = 0;
            goto a2Z4w;
            nml1v:
            ex0_N:
            goto XWUjm;
            XWUjm:
        }
        goto q8qjs;
        aLTbg:
        $uniacid = $_W["uniacid"];
        goto TmdCo;
        bg77S:
        return $this->result(0, "success", $coupon);
        goto T7kFa;
        bXyAI:
        $user = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_user") . " WHERE uniacid = :uniacid and openid = :openid", array(":uniacid" => $uniacid, ":openid" => $openid));
        goto yZ6WR;
        TiAm5:
        $xuVXW = !$openid;
        goto Unao8;
        A60Jr:
        global $_GPC, $_W;
        goto aLTbg;
        q8qjs:
        PkU3J:
        goto bg77S;
        Unao8:
        if ($xuVXW) {
            goto qQF4Y;
        }
        goto bXyAI;
        rFDg0:
        $uid = $user["id"];
        goto CZDIg;
        TmdCo:
        $openid = $_GPC["openid"];
        goto TiAm5;
        CZDIg:
        $coupon = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_coupon") . " WHERE uniacid = :uniacid and flag = 1 ORDER BY num DESC,id DESC", array(":uniacid" => $uniacid));
        goto Zh_2e;
        yZ6WR:
        qQF4Y:
        goto rFDg0;
        T7kFa:
    }
    public function doPagegetcoupon()
    {
        goto g5zVu;
        v3fse:
        $uid = $user["id"];
        goto ceF03;
        rFEsK:
        $openid = $_GPC["openid"];
        goto vfEfM;
        zdNFu:
        $xuVXW = !$openid;
        goto MJlHa;
        MJlHa:
        if ($xuVXW) {
            goto gRYnJ;
        }
        goto jrC4U;
        lH1Ly:
        gRYnJ:
        goto v3fse;
        jrC4U:
        $user = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_user") . " WHERE uniacid = :uniacid and openid = :openid", array(":uniacid" => $uniacid, ":openid" => $openid));
        goto lH1Ly;
        tME5d:
        $data = array("uniacid" => $uniacid, "uid" => $uid, "cid" => $id, "ltime" => time(), "flag" => 0, "btime" => $coupon["btime"], "etime" => $coupon["etime"]);
        goto L372d;
        SX_p_:
        $uniacid = $_W["uniacid"];
        goto rFEsK;
        sx3KD:
        return $this->result(0, "success", 1);
        goto qu4Wp;
        vfEfM:
        $id = $_GPC["id"];
        goto zdNFu;
        g5zVu:
        global $_GPC, $_W;
        goto SX_p_;
        ceF03:
        $coupon = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_coupon") . " WHERE uniacid = :uniacid and id = :id", array(":uniacid" => $uniacid, ":id" => $id));
        goto tME5d;
        L372d:
        $res = pdo_insert("sudu8_page_coupon_user", $data);
        goto sx3KD;
        qu4Wp:
    }
    public function doPagemycoupon()
    {
        goto G_5lR;
        G_5lR:
        global $_GPC, $_W;
        goto go9jK;
        bg3r_:
        if ($xuVXW) {
            goto p0jNs;
        }
        goto nbur9;
        vqWOa:
        $uid = $user["id"];
        goto RoTNV;
        ZZ3Tc:
        SIJrV:
        goto P7vBG;
        DI_4m:
        foreach ($yhqs as $key => &$res) {
            goto H64uj;
            rCqdv:
            $arrs["etime"] = date("Y-m-d", $arrs["etime"]);
            goto q9HAV;
            DE0Nc:
            if ($lMnPS) {
                goto nyGWH;
            }
            goto rCqdv;
            SEZEh:
            $lMnPS = !($arrs["etime"] != 0);
            goto DE0Nc;
            FcE8u:
            $R7bvh = !($arrs["btime"] != 0);
            goto zUc1z;
            H64uj:
            $arrs = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_coupon") . " WHERE uniacid = :uniacid and id = :id ", array(":uniacid" => $uniacid, "id" => $res["cid"]));
            goto FcE8u;
            zUc1z:
            if ($R7bvh) {
                goto voy6D;
            }
            goto go3jk;
            Hsxhy:
            voy6D:
            goto SEZEh;
            q9HAV:
            nyGWH:
            goto LMBQN;
            fgOgd:
            ZLXnY:
            goto d7Fyw;
            go3jk:
            $arrs["btime"] = date("Y-m-d", $arrs["btime"]);
            goto Hsxhy;
            LMBQN:
            $res["coupon"] = $arrs;
            goto fgOgd;
            d7Fyw:
        }
        goto ZZ3Tc;
        go9jK:
        $uniacid = $_W["uniacid"];
        goto atOhX;
        RoTNV:
        $yhqs = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_coupon_user") . " WHERE uniacid = :uniacid and uid = :id and flag = 0 ORDER BY id DESC", array(":uniacid" => $_W["uniacid"], ":id" => $uid));
        goto DI_4m;
        HOl0X:
        p0jNs:
        goto vqWOa;
        nbur9:
        $user = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_user") . " WHERE uniacid = :uniacid and openid = :openid", array(":uniacid" => $uniacid, ":openid" => $openid));
        goto HOl0X;
        WFDS_:
        $xuVXW = !$openid;
        goto bg3r_;
        P7vBG:
        return $this->result(0, "success", $yhqs);
        goto GD35M;
        atOhX:
        $openid = $_GPC["openid"];
        goto WFDS_;
        GD35M:
    }
    public function doPageCollect()
    {
        goto LCkTH;
        IsBnt:
        $res = pdo_insert("sudu8_page_collect", $data);
        goto beMRC;
        e4utX:
        if ($collect) {
            goto KEf1J;
        }
        goto VyNCn;
        gxBiQ:
        bm_7F:
        goto jeMJa;
        m0pwu:
        if ($bPshy) {
            goto NZ3oT;
        }
        goto WeCFW;
        s70aP:
        nac3a:
        goto O6cIm;
        YHvBO:
        $user = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_user") . " WHERE uniacid = :uniacid and openid = :openid", array(":uniacid" => $uniacid, ":openid" => $openid));
        goto BMuRV;
        VyNCn:
        $data = array("uid" => $uid, "type" => $type, "cid" => $cid, "uniacid" => $uniacid);
        goto IsBnt;
        dUITo:
        $xuVXW = !$openid;
        goto yZT5Y;
        LCkTH:
        global $_GPC, $_W;
        goto WXuiv;
        p5xqm:
        KEf1J:
        goto zCB0M;
        FBDGF:
        NZ3oT:
        goto s70aP;
        BMuRV:
        o4bph:
        goto ZdfcZ;
        Y7PAc:
        $cid = $_GPC["id"];
        goto LLdwb;
        ZdfcZ:
        $uid = $user["id"];
        goto QVFhD;
        WXuiv:
        $uniacid = $_W["uniacid"];
        goto Y7PAc;
        H96ov:
        return $this->result(0, "success", "收藏成功");
        goto gxBiQ;
        WeCFW:
        return $this->result(0, "success", "取消收藏成功");
        goto FBDGF;
        LLdwb:
        $openid = $_GPC["openid"];
        goto Muh0S;
        Muh0S:
        $type = $_GPC["types"];
        goto dUITo;
        yZT5Y:
        if ($xuVXW) {
            goto o4bph;
        }
        goto YHvBO;
        PhdyN:
        $bPshy = !$res;
        goto m0pwu;
        zCB0M:
        $res = pdo_delete("sudu8_page_collect", array("uniacid" => $uniacid, "uid" => $uid, "type" => $type, "cid" => $cid));
        goto PhdyN;
        QVFhD:
        $collect = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_collect") . " WHERE uniacid = :uniacid and uid = :uid and type = :type and cid = :cid", array(":uniacid" => $uniacid, ":uid" => $uid, ":type" => $type, ":cid" => $cid));
        goto e4utX;
        yoQ7V:
        if ($bPshy) {
            goto bm_7F;
        }
        goto H96ov;
        beMRC:
        $bPshy = !$res;
        goto yoQ7V;
        jeMJa:
        goto nac3a;
        goto p5xqm;
        O6cIm:
    }
    public function doPagegetCollect()
    {
        goto NBAKc;
        hQkwk:
        $openid = $_GPC["openid"];
        goto deiaJ;
        u_o_A:
        $collect = pdo_fetchall("SELECT * FROM " . tablename("sudu8_page_collect") . " WHERE uniacid = :uniacid and uid = :uid LIMIT " . ($pindex - 1) * $psize . "," . $psize, array(":uniacid" => $uniacid, ":uid" => $uid));
        goto Uh7fO;
        wEqQB:
        return $this->result(0, "success", $arr);
        goto RVv2k;
        PLb2i:
        $arr = array();
        goto lhr7v;
        Uh7fO:
        $all = pdo_fetch("SELECT count(*) as n FROM " . tablename("sudu8_page_collect") . " WHERE uniacid = :uniacid and uid = :uid ", array(":uniacid" => $uniacid, ":uid" => $uid));
        goto pJFnZ;
        iqziQ:
        $uniacid = $_W["uniacid"];
        goto hQkwk;
        J4nk5:
        if ($xuVXW) {
            goto Bk3yj;
        }
        goto Ix3ZH;
        pJFnZ:
        $num = $all["n"];
        goto PLb2i;
        nvGvp:
        sCvGc:
        goto PKDGF;
        PKDGF:
        $arr["num"] = ceil($num / $psize);
        goto wEqQB;
        tHnLr:
        $uid = $user["id"];
        goto u_o_A;
        RVv2k:
        Bk3yj:
        goto p7Ndc;
        Ix3ZH:
        $user = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_user") . " WHERE uniacid = :uniacid and openid = :openid", array(":uniacid" => $uniacid, ":openid" => $openid));
        goto tHnLr;
        lhr7v:
        foreach ($collect as $key => &$rec) {
            goto titfS;
            gOPxn:
            $arr["list"][] = $pro;
            goto aPhvd;
            titfS:
            $pro = pdo_fetch("SELECT * FROM " . tablename("sudu8_page_products") . " WHERE id = :id and uniacid = :uniacid and flag = 1", array(":id" => $rec["cid"], ":uniacid" => $uniacid));
            goto vWKxQ;
            aPhvd:
            Lf6Sv:
            goto GlJfp;
            vWKxQ:
            $pro["thumb"] = HTTPSHOST . $pro["thumb"];
            goto gOPxn;
            GlJfp:
        }
        goto nvGvp;
        yKDdy:
        $psize = 10;
        goto pNuhc;
        pNuhc:
        $xuVXW = !$openid;
        goto J4nk5;
        NBAKc:
        global $_GPC, $_W;
        goto iqziQ;
        deiaJ:
        $pindex = max(1, intval($_GPC["page"]));
        goto yKDdy;
        p7Ndc:
    }
}