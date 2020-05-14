<?php

class App{

	public $data;
	public $results = [];
	public $filtered = [];
	public $total = 3;

	public function __construct(){
		$this->data = json_decode(file_get_contents('data.json'));
	}

	public function main(){
		$this->iterate(0, $this->emptyShell());
	}

	public function iterate($i=0, $data){
		if(!empty($this->data[$i])){
			foreach($this->data[$i]->options as $option){
				$temp = $data;
				$name = $this->data[$i]->name;

				foreach($option->hours as $hour){
					$temp[$option->day][$hour][] = [
						'name' => $name,
						'class' => $option->class
					];
				}

				$this->iterate($i+1, $temp);
			}
		}
		else{
			$this->results[] = $data;
		}
	}

	public function filter(){
		$total = count($this->results);

		for($i=0;$i<$total;$i++){
			$good = true;

			foreach($this->results[$i] as $day){
				foreach($day as $hour){
					if(count($hour) > 1){
						$good = false;
						break;
					}
				}

				if(!$good) break;
			}

			if($good) $this->filtered[] = $this->results[$i];
		}
	}

	public function save($filename='results.json'){
		file_put_contents($filename, json_encode($this->filtered, JSON_PRETTY_PRINT));
	}

	public function emptyShell(){
		$data = [];

		for($i=1;$i<=5;$i++){
			$data[$i] = [];
			for($j=1;$j<=12;$j++){
				$data[$i][$j] = [];
			}
		}

		return $data;
	}

}

require "parse.php";

$app = new App();
echo date('Y-m-d H:i:s')." : Executing App".PHP_EOL;
$app->main();
echo date('Y-m-d H:i:s')." : App executed".PHP_EOL;

echo date('Y-m-d H:i:s')." : Resulting....".PHP_EOL;
echo date('Y-m-d H:i:s')." : ".count($app->results)." combination founds".PHP_EOL;
echo date('Y-m-d H:i:s')." : Filtering....".PHP_EOL;
$app->filter();
echo date('Y-m-d H:i:s')." : Filtered....".PHP_EOL;
echo date('Y-m-d H:i:s')." : ".count($app->filtered)." from ".count($app->results)." filtered combination founds".PHP_EOL;
echo date('Y-m-d H:i:s')." : Saving....".PHP_EOL;
$app->save();
echo date('Y-m-d H:i:s')." : Saved to results.json".PHP_EOL;

require "result.php";