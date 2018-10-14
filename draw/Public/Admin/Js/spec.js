function spec_combi(sp_a,sp_b,sp_data){
    //return;
	//得到组合数据
	var tbody  = '';//定义body内容
	if($.isEmptyObject(sp_a) && $.isEmptyObject(sp_b)){
		$('.sku-wrapper table tbody').html("<tr><td colspan='6'>请选择规格</td></tr>");;
		return;
	}
	if($.isEmptyObject(sp_a) && !$.isEmptyObject(sp_b)){
		sp_a = sp_b;
		sp_b = {};
	}
	var rowNum = $.Object.count.call(sp_b);
	$.each(sp_a,function(key,val){
		var i = 0;
		if(!$.isEmptyObject(sp_b)){
			$.each(sp_b,function(k,v){
				var is_edit = true;
			    spec_ids = (key<k) ? key + "_" + k : k + "_" + key;
				if(sp_data != null) var sp_ev = eval("sp_data[spec_ids]");
				if(sp_ev == undefined) {
					sp_ev = {'price':'','quantity':'','product_sn':'','product_id':'','goods_id':''};
					is_edit = false;
				}
				tbody += '<tr>'
					  + ((i==0) ? '<td class="title" rowspan=' + rowNum + '><span>' + val + '</span></td>' : '')
					  + '<td rowspan="1"><span>' + v + '</span></td>'
					  + '<td class="price"><input type="text" value="' + sp_ev["price"] + '" name="s['+spec_ids+'][price]"/></td>'
					  + '<td class="quantity"><input type="text" value="' + sp_ev["quantity"] + '" name="s['+spec_ids+'][quantity]" /></td>'
					  + '<td class="tsc"><input type="text" value="' + sp_ev["product_sn"] + '" name="s['+spec_ids+'][product_sn]" /></td>'
					  + '<td class="batch">'
					  + ((is_edit ==  false) ? '' : '<a href="javascript:;" onclick=\'javascript:confirm_delete("'+__dir__+'/delete_product/pro_id/' + sp_ev["product_id"] + '/goods_id/'+sp_ev["goods_id"]+'/menuid/'+ __menuid__ +' ")\' title="移除">移除</a>'
					  + '|<a href="javascript:;" onclick=\'javascript:confirm_delete("'+__dir__+'/delete_product/pro_id/' + sp_ev["product_id"] + '/goods_id/'+sp_ev["goods_id"]+'/menuid/'+ __menuid__ +' ")\' title="移除">编辑</a>')
					  + '<input type="hidden" name="s['+spec_ids+'][product_id]" value="'+ sp_ev["product_id"] +'"></td>'
					  + '</tr>';
				
				++i;
			});
		}else{
			var is_edit = true;
			spec_ids = key;
			if(sp_data != null) var is_edit = sp_ev = eval("sp_data[spec_ids]");
			if(sp_ev == undefined) {
				sp_ev = {'price':'','quantity':'','product_sn':'','product_id':'','goods_id':''};
				is_edit = false;
			}
			tbody += '<tr>'
					  + ((i==0) ? '<td class="title" ><span>' + val + '</span></td>' : '')
					  + '<td rowspan="1"><span>-</span></td>'
					  + '<td class="price"><input type="text" value="' + sp_ev["price"] + '" name="s['+spec_ids+'][price]"/></td>'
					  + '<td class="quantity"><input type="text" value="' + sp_ev["quantity"] + '" name="s['+spec_ids+'][quantity]"/></td>'
					  + '<td class="tsc"><input type="text" value="' + sp_ev["product_sn"] + '" name="s['+spec_ids+'][product_sn]"/></td>'
					  + '<td class="batch">'
					  + ((is_edit ==  false) ? '' : '<a href="javascript:;" onclick=\'javascript:confirm_delete("'+__dir__+'/delete_product/pro_id/' + sp_ev["product_id"] + '/goods_id/'+sp_ev["goods_id"]+'/menuid/'+ __menuid__ +' ")\' title="移除">移除</a>'
					  + '|<a href="javascript:;" onclick=\'javascript:confirm_delete("'+__dir__+'/delete_product/pro_id/' + sp_ev["product_id"] + '/goods_id/'+sp_ev["goods_id"]+'/menuid/'+ __menuid__ +' ")\' title="移除">编辑</a>')
					  + '<input type="hidden" name="s['+spec_ids+'][product_id]" value="'+ sp_ev["product_id"] +'"></td>'
					  + '</tr>';
		}
	});
	//拼接价格数量等数据
	
	//替换数据
	$('.sku-wrapper table tbody').html(tbody);
}