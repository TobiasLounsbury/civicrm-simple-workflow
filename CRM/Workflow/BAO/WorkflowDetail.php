<?php


class CRM_Workflow_BAO_WorkflowDetail extends CRM_Workflow_DAO_WorkflowDetail {

  static function getDetail($wid, $stepName) {

    $sql = "SELECT * FROM `" . CRM_Workflow_DAO_WorkflowDetail::$_tableName . "` WHERE `workflow_id` = %1 AND `name` = %2 LIMIT 1";
    $vars = array(
      1 => array($wid, "Integer"),
      2 => array($stepName, "String")
    );

    $dao =& CRM_Core_DAO::executeQuery($sql, $vars);
    $dao->fetch();
    $data = (array) $dao;

    if($dao->options) {
      $data['options'] = json_decode($dao->options);
    }

    return $data;
  }


}