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
    protected static $instance = null;
    private static $plugin_object = null;

    public function __construct()
    {
        parent::__construct();
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

    public function getCronJobInstance(/*string*/ $a_job_id)/*: ?ilCronJob*/
    {
        return new ilPurgeRoleJob();
    }

    public function reloadCtrlStructure() : void
    {
        // https://github.com/ILIAS-eLearning/ILIAS/blob/release_6/Services/Component/classes/class.ilPlugin.php#L1078-L1091
        $structure_reader = new ilCtrlStructureReader();
        $structure_reader->readStructure(
            true,
            "./" . parent::getDirectory(),
            parent::getPrefix(),
            parent::getDirectory()
        );
        self::dic()->ctrl()->insertCtrlCalls(
            strtolower(ilObjComponentSettingsGUI::class),
            ilPlugin::getConfigureClassName(["name" => parent::getPluginName()]),
            parent::getPrefix()
        );

        // Clear loaded ctrl cache for force reload new node ids from database
        /*self::dic()->ctrl()->class_cid = [];
        self::dic()->ctrl()->cid_class = [];
        self::dic()->ctrl()->info_read_class = [];
        self::dic()->ctrl()->info_read_cid = [];
        self::dic()->ctrl()->initBaseClass(strval(filter_input(INPUT_GET, "baseClass")));*/
    }

    public function reloadDatabase() : void
    {
        parent::updateDatabase();
    }

    public function reloadLanguages() : void
    {
        parent::updateLanguages();
    }

    protected function beforeUninstall() : bool
    {
        return parent::beforeUninstall();
    }
}
