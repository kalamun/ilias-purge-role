<#1>
<?php
$table_name = "cron_crnhk_xpurgerole";

if(!$ilDB->tableExists($table_name)) {
	$fields = [
		'role_id' => [
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		],
		'rule_id' => [
			'type' => 'integer',
			'length' => 2,
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
	$ilDB->addPrimaryKey($table_name, ["role_id"]);
}
?>