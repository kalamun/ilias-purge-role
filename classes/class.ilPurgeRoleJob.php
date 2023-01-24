<?php

class ilPurgeRoleJob extends ilCronJob
{
  public const JOB_ID = 'PurgeRole';
  public const JOB_NAME = ilPurgeRolePlugin::PLUGIN_NAME.' CronJob';

  public function __construct()
  {
  }

  public function getId(): string
  {
      return self::JOB_ID;
  }

  public function getTitle(): string
  {
      return self::JOB_NAME;
  }

  public function hasAutoActivation(): bool
  {
      return true;
  }

  public function hasFlexibleSchedule(): bool
  {
      return true;
  }

  public function getDefaultScheduleType(): int
  {
      return ilCronJob::SCHEDULE_TYPE_DAILY;
  }

  public function getDefaultScheduleValue(): int
  {
      return 1;
  }

  public function hasCustomSettings(): bool
  {
      return false;
  }

  public function addCustomSettingsToForm(ilPropertyFormGUI $a_form)
  {
  }

  public function run(): ilCronJobResult
  {
    $result = new ilCronJobResult();
    $result->setStatus(ilCronJobResult::STATUS_OK);
    $result->setCode(200);

    global $DIC, $ilDB;

    $rbacreview = $DIC['rbacreview'];
    $rbacadmin = $DIC['rbacadmin'];

    $table_prefix = "purgerole_";
    $table_name = $table_prefix . "rules";

    $current_day = date("j");
    $current_month = date("n");

    $db_query = $ilDB->query("SELECT * FROM " . $table_name . " WHERE `day`='" . $current_day . "' AND `month`='" . $current_month . "' AND `active`='1'");
    $db_values = [];
    while($db_row = $ilDB->fetchAssoc($db_query)) {
        $role_id = $db_row["role_id"];

        // remove all users
        $users = $rbacreview->assignedUsers($role_id);
        foreach( $users as $user_id ) {
          $rbacadmin->deassignUser($role_id, $user_id);
        }
    }

    return $result;
  }

}