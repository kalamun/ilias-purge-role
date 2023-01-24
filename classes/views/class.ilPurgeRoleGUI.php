<?php

/**
 * Class ilPurgeRoleGUI
 * @package PurgeRole
 * @version $Id$
 * @author  Kalamun <rp@kalamun.net>
 * @ilCtrl_isCalledBy ilPurgeRoleGUI: ilPurgeRoleUIHookGUI
 * @ilCtrl_isCalledBy ilPurgeRoleGUI: ilObjRoleGUI
 */

class ilPurgeRoleGUI {

  protected $plugin;

  const PLUGIN_CLASS_NAME = ilPurgeRolePlugin::class;
  const CMD_EXPIRATION_DATE = "expiration_date_gui";
  const CMD_UPDATE_CONFIGURE = "updateConfigure";
  const LANG_MODULE = "config";
  const TAB_CONFIGURATION = "show_purge_role_config";

  public function __construct()
  {
  }
  
  public function performCommand(/*string*/ $cmd)/*:void*/
  {
    $this->plugin = ilPurgeRolePlugin::getInstance();
    $this->initTabs();

    switch ($cmd)
    {
      case self::CMD_EXPIRATION_DATE:
          $this->{$cmd}();
          break;

      default:
          break;
    }
  }

  protected function initTabs($a_mode = "")
  {
    global $ilCtrl, $ilTabs;
  }

  function expiration_date_gui()
  {
    global $tpl, $ilCtrl, $lng;

		require_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		$form->setFormAction($ilCtrl->getFormAction($this));

        $days = range(1, 31, 1);
        $months = range(1, 12, 1);

        $select_day = new ilSelectInputGUI($this->plugin->txt($prefix . "_title_long"), $class);
        $select_day->setOptions($days);
        $select_day->setValue("value");
        $select_day->setInfo($this->plugin->txt($prefix . "_description") . '<br /><em>'.sprintf($this->plugin->txt('evaluation_info_id'), $class).'</em>');
        $form->addItem($select_day);
        
        $select_month = new ilSelectInputGUI($this->plugin->txt($prefix . "_title_long"), $class);
        $select_month->setOptions($months);
        $select_month->setValue("value");
        $select_month->setInfo($this->plugin->txt($prefix . "_description") . '<br /><em>'.sprintf($this->plugin->txt('evaluation_info_id'), $class).'</em>');
        $form->addItem($select_month);
        
        $form->setTitle($this->plugin->txt('test_evaluation_settings'));
        $form->addCommandButton("saveTestSettings", $lng->txt("save"));

		$tpl->setContent($form->getHTML());
  }

}