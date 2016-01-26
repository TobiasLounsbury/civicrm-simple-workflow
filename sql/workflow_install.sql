DROP TABLE IF EXISTS `civicrm_simple_workflow`;
DROP TABLE IF EXISTS `civicrm_simple_workflow_detail`;

-- /*******************************************************
-- *
-- * workflow
-- *
-- * A workflow entity that is used to store settings and collate profiles
-- *
-- *******************************************************/
CREATE TABLE `civicrm_simple_workflow` (
     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Workflow ID',
     `name` varchar(255) NOT NULL   COMMENT 'Name of the Workflow',
     `description` varchar(255) NOT NULL   COMMENT 'Workflow Description.',
     `require_login` tinyint    COMMENT 'Should this Workflow require a user to login/create an account?',
     `is_active` tinyint    COMMENT 'Is this Workflow active?',
     `login_form_id` int unsigned COMMENT 'Profile ID for forcing login',

    PRIMARY KEY ( `id` )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- /*******************************************************
-- *
-- * workflow_detail
-- *
-- * Which Profiles/Pricesets to use and in what order
-- *
-- *******************************************************/
CREATE TABLE `civicrm_simple_workflow_detail` (
     `breadcrumb` varchar(255) NOT NULL   COMMENT 'Link text to use for the workflow progress bar',
     `name` VARCHAR(255) NOT NULL COMMENT 'The name of the step, used in calling hook and custom JS',
     `workflow_id` int unsigned COMMENT 'FK to Workflow ID of the Workflow Entity',
     `entity_table` varchar(64) NOT NULL   COMMENT 'Name of table where item being referenced is stored',
     `entity_id` text NOT NULL   COMMENT 'Foreign key to the referenced item',
     `next` varchar(255) NOT NULL   COMMENT 'Text for the Next button for this step',
     `title` varchar(255) NULL   COMMENT 'Title for this workflow step',
     `options` text NULL   COMMENT 'Generic field for misc options',
     `order` int unsigned NOT NULL   COMMENT 'Order for profiles/price sets to be displayed in',
     `custom_js` VARCHAR(255) NULL,

     PRIMARY KEY (`workflow_id`, `name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;
