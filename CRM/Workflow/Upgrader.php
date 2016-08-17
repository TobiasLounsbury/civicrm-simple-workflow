<?php

/**
 * Collection of upgrade steps
 */
class CRM_Workflow_Upgrader extends CRM_Workflow_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed
   */
  public function install() {
    $this->executeSqlFile('sql/workflow_install.sql');
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled
   *
  public function uninstall() {
   $this->executeSqlFile('sql/myuninstall.sql');
  }

  /**
   * Example: Run a simple query when a module is enabled
   *
  public function enable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 1 WHERE bar = "whiz"');
  }

  /**
   * Example: Run a simple query when a module is disabled
   *
  public function disable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
  }

  /**
   * Example: Run a couple simple queries
   *
   * @return TRUE on success
   * @throws Exception
   */

    public function upgrade_101() {
    $this->ctx->log->info('Applying update 1.0.1');
    CRM_Core_DAO::executeQuery('ALTER TABLE `civicrm_workflow_detail` ADD COLUMN `custom_js` VARCHAR(255) NULL  AFTER `order`');
    return TRUE;
  }

  public function upgrade_102() {
    $this->ctx->log->info('Applying Simple Workflow update 1.0.2');
    CRM_Core_DAO::executeQuery('RENAME TABLE `civicrm_workflow` TO `civicrm_simple_workflow`');
    CRM_Core_DAO::executeQuery('RENAME TABLE `civicrm_workflow_detail` TO `civicrm_simple_workflow_detail`');
    CRM_Core_DAO::executeQuery('ALTER TABLE `civicrm_simple_workflow_detail` ADD COLUMN `name` VARCHAR(255) NOT NULL  AFTER `breadcrumb`');
    CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_simple_workflow_detail` ADD COLUMN `pre_message` text NULL COMMENT 'This field is for HTML to be displayed BEFORE the step' AFTER `custom_js`");
    CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_simple_workflow_detail` ADD COLUMN `post_message` text NULL COMMENT 'This field is for HTML to be displayed AFTER the step' AFTER `pre_message`");
    CRM_Core_DAO::executeQuery("UPDATE `civicrm_simple_workflow_detail` SET `name` = REPLACE(`breadcrumb`, ' ', '_')");
    CRM_Core_DAO::executeQuery('ALTER TABLE `civicrm_simple_workflow_detail` ADD PRIMARY KEY (`workflow_id`, `name`)');
    return TRUE;
  }


  public function upgrade_103() {
    $this->ctx->log->info('Applying Simple Workflow update 1.0.3');
    CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_simple_workflow` ADD COLUMN `pre_message` text NULL COMMENT 'This field is for HTML to be displayed ABOVE the form' AFTER `login_form_id`");
    CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_simple_workflow` ADD COLUMN `post_message` text NULL COMMENT 'This field is for HTML to be displayed BELOW the form', AFTER `pre_message`");
    CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_simple_workflow` ADD COLUMN `options` text NULL COMMENT 'JSON Encoded string of data for additional options' AFTER `post_message`");
    return true;
  }

  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4201() {
    $this->ctx->log->info('Applying update 4201');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_4201.sql');
    return TRUE;
  } // */


  /**
   * Example: Run a slow upgrade process by breaking it up into smaller chunk
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4202() {
    $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

    $this->addTask(ts('Process first step'), 'processPart1', $arg1, $arg2);
    $this->addTask(ts('Process second step'), 'processPart2', $arg3, $arg4);
    $this->addTask(ts('Process second step'), 'processPart3', $arg5);
    return TRUE;
  }
  public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
  public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
  public function processPart3($arg5) { sleep(10); return TRUE; }
  // */


  /**
   * Example: Run an upgrade with a query that touches many (potentially
   * millions) of records by breaking it up into smaller chunks.
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4203() {
    $this->ctx->log->info('Planning update 4203'); // PEAR Log interface

    $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
    $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
    for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
      $endId = $startId + self::BATCH_SIZE - 1;
      $title = ts('Upgrade Batch (%1 => %2)', array(
        1 => $startId,
        2 => $endId,
      ));
      $sql = '
        UPDATE civicrm_contribution SET foobar = whiz(wonky()+wanker)
        WHERE id BETWEEN %1 and %2
      ';
      $params = array(
        1 => array($startId, 'Integer'),
        2 => array($endId, 'Integer'),
      );
      $this->addTask($title, 'executeSql', $sql, $params);
    }
    return TRUE;
  } // */

}
