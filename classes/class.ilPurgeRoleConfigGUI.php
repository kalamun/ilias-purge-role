<?php

/**
 * Config screen
 */
class ilPurgeRoleConfigGUI extends ilPluginConfigGUI {

    const PLUGIN_CLASS_NAME = ilPurgeRolePlugin::class;
    const CMD_CONFIGURE = "configure";
    const CMD_UPDATE_CONFIGURE = "updateConfigure";
    const LANG_MODULE = "purgerole_";
    const TAB_CONFIGURATION = "show_purge_role_config";
    const CMD_SAVE = "savePurgeRole";
    const TAB_DEV_TOOLS = "dev_tools";

    const TYPE_GLOBAL_AU = 1;
    const TYPE_GLOBAL_UD = 2;
    const TYPE_LOCAL_AU = 3;
    const TYPE_LOCAL_UD = 4;
    const TYPE_ROLT_AU = 5;
    const TYPE_ROLT_UD = 6;

    protected $dic;
    protected $plugin;
    protected $lng;
    protected $request;
    protected $user;
    protected $ctrl;
    protected $object;
  
    public function __construct()
    {
      global $DIC;
      $this->dic = $DIC;
      $this->plugin = ilPurgeRolePlugin::getInstance();
      $this->lng = $this->dic->language();
      $this->request = $this->dic->http()->request();
      $this->user = $this->dic->user();
      $this->ctrl = $this->dic->ctrl();
      $this->object = $this->dic->object();
    }
    
    public function performCommand(/*string*/ $cmd)/*:void*/
    {
        $this->plugin = $this->getPluginObject();
        $this->initTabs();

        switch ($cmd)
		{
			case self::CMD_CONFIGURE:
			case self::CMD_RELOAD_CTRL_STRUCTURE:
			case self::CMD_RELOAD_DATABASE:
			case self::CMD_RELOAD_LANGUAGES:
			case self::CMD_RELOAD_PLUGIN_XML:
            case self::CMD_UPDATE_CONFIGURE:
                $this->{$cmd}();
                break;

            default:
                break;
		}
    }

	protected function initTabs($a_mode = "")
	{
		global $ilCtrl, $ilTabs;

		$ilTabs->addTab(
            self::TAB_CONFIGURATION,
            $this->plugin->txt("purge_role_config"),
            $ilCtrl->getLinkTarget($this, self::CMD_CONFIGURE)
        );
		$ilTabs->setTabActive(self::TAB_CONFIGURATION);
	}

    protected function configure()/*: void*/
    {
        global $tpl, $ilCtrl, $lng, $DIC, $ilDB;

		require_once("./Services/Table/classes/class.ilTableGUI.php");
		require_once("./Services/Form/classes/class.ilFormGUI.php");

        $form_action = $ilCtrl->getFormAction($this);
        $table_prefix = "purgerole_";
        $table_name = $table_prefix . "rules";

        $days = [];
        for($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }

        $months = [];
        for($i = 1; $i <= 12; $i++) {
            $months[$i] = $this->plugin->txt("month_" . $i);
        }

        $rbacreview = $DIC['rbacreview'];
        $ilUser = $DIC['ilUser'];
        
        $this->role_folder_id = $role_folder_id;

        include_once './Services/AccessControl/classes/class.ilObjRole.php';
        
        $type = ilRbacReview::FILTER_ALL;
        $filter = '';

        $role_list = $rbacreview->getRolesByFilter(
            $type,
            0,
            ''
        );
        
        $counter = 0;
        $rows = array();
        foreach ((array) $role_list as $role) {
            if (
                $role['parent'] and
                    (
                        $GLOBALS['DIC']['tree']->isDeleted($role['parent']) or
                        !$GLOBALS['DIC']['tree']->isInTree($role['parent'])
                    )
            ) {
                continue;
            }
            $title = ilObjRole::_getTranslation($role['title']);
            if (strlen($filter_orig)) {
                if (stristr($title, $filter_orig) == false) {
                    continue;
                }
            }
            
            $rows[$counter]['title_orig'] = $role['title'];
            $rows[$counter]['title'] = $title;
            $rows[$counter]['description'] = $role['description'];
            $rows[$counter]['obj_id'] = $role['obj_id'];
            $rows[$counter]['parent'] = $role['parent'];
            $rows[$counter]['type'] = $role['type'];

            $auto = (substr($role['title'], 0, 3) == 'il_' ? true : false);


            // Role templates
            if ($role['type'] == 'rolt') {
                $rows[$counter]['rtype'] = $auto ? self::TYPE_ROLT_AU :	self::TYPE_ROLT_UD;
            } else {
                // Roles
                if ($role['parent'] == ROLE_FOLDER_ID) {
                    if ($role['obj_id'] == ANONYMOUS_ROLE_ID or $role['obj_id'] == SYSTEM_ROLE_ID) {
                        $rows[$counter]['rtype'] = self::TYPE_GLOBAL_AU;
                    } else {
                        $rows[$counter]['rtype'] = self::TYPE_GLOBAL_UD;
                    }
                } else {
                    $rows[$counter]['rtype'] = $auto ? self::TYPE_LOCAL_AU : self::TYPE_LOCAL_UD;
                }
            }

            ++$counter;
        }

        ob_start();

        // TODO: find a proper way to save data
        if( !empty($_GET['rtoken']) && !empty($_POST['purge']) ) {
            foreach( $_POST['purge'] as $role_id => $settings ) {
                $ilDB->replace($table_name, [
                    "role_id" => ["integer", $role_id] // primary keys
                ], [
                    "day" => ["integer", intval($settings['day'])], // other values
                    "month" => ["integer", intval($settings['month'])],
                    "active" => ["integer", intval(!empty($settings['active']))],
                ]);
            }
        }

        $db_query = $ilDB->query("SELECT * FROM " . $table_name);
        $db_values = [];
        while($db_row = $ilDB->fetchAssoc($db_query)) {
            $db_values[ $db_row["role_id"] ] = $db_row;
        }
        ?>
        <form action="<?= $form_action; ?>" method="post">
            <div class="ilTableOuter">
                <div class="table-responsive">
                    <div class="submit ilTableCommandRow form-inline">
                        <input type="submit" class="btn btn-default" value="<?= $this->plugin->txt("save"); ?>" />
                    </div>
                    <table class="table table-striped fullwidth">
                        <thead>
                            <tr>
                                <th><?= $this->plugin->txt("role"); ?></th>
                                <th><?= $this->plugin->txt("day"); ?></th>
                                <th><?= $this->plugin->txt("month"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($rows as $row) {
                            if($row['rtype'] != self::TYPE_GLOBAL_AU && $row['rtype'] != self::TYPE_GLOBAL_UD) continue;
                            ?>
                            <tr>
                                <td>
                                    <b><?= $row['title']; ?></b><br />
                                    <?= $row['description']; ?>
                                </td>
                                <td style="vertical-align: middle;">
                                    <?php
                                    $select_input = new ilSelectInputGUI($this->plugin->txt("day"), $class);
                                    $select_input->setPostVar("purge[" . $row['obj_id'] . "][day]");
                                    $select_input->setOptions($days);
                                    $select_input->setValue($db_values[ $row['obj_id'] ]['day']);
                                    echo $select_input->render();
                                    ?>
                                </td>
                                <td style="vertical-align: middle;">
                                    <?php
                                    $select_input = new ilSelectInputGUI($this->plugin->txt("month"), $class);
                                    $select_input->setPostVar("purge[" . $row['obj_id'] . "][month]");
                                    $select_input->setOptions($months);
                                    $select_input->setValue($db_values[ $row['obj_id'] ]['day']);
                                    echo $select_input->render();
                                    ?>
                                </td>
                                <td style="vertical-align: middle;">
                                    <?php
                                    $checkbox_input = new ilCheckboxInputGUI($this->plugin->txt("purge_active"), $class);
                                    $checkbox_input->setPostVar("purge[" . $row['obj_id'] . "][active]");
                                    $checkbox_input->setOptionTitle($this->plugin->txt("purge_active"));
                                    $checkbox_input->setChecked(!!$db_values[ $row['obj_id'] ]['active']);
                                    $checkbox_input->setValue(true);
                                    echo $checkbox_input->render();
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <div class="submit ilTableCommandRow form-inline">
                        <input type="submit" class="btn btn-default" value="<?= $this->plugin->txt("save"); ?>" />
                    </div>
                </div>
            </div>
        </form>
        <?php

        $output = ob_get_clean();

        $tpl->setContent($output);
    }

}
