<?php

require_once 'CRM/Core/Page.php';
require_once 'CRM/Workflow/DAO/Workflow.php';

class CRM_Workflow_Page_Workflow_List extends CRM_Core_Page {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    private static $_actionLinks;

    /**
     * Get the action links for this page.
     *
     * @param null
     *
     * @return  array   array of action links that we need to display for the browse screen
     * @access public
     */ static function &actionLinks() {
    // check if variable _actionsLinks is populated
    if (!isset(self::$_actionLinks)) {
        // helper variable for nicer formatting
        $deleteExtra = ts('Are you sure you want to delete this workflow?');
        $copyExtra = ts('Are you sure you want to make a copy of this workflow?');
        self::$_actionLinks = array(
            CRM_Core_Action::BROWSE => array(
                'name' => ts('View and Edit Steps'),
                'url' => 'civicrm/workflows/steps',
                'qs' => 'reset=1&action=browse&wid=%%wid%%',
                'title' => ts('View and Edit Steps'),
            ),
            CRM_Core_Action::UPDATE => array(
                'name' => ts('Settings'),
                'url' => 'civicrm/workflows/update',
                'qs' => 'action=update&reset=1&wid=%%wid%%',
                'title' => ts('Edit Workflow Settings'),
            ),
            CRM_Core_Action::VIEW => array(
                'name' => ts('Workflow Link'),
                'url' => 'civicrm/workflow',
                'qs' => 'wid=%%wid%%&reset=1',
                'title' => ts('Link to the Workflow page'),
                'fe' => TRUE,
            ),
            CRM_Core_Action::DISABLE => array(
                'name' => ts('Disable'),
                'url' => CRM_Utils_System::currentPath(),
                'qs' => 'action=disable&wid=%%wid%%&reset=1',
                'title' => ts('Disable Workflow'),
            ),
            CRM_Core_Action::ENABLE => array(
                'name' => ts('Enable'),
                'url' => CRM_Utils_System::currentPath(),
                'qs' => 'action=enable&wid=%%wid%%&reset=1',
                'title' => ts('Enable Workflow'),
            ),
            CRM_Core_Action::DELETE => array(
                'name' => ts('Delete'),
                'url' => CRM_Utils_System::currentPath(),
                'qs' => 'action=delete&reset=1&wid=%%wid%%',
                'title' => ts('Delete Workflow'),
                'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
            ),
            CRM_Core_Action::COPY => array(
                'name' => ts('Clone Workflow'),
                'url' => CRM_Utils_System::currentPath(),
                'qs' => 'action=copy&reset=1&wid=%%wid%%',
                'title' => ts('Make a Copy of this workflow'),
                'extra' => 'onclick = "return confirm(\'' . $copyExtra . '\');"',
            ),
          CRM_Core_Action::EXPORT => array(
            'name' => ts('Export Workflow'),
            'url' => CRM_Utils_System::currentPath(),
            'qs' => 'action=export&reset=1&wid=%%wid%%',
            'title' => ts('Export this workflow to a json file')
          ),
        );
    }
    return self::$_actionLinks;
}
    function getBAOName() {
        return 'CRM_Workflow_BAO_Workflow';
    }

    function run() {
        if (array_key_exists("action", $_GET)) {
            switch ($_GET['action']) {
                case 'enable':
                    CRM_Workflow_BAO_Workflow::setIsActive($_GET['wid'], true);
                    break;

                case 'disable':
                    CRM_Workflow_BAO_Workflow::setIsActive($_GET['wid'], false);
                    break;

                case 'delete':
                    CRM_Workflow_BAO_Workflow::del($_GET['wid']);
                    break;

                case 'copy':
                    CRM_Workflow_BAO_Workflow::copy($_GET['wid']);
                    break;
                case 'export':
                    CRM_Workflow_BAO_Workflow::exportJSON($_GET['wid']);
                    break;
            }
        }



        CRM_Utils_System::setTitle(ts('Workflows'));
        $this->assign('rows', CRM_Workflow_BAO_Workflow::getWorkflows());
        parent::run();
    }

}
