<?php
/*
 * 前台模版地区调用逻辑代码
 * @author wscsky
 */
namespace Home\Logic;
class CityLogic{
	
    /**
     * 读取城市位置函数
     * @param int $region_id
     * @author wscsky
     */
    function get_pos($region_id){
        if(!is_numeric($region_id)) return "";
        $data = F("city_pos_data");
        
        if(!$data[$region_id]){
            $region_full_ids = M("region")->where("region_id = %d", $region_id)->getField('region_full_ids');
            if($region_full_ids){
                $data[$region_id] = M("region") -> where("region_id in (%s) and region_type > 0", trim($region_full_ids))
                                                -> order("region_type asc")
                                                -> select();
            }
            APP_DEBUG or F("city_pos_data", $data);
        }
        return $data[$region_id];
    }
	
}