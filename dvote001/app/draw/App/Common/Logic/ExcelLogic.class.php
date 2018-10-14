<?php
/**
 * 导入导出Excell
 * @author watchman
 */
namespace Common\Logic;
class ExcelLogic{
	protected $_error = null;
	
	/**
	 * 导出数据到excel
	 * @author watchman
	 * @param string $filename 文件名称
	 * @param array $data 要导入的数据
	 * @param array $config excel配置
	 */
	function getExcel($filename, $data, $config, $is_output = true, $save_path = null){
		//导入PHPExcel类库
		import("Org.Util.PHPExcel");
		import("Org.Util.PHPExcel.Writer.Excel5");
		import("Org.Util.PHPExcel.IOFactory.php");
	
		$date = date("Ymd",time());
		$filename .= "_{$date}.xls";
	
		//创建PHPExcel对象
		$objPHPExcel = new \PHPExcel();
		$objProps = $objPHPExcel->getProperties();
		
		\PHPExcel_Settings::setCacheStorageMethod(\PHPExcel_CachedObjectStorageFactory::cache_to_discISAM);
		
		$rkey = array("","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		//设置表头
		$i = 1;
		foreach($config as $columns){
			if($i > 26){
				$j = intval($i/26);
				$k = $i % 26;
				$column = $rkey[$j].$rkey[$k];
			}else{
				$column = $rkey[$i];
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($column.'1', $columns['headname']);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle($column.'1')->getAlignment()->setHorizontal('center');
			$i++;
		}
		$row = 2;
		$objActSheet = $objPHPExcel->getActiveSheet();
	
		foreach($data as $rows){ //行写入
			$i = 1;
			foreach($rows as $key=>$value){// 列写入
				if($i > 26){
					$j = intval($i/26);
					$k = $i % 26;
					$column = $rkey[$j].$rkey[$k];
				}else{
					$column = $rkey[$i];
				}
				$objActSheet->getColumnDimension($column)->setWidth($config[$key]['width']);
// 				$objActSheet->setCellValue($column.$row, $value['value']);
				$objActSheet->setCellValueExplicit($column.$row, $value,'s');
				$objActSheet->getStyle($column.$row)->getFont()->getColor()->setARGB($value['color']);
				$i++;
			}
			$row++;
		}
	
		$filename = iconv("utf-8", "gb2312", $filename);
		
		//设置活动单指数到第一个表,所以Excel打开这是第一个表
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		
		if($is_output || is_null($save_path) || !is_dir($save_path)){
			header('Content-Type: application/vnd.ms-excel;charset=utf-8');
			header("Content-Disposition: attachment;filename=\"$filename\"");
			header('Cache-Control: max-age=0');
			$objWriter->save('php://output'); //文件通过浏览器下载
			exit;
		}else{
                    $objWriter->save($save_path."/".$filename); //保存文件到指定路径
                    return $save_path."/".$filename;    
		}
	}
	
	/**
	 * 生成excel表数据
	 * @param string $tmp_uri Excel模版URI
	 * @param array $rdata     已处理好的表格数据
	 * @param string          保存位置如果为空则直接输出到浏览器
	 * @author wscsky
	 */
	
	function create_excel($tmp_uri, $rdata = array(), $save_uri = ""){
	    import("Org.Util.PHPExcel");
	    import("Org.Util.PHPExcel.IOFactory",'','.php');
	    $objReader = \PHPExcel_IOFactory::createReader('Excel5');
	    $objPHPExcel = $objReader->load($tmp_uri);
	    foreach ($rdata as $key => $val){
	        if(is_array($val)){
	            $objPHPExcel->getActiveSheet()->setCellValueExplicit($key, $val[0], $val[1] ? $val[1] :'s');
	        }else{
	            $objPHPExcel->getActiveSheet()->setCellValueExplicit($key, $val, "s");
	        }
	    }
	    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    if($save_uri == ""){
	        $save_uri = time().".xls";
	        header('Content-Type: application/vnd.ms-excel;charset=utf-8');
	        header("Content-Disposition: attachment;filename={$save_uri}");
	        header('Cache-Control: max-age=0');
	        $objWriter->save('php://output'); //文件通过浏览器下载
	        exit;
	    }else{
	        return  $objWriter->save($save_uri);
	    }
	}
	
	/**
	 * Excel转成数组
	 * @param string $filePath excel文件地址
	 * @param int  $sheet excel 工作表索引
	 * @return array
	 * @author wscsky
	 */
	function excel2array($filePath='',$sheet=0){
		if(empty($filePath) or !file_exists($filePath)){
			$this->_error = "Excel文件不存在";
			return false;
		}
		import("Org.Util.PHPExcel");
		import("Org.Util.PHPExcel.Reader.Excel2007");
		$PHPReader = new \PHPExcel_Reader_Excel2007();        //建立reader对象
		if(!$PHPReader->canRead($filePath)){
			import("Org.Util.PHPExcel.Reader.Excel5");
			$PHPReader = new \PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filePath)){
				$this->_error = "Excel文件有错。无法识别！";
				return false;
			}
		}
		$PHPExcel 		= $PHPReader->load($filePath);        //建立excel对象
		$currentSheet 	= $PHPExcel->getSheet($sheet);        //**读取excel文件中的指定工作表*/
		$allColumn 		= $currentSheet->getHighestColumn();  //**取得最大的列号*/
		$allRow 		= $currentSheet->getHighestRow();     //**取得一共有多少行*/
		if(strlen($allColumn) == 2){
			$allColumn2 = $allColumn{1};
			$allColumn = $allColumn{0};
		}else{
			$allColumn2 = false;
		}
		$data = array();
		for($rowIndex=1; $rowIndex<=$allRow; $rowIndex++){      //循环读取每个单元格的内容。注意行从1开始，列从A开始
			for($colIndex='A';$colIndex<=$allColumn;$colIndex++){
				if($allColumn2){
					//有两排字母时
					if($colIndex == "A"){
						//先处单字母
						for($kk=0;$kk <=25;$kk++){
							$k = chr($kk+65);
							$addr = $k.$rowIndex;
							$cell = $currentSheet->getCell($addr)->getValue();
							if($cell instanceof \PHPExcel_RichText){ //富文本转换字符串
								$cell = $cell->__toString();
							}
							$data[$rowIndex-1][$k] = trim(trim($cell,chr(9)));
						}
					}
					//处理双字母
					for($k='A'; $k <=$allColumn2; $k++){
						$addr = $colIndex.$k.$rowIndex;
						$cell = $currentSheet->getCell($addr)->getValue();
						if($cell instanceof \PHPExcel_RichText){ //富文本转换字符串
							$cell = $cell->__toString();
						}
						$data[$rowIndex-1][$colIndex.$k] = trim(trim($cell,chr(9)));
					}
				}else{
					//只有一排字母时
					$addr = $colIndex.$rowIndex;
					$cell = $currentSheet->getCell($addr)->getValue();
					if($cell instanceof \PHPExcel_RichText){ //富文本转换字符串
						$cell = $cell->__toString();
					}
					$data[$rowIndex-1][$colIndex] =  trim(trim($cell,chr(9)));
				}
			}
		}
		return $data;
	}
	
	/**
	 * 获取错误信息
	 * @author watchman
	 */
	function getError(){
		return $this->_error;
	}
        
        public function exportExcel($expTitle,$expCellName,$expTableData,$save_path){
        $abs_path = dirname(dirname(dirname(dirname(__FILE__))));        
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
       
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
       // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]); 
        } 
          // Miscellaneous glyphs, UTF-8   
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
          }             
        }  
        $fileName = iconv("utf-8", "gb2312", $fileName);
        if( is_null($save_path) || !is_dir($save_path)){
            header('pragma:public');
            header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
            header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
            $objWriter->save('php://output'); 
            exit;
        }else{
            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save($abs_path.$save_path."/".$fileName.".xls"); //保存文件到指定路径
            $fileName = iconv("gb2312", "utf-8", $fileName);
            return $save_path."/".$fileName.'.xls';    
        }
    }
}