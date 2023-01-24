<?php
//include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");
include_once("./Services/Object/classes/class.ilObjectGUI.php");

/**
 * Class ilPurgeRoleUIHookGUI
 * @author            Kalamun <rp@kalamun.net>
 * @version $Id$
 * @ingroup ServicesUIComponent
 * @ilCtrl_isCalledBy ilPurgeRoleUIHookGUI: ilUIPluginRouterGUI, ilAdministrationGUI, ilPurgeRoleGUI
 */

class ilPurgeRoleUIHookGUI extends ilUIHookPluginGUI {
  protected $dic;
  protected $plugin;
  protected $lng;
  protected $request;
  protected $user;
  protected $ctrl;
  protected $object;

  const EXPIRATION_DATE_GUI = "expiration_date_gui";

  public function __construct()
  {
    global $DIC;
    $this->dic = $DIC;
    $this->plugin = ilPurgeRolePlugin::getInstance();
    $this->lng = $this->dic->language();
    // $this->lng->loadLanguageModule("assessment");
    $this->request = $this->dic->http()->request();
    $this->user = $DIC->user();
    $this->ctrl = $DIC->ctrl();
    $this->object = $DIC->object();

    //$this->plugin->includeClass('views/class.ilPurgeRoleGUI.php');
  }

  public function performCommand(/*string*/ $cmd)/*:void*/
  {
        switch ($cmd)
        {
          case self::EXPIRATION_DATE_GUI:
              $this->{$cmd}();
              break;

          default:
              break;
        }
  }

  /**
	 * Modify HTML output of GUI elements. Modifications modes are:
	 * - ilUIHookPluginGUI::KEEP (No modification)
	 * - ilUIHookPluginGUI::REPLACE (Replace default HTML with your HTML)
	 * - ilUIHookPluginGUI::APPEND (Append your HTML to the default HTML)
	 * - ilUIHookPluginGUI::PREPEND (Prepend your HTML to the default HTML)
	 *
	 * @param string $a_comp component
	 * @param string $a_part string that identifies the part of the UI that is handled
	 * @param string $a_par array of parameters (depend on $a_comp and $a_part)
	 *
	 * @return array array with entries "mode" => modification mode, "html" => your html
	 */
	function getHTML($a_comp = false, $a_part = false, $a_par = array()) {
    return ["mode" => ilUIHookPluginGUI::KEEP, "html" => ""];
  }


  /**
	 * Modify GUI objects, before they generate ouput
	 *
	 * @param string $a_comp component
	 * @param string $a_part string that identifies the part of the UI that is handled
	 * @param string $a_par array of parameters (depend on $a_comp and $a_part)
	 */
  function modifyGUI($a_comp, $a_part, $a_par = array())
	{
		global $ilAccess, $ilCtrl, $ilTabs;

    if($a_part == "sub_tabs") {
      if($ilCtrl->getCmdClass() == "ilobjrolegui") {
        $ilTabs->addSubTab(
          "config",
          $this->lng->txt("role_edit"),
          false
        );
        $ilTabs->addSubTab(
          "expirationdate",
          $this->lng->txt("expiration_rules_this"),
          $ilCtrl->getLinkTarget($this, self::EXPIRATION_DATE_GUI)
          //$ilCtrl->getLinkTargetByClass([self::class, ilObjRoleGUI::class], self::EXPIRATION_DATE_GUI)
        );
      }
  
      if ($ilCtrl->getCmdClass()  == 'ilPurgeRolepagegui')
      {
        // reuse the tabs that were saved from the test gui
      }
    }
	}
  
  /**
   * Checks if the received command can be executed and redirects the command into the structure presentation class
   * for further processing
   * @throws Exception
   */
  public function executeCommand()
  {
    $query = $this->request->getQueryParams();
    $cmd = $this->ctrl->getCmd();

    if (!isset($cmd)) {
      ilUtil::sendFailure($this->plugin->txt("missing_get_parameter_cmd"), true);
      $this->ctrl->redirectToURL("ilias.php");
    }

    if ($this->user->isAnonymous()) {
      $this->ctrl->redirectToURL('login.php');
    }

    $next_class = $this->ctrl->getNextClass($this);

    switch (strtolower($next_class)) {
        default:
            switch ($cmd) {
              case self::EXPIRATION_DATE_GUI:
//                    $this->{$cmd}();
                $this->plugin->includeClass('views/class.ilPurgeRoleGUI.php');
                (new ilPurgeRoleGUI($this->dic))->performCommand($cmd, $query);
                break;

              default:
                break;
            }
            break;
    }
  }  

  /**
   * Returns the array used to replace the html content
   * @param string $mode
   * @param string $html
   * @return string[]
   */
  protected function uiHookResponse(string $mode = self::KEEP, string $html = "") : array
  {
    return ['mode' => $mode, 'html' => $html];
  }

  public function expiration_date_gui() {
    global $tpl, $ilCtrl, $lng;

		require_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		$form->setFormAction($ilCtrl->getFormAction($this));

        $select_input = new ilSelectInputGUI($this->plugin->txt($prefix . "_title_long"), $class);
        $select_input->setOptions([]);
        $select_input->setValue("value");
        $select_input->setInfo($this->plugin->txt($prefix . "_description") . '<br /><em>'.sprintf($this->plugin->txt('evaluation_info_id'), $class).'</em>');
        $form->addItem($select_input);
        
        $form->setTitle($this->plugin->txt('test_evaluation_settings'));
        $form->addCommandButton("saveTestSettings", $lng->txt("save"));

		$tpl->setContent($form->getHTML());
  }

  public function setCreationMode($a_mode = false) {
    return false;
  }
}