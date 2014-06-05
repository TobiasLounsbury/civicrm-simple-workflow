<?php

//TODO: Change tablename to civicrm_workflow

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_Workflow_DAO_Workflow extends CRM_Core_DAO {
    /**
     * static instance to hold the table name
     *
     * @var string
     * @static
     */
    static $_tableName = 'civicrm_workflow';
    /**
     * static instance to hold the field values
     *
     * @var array
     * @static
     */
    static $_fields = null;
    /**
     * static instance to hold the FK relationships
     *
     * @var string
     * @static
     */
    static $_links = null;
    /**
     * static instance to hold the values that can
     * be imported
     *
     * @var array
     * @static
     */
    static $_import = null;
    /**
     * static instance to hold the values that can
     * be exported
     *
     * @var array
     * @static
     */
    static $_export = null;
    /**
     * static value to see if we should log any modifications to
     * this table in the civicrm_log table
     *
     * @var boolean
     * @static
     */
    static $_log = false;
    /**
     * Workflow ID
     *
     * @var int unsigned
     */
    public $id;
    /**
     * Name of the workflow
     *
     * @var string
     */
    public $name;
    /**
     * Workflow Description.
     *
     * @var string
     */
    public $description;
    /**
     * Profile ID for forcing login.
     *
     * @var string
     */
    public $login_form_id;
    /**
     * Is this property active?
     *
     * @var boolean
     */
    public $require_login;
    /**
     * Is a login required to complete this workflow?
     *
     * @var boolean
     */
    public $is_active;
    /**
     * class constructor
     *
     * @access public
     * @return workflow
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * returns all the column names of this table
     *
     * @access public
     * @return array
     */
    static function &fields() {
        if (!(self::$_fields)) {
            self::$_fields = array(
                'id' => array(
                    'name' => 'id',
                    'type' => CRM_Utils_Type::T_INT,
                    'required' => true,
                ),
                'name' => array(
                    'name' => 'name',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Name'),
                    'required' => true,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ),
                'login_form_id' => array(
                    'name' => 'login_form_id',
                    'type' => CRM_Utils_Type::T_INT,
                    'title' => ts('Login Profile ID'),
                    'required' => false,
                ),
                'description' => array(
                    'name' => 'description',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Description'),
                    'required' => false,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ),
                'require_login' => array(
                    'name' => 'require_login',
                    'type' => CRM_Utils_Type::T_BOOLEAN,
                ),
                'is_active' => array(
                    'name' => 'is_active',
                    'type' => CRM_Utils_Type::T_BOOLEAN,
                ),
            );
        }
        return self::$_fields;
    }
    /**
     * returns the names of this table
     *
     * @access public
     * @return string
     */
    static function getTableName() {
        return CRM_Core_DAO::getLocaleTableName(self::$_tableName);
    }
    /**
     * returns if this table needs to be logged
     *
     * @access public
     * @return boolean
     */
    function getLog() {
        return self::$_log;
    }
    /**
     * returns the list of fields that can be imported
     *
     * @access public
     * return array
     */
    function &import($prefix = false) {
        if (!(self::$_import)) {
            self::$_import = array();
            $fields = self::fields();
            foreach($fields as $name => $field) {
                if (CRM_Utils_Array::value('import', $field)) {
                    if ($prefix) {
                        self::$_import['ount_item'] = & $fields[$name];
                    }
                    else {
                        self::$_import[$name] = & $fields[$name];
                    }
                }
            }
        }
        return self::$_import;
    }
    /**
     * returns the list of fields that can be exported
     *
     * @access public
     * return array
     */
    function &export($prefix = false) {
        if (!(self::$_export)) {
            self::$_export = array();
            $fields = self::fields();
            foreach($fields as $name => $field) {
                if (CRM_Utils_Array::value('export', $field)) {
                    if ($prefix) {
                        self::$_export['ount_item'] = & $fields[$name];
                    }
                    else {
                        self::$_export[$name] = & $fields[$name];
                    }
                }
            }
        }
        return self::$_export;
    }
}
