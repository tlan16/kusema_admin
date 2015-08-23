<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
echo 'START ' . basename(__FILE__) . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;

$class = 'Question';
$title = $class . '_title_' . getTimeString();
$content = $class . '_content_' . getTimeString();
$refId = $class . '_ref_' . getTimeString();
$author = UserAccount::get(24);
$authorName = 'alias of ' . $author->getPerson()->getFullName();
$active = true;

$obj = $class::create($title, $content, $refId, $author, $authorName, $active);

echo 'TESTING ' . $class . PHP_EOL;
echo 'JSON: ' . PHP_EOL . print_r(getRealJson($obj->getJson()), true);

$class = 'Answer';
$title = $class . '_title_' . getTimeString();
$content = $class . '_content_' . getTimeString();
$refId = $class . '_ref_' . getTimeString();
$author = UserAccount::get(24);
$authorName = 'alias of ' . $author->getPerson()->getFullName();
$active = true;

$obj = $obj->addAnswer($title, $content, $refId, $author, $authorName, $active);

echo 'TESTING ' . $class . PHP_EOL;
echo 'JSON: ' . PHP_EOL . print_r(getRealJson($obj->getJson()), true);

$class = 'Comments';
$title = $class . '_title_' . getTimeString();
$content = $class . '_content_' . getTimeString();
$refId = $class . '_ref_' . getTimeString();
$author = UserAccount::get(24);
$authorName = 'alias of ' . $author->getPerson()->getFullName();
$active = true;

$obj = $obj->addComments($title, $content, $refId, $author, $authorName, $active);

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