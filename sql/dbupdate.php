<#1>
<?php
//$table_prefix = "cron_crnhk_xpurgerole_";
$table_prefix = "purgerole_";
$table_name = $table_prefix . "rules";

$fields = [
	'role_id' => [
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	],
	'day' => [
		'type' => 'integer',
		'length' => 2,
		'notnull' => true
	],
	'month' => [
		'type' => 'integer',
		'length' => 2,
		'notnull' => true
	],
	'active' => [
		'type' => 'integer',
		'length' => 1,
		'notnull' => true
	],
];
 
$ilDB->createTable($table_name, $fields);
$ilDB->addPrimaryKey($table_name, array("role_id"));
?>