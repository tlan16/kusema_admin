<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
echo 'START ' . basename(__FILE__) . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;

$file = dirname(__FILE__) . '/../../../resource/eng_units.json';
$content = file($file);

echo 'data size: ' . count($content) . PHP_EOL;
echo 'there are total of ' . count(Unit::getAll()) . ' Units' . PHP_EOL;
try {
	$transStarted = false;
	try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
	$rowCount = 0;
	foreach($content as $line)
	{
		echo $rowCount . ': ' . print_r($line, true);
		if(($line = trim($line)) !== '')
		{
			$data = json_decode($line);
			echo 'data: ' . PHP_EOL . print_r($data,true);
			$code = trim($data->code);
			$name = trim(str_replace($code, '', $data->name));
			$obj = Unit::create($name, $code, intval($code[3]));
			echo get_class($obj) . ": " . PHP_EOL . print_r(json_decode(json_encode($obj->getJson()),true),true);
		}
		$rowCount++;
	}
	if($transStarted === false)
		Dao::commitTransaction();
	else {echo "transStarted === true, nothing is commited! \n";}
	echo 'there are total of ' . count(Unit::getAll()) . ' Units' . PHP_EOL;
}
catch(Exception $ex)
{
	if($transStarted === false)
		Dao::rollbackTransaction();
	throw $ex;
}