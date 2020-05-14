<?php

echo date('Y-m-d H:i:s')." : Welcome...........".PHP_EOL;
echo date('Y-m-d H:i:s')." : Initiating PHPExcel".PHP_EOL;
require("./PHPExcel/Classes/PHPExcel/IOFactory.php");
echo date('Y-m-d H:i:s')." : PHPExcel initiated".PHP_EOL;

class KRS{

	public $filename;
	public $data = [];
	public $excel;
	public $row = 6;
	public $column = 13;

	public function __construct($filename){
		$this->filename = $filename;
		echo date('Y-m-d H:i:s')." : Reading data source".PHP_EOL;
		$this->excel = PHPExcel_IOFactory::load($this->filename);
	}

	public function main(){
		foreach($this->excel->getWorksheetIterator() as $idworksheet => $worksheet){
			echo date('Y-m-d H:i:s')." : Reading worksheet {$idworksheet}";
			for($i=2 ; $i<=$this->row ; $i++){
				for($j=1; $j<=$this->column ; $j++){
					$cell = $worksheet->getCellByColumnAndRow($j, $i);
					
					if($cell->isMergeRangeValueCell()){
						$name = $cell->getValue();

						if(!empty($name) && is_string($name)){
							$ranges = PHPExcel_Cell::splitRange($cell->getMergeRange());
							foreach($ranges as $range){
								$from = $this->__convertId($range[0]);
								$to = $this->__convertId($range[1]);

								for($k=$from["j"];$k<=$to["j"];$k++){
									$this->inject($idworksheet, $name, $from["i"], $k);
								}
							}
						}
					}
				}
			}
			echo " - Completed".PHP_EOL;
		}

		echo date('Y-m-d H:i:s')." : Saving to data.json".PHP_EOL;
		file_put_contents('data.json', json_encode($this->data, JSON_PRETTY_PRINT));
		echo date('Y-m-d H:i:s')." : Saved to data.json".PHP_EOL;
	}

	public function __convertId($id){
		$num = [];$_num=0;
		$str = [];$_str=0;

		$id = str_split($id);
		foreach($id as $i){
			if(ord($i)>=65 && ord($i)<=90){
				$str[] = ord($i);
			}
			else{
				$num[] = $i;
			}
		}

		array_reverse($str);
		array_reverse($num);

		for($i=0;$i<count($num);$i++){
			$_num += intval($num[$i])*pow(10, $i);
		}
		for($i=0;$i<count($str);$i++){
			$_str += (intval($str[$i])-64)*pow(10, $i);
		}

		return [
			'i' => $_num,
			'j' => $_str-1
		];
	}

	public function inject($idworksheet, $name, $day, $hour){
		$i = $this->__findId($name);

		if($i == -1){
			$i = array_push($this->data, [
				'name' => $name,
				'options' => []
			])-1;
		}

		if(empty($this->data[$i]['options'][$idworksheet])){
			$this->data[$i]['options'][$idworksheet] = [
				'class' => chr($idworksheet+65),
				'day' => $day,
				'hours' => []
			];
		}

		array_push($this->data[$i]['options'][$idworksheet]['hours'], $hour);
	}

	public function __findId($name){
		foreach($this->data as $key => $data){
			if(@$data["name"] == $name){
				return $key;
			}
		}

		return -1;
	}
}

$krs = new KRS('data.xlsx');
$krs->main();
