<?php


//TODO: Add fields to allow for setting the "next" button text per step
//TODO: Forward to list after save
//TODO: Allow custom javascript per step
//TODO: cross browser check
//TODO: Fix Breadcrumbs


require_once 'CRM/Core/Page.php';

class CRM_Workflow_Page_Profiles extends CRM_Core_Page {
    function run() {
        $wid = CRM_Utils_Request::retrieve('wid', 'Positive', $this, false, 0);

        if ($wid) {
            $wsql = "SELECT * FROM civicrm_workflow WHERE id = {$wid} LIMIT 1";
            $dao =& CRM_Core_DAO::executeQuery($wsql);
            if (!$dao->fetch()) {
                $workflow = null;
            } else {
                $workflow = (array) $dao;
            }

            $dsql = "SELECT * FROM civicrm_workflow_detail WHERE workflow_id = {$wid} ORDER BY `order`";
            $dao =& CRM_Core_DAO::executeQuery($dsql);

            $details = array();

            while ($dao->fetch()) {
                $details[$dao->id] = (array) $dao;

                if($dao->entity_table == "Profile") {
                    $result = civicrm_api3('UFGroup', 'get', array(
                        'sequential' => 1,
                        'return' => "title",
                        'id' => $dao->entity_id,
                    ));
                    $details[$dao->id]['name'] = $result['values'][0]['title'];
                }
                if($dao->entity_table == "Page") {
                    $result = civicrm_api3('ContributionPage', 'get', array(
                        'sequential' => 1,
                        'return' => "title",
                        'id' => $dao->entity_id,
                    ));
                    $details[$dao->id]['name'] = $result['values'][0]['title'];
                }

            }

            CRM_Utils_System::setTitle(ts("Details for ".$workflow['name'].":"));



        } else {
            $workflow = null;
            $details = array();
        }

        $result = civicrm_api3('ContributionPage', 'get', array(
            'sequential' => 1,
            'return' => array("id", "title"),
        ));
        $pages = $result['values'];
        $this->assign("pages", $pages);

        $this->assign('workflow', $workflow);
        $this->assign('details', $details);

        //Add Stylesheet
        CRM_Core_Resources::singleton()->addStyleFile('org.botany.workflow', 'workflow_profiles.css');

        CRM_UF_Page_ProfileEditor::registerProfileScripts();
        parent::run();
    }



}
