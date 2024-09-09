<?php
/**
 * Class ilPurgeRolePlugin
 * @author  Kalamun <rp@kalamun.net>
 * @version $Id$
 */

require_once('class.ilPurgeRoleJob.php');

class ilPurgeRolePlugin extends ilCronHookPlugin
{
    const PLUGIN_NAME = "PurgeRole";
    const PLUGIN_ID = "xpurgerole";
    protected static $instance = null;
    private static $plugin_object = null;
    
    public function __construct()
    {
        global $DIC;
        $this->db = $DIC->database();
        parent::__construct(
            $this->db, $DIC["component.repository"], self::PLUGIN_ID
        );
    }

    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }

    public function getCronJobInstances() : array
    {
        return [new ilPurgeRoleJob()];
    }

    public function getCronJobInstance(string $a_job_id): ilCronJob
    {
        return new ilPurgeRoleJob();
    }

    protected function beforeUninstall() : bool
    {
        global $ilDB;
        $table_name = "cron_crnhk_xpurgerole";
        if($ilDB->tableExists($table_name)) {
            $ilDB->query("DROP TABLE " . $table_name);
        }
        return parent::beforeUninstall();
    }
}
