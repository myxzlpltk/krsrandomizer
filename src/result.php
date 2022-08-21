<?php

class Result{

	public $results;
	public $excel;

	public function __construct(){
		$this->results = json_decode(file_get_contents('output/results.json'));
		$this->excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$this->excel->getProperties()->setCreator("Saddam Azy")
			->setLastModifiedBy("Saddam Azy")
			->setTitle("Kombinasi KRS")
			->setSubject("Kombinasi KRS")
			->setDescription("Kombinasi KRS")
			->setKeywords("mahasantuy")
			->setCategory("mahasantuy");
	}

	public function main(){
		echo date('Y-m-d H:i:s')." : Creating ".count($this->results)." sheets".PHP_EOL;
		foreach($this->results as $page => $result){
			$sheet = $this->excel->createSheet($page);
			$sheet->setTitle(strval($page+1));

			for($i=2;$i<=6;$i++){
				$sheet->getRowDimension($i)->setRowHeight(30);
				$sheet->getStyle(strval("A{$i}"))->getAlignment()->setWrapText(true);
				$sheet->getStyle(strval("A{$i}"))->applyFromArray([
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
					]
				]);
			}

			$sheet
				->setCellValue('A2', 'Senin')
				->setCellValue('A3', 'Selasa')
				->setCellValue('A4', 'Rabu')
				->setCellValue('A5', 'Kamis')
				->setCellValue('A6', 'Jumat');

			for($i=1;$i<=12;$i++){
				$sheet->setCellValue(strval(chr(65+$i)."1"), $i);
				$sheet->getStyle(strval(chr(65+$i)."1"))->getAlignment()->setWrapText(true);
				$sheet->getStyle(strval(chr(65+$i)."1"))->applyFromArray([
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
					]
				]);
			}

			$temp = "";
			$merge;
			foreach($result as $i => $day){
				foreach($day as $j => $hour){
					foreach($hour as $k => $lesson){
						if($temp == $lesson->name){
							if($j > $merge+1){
								$sheet->unmergeCells(chr(65+$merge).($i).":".chr(65+$j-1).($i));
							}
							$sheet->mergeCells(chr(65+$merge).($i).":".chr(65+$j).($i));

							$sheet->getStyle(chr(65+$merge).($i).":".chr(65+$j).($i))->getAlignment()->setWrapText(true);
							$sheet->getStyle(chr(65+$merge).($i).":".chr(65+$j).($i))->applyFromArray([
								'alignment' => [
									'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
									'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
								]
							]);
						}
						else{
							$id = strval(chr(65+$j).strval($i));
							$sheet->setCellValue($id, $lesson->name." ".$lesson->class);
							$temp = $lesson->name;
							$merge = $j;
						}
					}
				}
			}
		}

		echo date('Y-m-d H:i:s')." : Total ".count($this->results)." sheets created".PHP_EOL;

		$this->excel->setActiveSheetIndex(0);

		echo date('Y-m-d H:i:s')." : Saving to results.xlsx".PHP_EOL;
		$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->excel, 'Xlsx');
		$objWriter->save('output/results.xlsx');
		echo date('Y-m-d H:i:s')." : Saved to results.xlsx".PHP_EOL;
	}

}

$result = new Result();
$result->main();