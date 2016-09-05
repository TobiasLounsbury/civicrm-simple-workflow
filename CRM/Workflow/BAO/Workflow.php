<?php

require_once 'CRM/Workflow/DAO/Workflow.php';

class CRM_Workflow_BAO_Workflow extends CRM_Workflow_DAO_Workflow {

  /**
   * class constructor
   */
  function __construct() {
    parent::__construct();
  }

  /**
   * Takes an associative array and creates a new workflow
   *
   * This function extracts all the params it needs to initialize the created
   * Workflow. The params array could contain additional unused name/value
   * pairs
   *
   * @param array  $params (reference ) an assoc array of name/value pairs
   *
   * @return object CRM_Workflow_DAO_Workflow object
   * @access public
   * @static
   */
  static function &add(&$params) {
    //require_once 'CRM/Utils/Date.php';

    //TODO: Fix this garbage
    $workf = new CRM_Workflow_DAO_Workflow();
    $workf->name = $params['name'];
    $workf->description = $params['description'];
    $workf->login_form_id = $params['login_form_id'];

    if (! empty($params['id'])) {
      $workf->id = $params['id'];
    }

    $workf->is_active = CRM_Utils_Array::value('is_active', $params) ? 1 : 0;
    $workf->require_login = CRM_Utils_Array::value('require_login', $params) ? 1 : 0;

    $workf->pre_message = $params['pre_message'];
    $workf->post_message = $params['post_message'];
    $options = (array_key_exists("options", $params) && $params['options']) ? $params['options'] : array();
    $workf->options = json_encode($options);

    $id = empty($params['id']) ? NULL : $params['id'];
    $op = $id ? 'edit' : 'create';
    CRM_Utils_Hook::pre($op, 'Workflow', $id, $params);
    $workf->save();
    CRM_Utils_Hook::post($op, 'Workflow', $workf->id, $workf);

    return $workf;
  }

  /**
   * Takes a bunch of params that are needed to match certain criteria and
   * retrieves the relevant objects. Typically the valid params are only
   * contact_id. We'll tweak this function to be more full featured over a period
   * of time. This is the inverse function of create. It also stores all the retrieved
   * values in the default array
   *
   * @param array $params   (reference) an assoc array of name/value pairs
   * @param array $defaults (reference) an assoc array to hold the flattened values
   *
   * @return object CDM_BAO_Item object on success, null otherwise
   * @access public
   * @static
   */
  static function retrieve(&$params, &$defaults) {
    $item = new CRM_Workflow_DAO_Workflow();
    $item->copyValues($params);
    if ($item->find(true)) {
      CRM_Core_DAO::storeValues($item, $defaults);
      $item->options = json_decode($item->options, true);
      return $item;
    }
    return null;
  }

  static function getWorkflow($wid) {
    if (!$wid) {
      return null;
    }
    $workflowTable = CRM_Workflow_DAO_Workflow::$_tableName;
    $wsql = "SELECT * FROM `".$workflowTable."` WHERE id = {$wid} LIMIT 1";
    $dao =& CRM_Core_DAO::executeQuery($wsql);

    $result = null;
    if ($dao->fetch()) {
      $result = $dao->toArray();
    }
    $detailTable = CRM_Workflow_DAO_WorkflowDetail::$_tableName;
    $dsql = "SELECT `entity_id` as `contains_page` FROM `".$detailTable."` WHERE workflow_id = {$wid} AND `entity_table` = 'Page'";
    $dao =& CRM_Core_DAO::executeQuery($dsql);
    if ($dao->fetch()) {
      $result['contains_page'] = $dao->contains_page;
    } else {
      $result['contains_page'] = 0;
    }

    if(array_key_exists("options", $result) && is_string($result['options'])) {
      $result['options'] = json_decode($result['options'], true);
    }

    return $result;
  }

  static function getWorkflowDetails($wid, $AllowNullBreadcrumbs = true) {
    $extraWhere = ($AllowNullBreadcrumbs) ? "" : " AND breadcrumb <> ''";
    $detailTable = CRM_Workflow_DAO_WorkflowDetail::$_tableName;
    $dsql = "SELECT * FROM `".$detailTable."` WHERE workflow_id = {$wid}{$extraWhere} ORDER BY `order`";
    $dao =& CRM_Core_DAO::executeQuery($dsql);
    $steps = array();
    while ($dao->fetch()) {
      $steps[$dao->order] = $dao->toArray();
      $steps[$dao->order]['options'] = json_decode($dao->options, true);
    }
    return $steps;
  }

  static function getWorkflowPages($is_active = true) {
    $detailTable = CRM_Workflow_DAO_WorkflowDetail::$_tableName;
    $workflowTable = CRM_Workflow_DAO_Workflow::$_tableName;
    if ($is_active) {
      $sql = "SELECT workflow_id,entity_id FROM `".$detailTable."` LEFT JOIN `".$workflowTable."` AS `wt` on workflow_id=wt.id WHERE entity_table = 'Page' AND is_active = 1";
    } else {
      $sql = "SELECT workflow_id,entity_id FROM `".$detailTable."` WHERE entity_table = 'Page'";
    }
    $result = array();
    $dao =& CRM_Core_DAO::executeQuery($sql, array());
    while ($dao->fetch()) {
      $result[$dao->entity_id] = $dao->workflow_id;
    }
    return $result;
  }

  static function getWorkflows() {

    $workflowTable = CRM_Workflow_DAO_Workflow::$_tableName;

    $sql = "SELECT * FROM `".$workflowTable."`";
    $dao =& CRM_Core_DAO::executeQuery($sql, array());
    $allLinks = CRM_Workflow_Page_Workflow_List::actionLinks();
    while ($dao->fetch()) {

      $result[$dao->id] = $dao->toArray();

      // form all action links
      $action = array_sum(array_keys($allLinks));

      // update enable/disable links depending on price_set properties.

      if ($dao->is_active) {
        $action -= CRM_Core_Action::ENABLE;
      }
      else {
        $action -= CRM_Core_Action::DISABLE;
      }

      $actionLinks = $allLinks;
      //CRM-10117
      $result[$dao->id]['action'] = CRM_Core_Action::formLink($actionLinks, $action,
        array('wid' => $dao->id),
        ts('more'),
        FALSE,
        'workflow.row.actions',
        'Workflow',
        $dao->id
      );
    }
    return (empty($result)) ? false : $result;
  }

  /**
   * update the is_active flag in the db
   *
   * @param int      $id        id of the database record
   * @param boolean  $is_active value we want to set the is_active field
   *
   * @return Object             DAO object on sucess, null otherwise
   *
   * @access public
   * @static
   */
  static function setIsActive($id, $is_active) {
    return CRM_Core_DAO::setFieldValue('CRM_Workflow_DAO_Workflow', $id, 'is_active', $is_active);
  }

  static function getName($wid) {
    if (!$wid) {
      return false;
    }
    $workflowTable = CRM_Workflow_DAO_Workflow::$_tableName;
    $sql = "SELECT name FROM `".$workflowTable."` WHERE id = {$wid} LIMIT 1";
    $dao =& CRM_Core_DAO::executeQuery($sql);
    if (!$dao->fetch()) {
      return false;
    } else {
      $a = $dao->toArray();
      return $a['name'];
    }
  }

  static function isEnabled($code) {
    if ($code['is_active'] == 1) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Function to delete workflow
   *
   * @param  wid int, Id of the workflow to be deleted.
   *
   * @access public
   * @static
   * @return true on success else false
   */
  static function del($wid) {
    $workflow = new CRM_Workflow_DAO_Workflow();
    $workflow->id = $wid;

    if ($workflow->find(TRUE)) {
      CRM_Utils_Hook::pre('delete', 'Workflow', $workflow->id, $workflow);

      $workflow->delete();
      $detailTable = CRM_Workflow_DAO_WorkflowDetail::$_tableName;
      $dsql = "DELETE FROM `".$detailTable."` WHERE workflow_id = {$wid}";
      $dao =& CRM_Core_DAO::executeQuery($dsql);

      CRM_Utils_Hook::post('delete', 'Workflow', $workflow->id, $workflow);

      return TRUE;
    }

    return FALSE;
  }

  static function copy($wid) {
    $workflowTable = CRM_Workflow_DAO_Workflow::$_tableName;
    $sql = "SELECT * FROM `".$workflowTable."` WHERE id = {$wid}";
    $dao =& CRM_Core_DAO::executeQuery($sql);
    if ($dao->fetch()) {
      $workflow = $dao->toArray();
      $workflow['id'] = null;
      $new_workflow = CRM_Workflow_BAO_Workflow::add($workflow);

      $detailTable = CRM_Workflow_DAO_WorkflowDetail::$_tableName;
      $sql2 = "SELECT * FROM `".$detailTable."` WHERE workflow_id = {$wid}";
      $dao2 =& CRM_Core_DAO::executeQuery($sql2);

      $nsql = "INSERT INTO `".$detailTable."` (id, workflow_id, entity_table, entity_id, `order`, breadcrumb) VALUES ";
      $nid = $new_workflow->id;
      while ($dao2->fetch()) {
        $did = 0;
        $eid = $dao2->entity_id;
        $e_type = $dao2->entity_table;
        $order = $dao2->order;
        $breadcrumb = $dao2->breadcrumb;
        $nsql .= "($did, $nid, '$e_type', $eid, $order, '$breadcrumb'),";
      }
      $nsql = substr($nsql, 0, -1);
      $dao =& CRM_Core_DAO::executeQuery($nsql);
    }
  }

  static function exportJSON($wid) {
    $workflow = self::getWorkflow($wid);
    foreach($workflow as $key => $fieldData) {
      if (substr($key, 0, 1) == "_") {
        unset($workflow[$key]);
      }
    }
    unset($workflow['N']);

    $steps = self::getWorkflowDetails($wid);

    foreach($steps as &$step) {
      foreach($step as $key => $fieldData) {
        if (substr($key, 0, 1) == "_") {
          unset($step[$key]);
        }
      }
      unset($step['N']);
    }

    $steps = self::processForExport($steps);

    $buffer = json_encode(array("workflow" => $workflow, "steps" => $steps));
    $filename = str_replace(" ", "_", $workflow['name']);
    CRM_Utils_System::download($filename, "application/json", $buffer, "json");
  }



  static function processForExport($steps) {

    foreach($steps as &$step) {

      switch($step['entity_table']) {
        case "Profile":
          $entityName = self::_wf_lookupName("UFGroup", $step['entity_id']);
          $step['entity_id'] = $step['entity_id']. ":" . $entityName;

          //Todo: Handle Existing Group

          break;
        case "Case":

          $entityName =  self::_wf_lookupName("CaseType", $step['entity_id']);
          $step['entity_id'] = $step['entity_id']. ":" . $entityName;

          if(array_key_exists("include_profile", $step['options']) && !empty($step['options']['include_profile'])) {
            $profileName = self::_wf_lookupName("UFGroup", $step['options']['include_profile']);
            $step['options']['include_profile'] = $step['options']['include_profile'].":".$profileName;
          }
          break;
        case "CaseActivity":

          $entityName = $result = civicrm_api3('OptionValue', 'getvalue', array(
            'return' => "name",
            'value' => $step['entity_id'],
            'option_group_id' => "activity_type",
          ));

          $step['entity_id'] = $step['entity_id']. ":" . $entityName;
          
          if(array_key_exists("include_profile", $step['options']) && !empty($step['options']['include_profile'])) {
            $profileName = self::_wf_lookupName("UFGroup", $step['options']['include_profile']);
            $step['options']['include_profile'] = $step['options']['include_profile'].":".$profileName;
          }
          break;
        default:
      }


      //Todo: Handle RelationshipTypes
      /*
      //Handle Relationships
      if (array_key_exists("options", $step) && array_key_exists("relationships", $step['options'])) {
        foreach($step['options']['relationships'] as &$relationship) {
          $relType = explode("_", $step['relType']);

          $relName = civicrm_api3('UFGroup', 'getvalue', array('return' => array("name"), 'id' => $step['entity_id']));

          $step['relType'] = implode("_", $relType);
        }

      }
      */

    }

    return $steps;
  }

  static function processForImport($steps) {

    foreach($steps as &$step) {

      switch($step['entity_table']) {
        case "Profile":
          //Handle the Profile
          list($id, $name) = explode(":",  $step['entity_id'], 2);
          $step['entity_id'] = self::_wf_lookupId("UFGroup", $name, $id);

          //Todo: Handle Existing Group

          break;
        case "Case":
          //Handle the Case Itself
          list($id, $name) = explode(":",  $step['entity_id'], 2);
          $step['entity_id'] = self::_wf_lookupId("CaseType", $name, $id);

          //Handle Included Profile
          if(array_key_exists("include_profile", $step['options']) && !empty($step['options']['include_profile'])) {
            list($id, $name) = explode(":",  $step['options']['include_profile'], 2);
            $step['options']['include_profile'] = self::_wf_lookupId("UFGroup", $name, $id);
          }
          break;
        case "CaseActivity":

          list($id, $name) = explode(":",  $step['entity_id'], 2);

          try {
            $entityId = $result = civicrm_api3('OptionValue', 'getvalue', array(
              'return' => "value",
              'name' => $name,
              'option_group_id' => "activity_type",
            ));
            $step['entity_id'] = $entityId;
          } catch (Exception $e) {
            $step['entity_id'] = $id;
          }

          //Handle Included Profile
          if(array_key_exists("include_profile", $step['options']) && !empty($step['options']['include_profile'])) {
            list($id, $name) = explode(":",  $step['options']['include_profile'], 2);
            $step['options']['include_profile'] = self::_wf_lookupId("UFGroup", $name, $id);
          }
          break;
        default:
      }
    }

    return $steps;
  }

  function _wf_lookupId($entity, $name, $id) {
    return self::_wf_lookup($entity, $name, "name", "id", $id);
  }

  function _wf_lookupName($entity, $id) {
    return self::_wf_lookup($entity, $id, "id", "name");
  }

  function _wf_lookup($entity, $value, $from = "id", $to = "name", $default = null) {
    try {
      return civicrm_api3($entity, 'getvalue', array('return' => $to, "{$from}" => $value));
    } catch (Exception $e) {
      if ($default) {
        return $default;
      }
      return $value;
    }
  }

}
