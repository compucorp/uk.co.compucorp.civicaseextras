<?php

require_once 'civicaseextras.civix.php';
use CRM_Civicaseextras_ExtensionUtil as E;
use \Civi\Angular\ChangeSet as AngularChangeSet;
use \Civi\Angular\Manager as AngularManager;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civicaseextras_civicrm_config(&$config) {
  _civicaseextras_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civicaseextras_civicrm_xmlMenu(&$files) {
  _civicaseextras_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civicaseextras_civicrm_install() {
  _civicaseextras_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function civicaseextras_civicrm_postInstall() {
  _civicaseextras_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civicaseextras_civicrm_uninstall() {
  _civicaseextras_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civicaseextras_civicrm_enable() {
  _civicaseextras_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civicaseextras_civicrm_disable() {
  _civicaseextras_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civicaseextras_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civicaseextras_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civicaseextras_civicrm_managed(&$entities) {
  _civicaseextras_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civicaseextras_civicrm_caseTypes(&$caseTypes) {
  _civicaseextras_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function civicaseextras_civicrm_angularModules(&$angularModules) {
  _civicaseextras_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civicaseextras_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civicaseextras_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function civicaseextras_civicrm_entityTypes(&$entityTypes) {
  _civicaseextras_civix_civicrm_entityTypes($entityTypes);
}

function civicaseextras_civicrm_alterAngular(AngularManager $angular) {
  _civicaseextras_alterAngular_addVisualAlert($angular);
  _civicaseextras_alterAngular_appendOutcomePanel($angular);
}

/**
 * Replaces the date column type on case lists so it displays an alert when the
 * given date is overdue.
 *
 * @param $angular AngularManager
 */
function _civicaseextras_alterAngular_addVisualAlert (AngularManager &$angular) {
  $changeSet = AngularChangeSet::create('display_warning_for_overdue_cases')
    ->alterHtml('~/civicase/case/list/directives/case-list-table.directive.html',
      function (phpQueryObject $doc) {
        $doc->find('[ng-switch-when="date"]')
          ->html('
            <span ng-if="!item.overdueDates[header.name]">
              {{ CRM.utils.formatDate(item[header.name]) }}
            </span>
            <strong ng-if="item.overdueDates[header.name]"
              class="text-danger">
              {{ CRM.utils.formatDate(item[header.name]) }}
              <i class="material-icons civicase__icon">error</i>
            </strong>
          ');
      });
  $angular->add($changeSet);
}

/**
 * Appends the case outcomes to the case details summary.
 *
 * @param $angular AngularManager
 */
function _civicaseextras_alterAngular_appendOutcomePanel (AngularManager &$angular) {
  $changeSet = AngularChangeSet::create('inject_case_outcomes')
    ->alterHtml('~/civicase/case/details/summary-tab/case-summary-custom-data.html',
      function (phpQueryObject $doc) {
        $doc->find('civicase-masonry-grid')
          ->prepend('<civicase-extras-case-outcome case="item"></civicase-extras-case-outcome>');
      });
  $angular->add($changeSet);
}

/**
 * Implements hook_civicrm_alterContent().
 * Adds extra settings fields to the Civicase Admin Settings form.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterContent/
 */
function civicaseextras_civicrm_alterContent (&$content, $context, $templateName, $form) {
  $isViewingTheCaseAdminForm = get_class($form) === CRM_Admin_Form_Setting_Case::class;

  if (!$isViewingTheCaseAdminForm) {
    return;
  }

  $settingsTemplate = &CRM_Core_Smarty::singleton();
  $settingsTemplateHtml = $settingsTemplate->fetchWith('CRM/Civicaseextra/Admin/Form/Settings.tpl', []);

  $doc = phpQuery::newDocumentHTML($content);
  $doc->find('table.form-layout tr:last')->append($settingsTemplateHtml);

  $content = $doc->getDocument();
}

/**
 * Implements hook_civicrm_preProcess().
 */
function civicaseextras_civicrm_preProcess($formName, &$form) {
  if ($formName == 'CRM_Admin_Form_Setting_Case') {
    $settings = $form->getVar('_settings');
    $settings['civicaseOverdueNotificationLimit'] = CRM_Core_BAO_Setting::SYSTEM_PREFERENCES_NAME;

    $form->setVar('_settings', $settings);
  }
}
