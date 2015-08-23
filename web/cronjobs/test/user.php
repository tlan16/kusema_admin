<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
echo 'START ' . basename(__FILE__) . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;

$class = 'UserProfileType';

$obj = $class::create('Subscription');

echo 'TESTING ' . $class . PHP_EOL;
echo 'JSON: ' . PHP_EOL . print_r(getRealJson($obj->getJson()), true);

$obj = $class::create('Enrollment');

echo 'TESTING ' . $class . PHP_EOL;
echo 'JSON: ' . PHP_EOL . print_r(getRealJson($obj->getJson()), true);

function getRealJson($json)
{
	return json_decode(json_encode($json), true);
}
function getTimeString()
{
	$obj = UDate::now(UDate::TIME_ZONE_MELB);
	return $obj->format('s_i_h_d_M');
}