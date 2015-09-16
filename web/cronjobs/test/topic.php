<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
echo 'START ' . basename(__FILE__) . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;

$file = dirname(__FILE__) . '/../../../resource/eng_topics.json';
$content = file($file);

echo 'data size: ' . count($content) . PHP_EOL;
echo 'there are total of ' . Topic::countByCriteria('active = 1') . ' Topics' . PHP_EOL;
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
			$name = trim($data->name);
			$obj = Topic::create($name);
			echo get_class($obj) . ": " . PHP_EOL . print_r(json_decode(json_encode($obj->getJson()),true),true);
		}
		$rowCount++;
	}
	if($transStarted === false)
		Dao::commitTransaction();
	else {echo "transStarted === true, nothing is commited! \n";}
	echo 'there are total of ' . Topic::countByCriteria('active = 1') . ' Topics' . PHP_EOL;
}
catch(Exception $ex)
{
	if($transStarted === false)
		Dao::rollbackTransaction();
	throw $ex;
}