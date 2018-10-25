<?php

use Civi\Angular\Manager as AngularManager;
use Civi\Angular\ChangeSet as AngularChangeSet;
use CRM_Civicaseextras_Services_CustomValueService as CustomValueService;

class CRM_Civicaseextras_AngularModifiers_CaseDuration {

  /**
   * @var AngularManager
   *   A reference to the Angular Manager object as provided by the alter angular hook.
   */
  protected $angular;

  /**
   * @var CustomValueService
   *   an instance of the custom value service.
   */
  protected $customValueService;

  /**
   * @param AngularManager $angular
   * @param CustomValueService $customValueService
   */
  public function __construct(AngularManager &$angular, CustomValueService $customValueService) {
    $this->angular = $angular;
    $this->customValueService = $customValueService;
  }

  /**
   * Adds the case duration column and value to the case list table template of civicase.
   */
  public function runModifications() {
    $changeSet = AngularChangeSet::create('inject_case_duration')
      ->alterHtml('~/civicase/CaseListTable.html',
        function (phpQueryObject $doc) {
          $caseDuration = $this->customValueService->getCustomField('case_stats', 'duration');

          $this->addCaseDurationTableHeader($doc, $caseDuration);
          $this->addCaseDurationTableCell($doc, $caseDuration);
        });

    $this->angular->add($changeSet);
  }

  /**
   * Adds the case duration table header to the civicase list table.
   *
   * @param phpQueryObject $doc
   * @param array $caseDuration
   */
  protected function addCaseDurationTableHeader(phpQueryObject &$doc, $caseDuration) {
    $doc->find('.civicase__case-list-table thead tr')
      ->append('<th
        ng-show="!viewingCase && headers.length"
        civicase-case-list-sort-header="custom_' . $caseDuration['id'] . '">
        ' . $caseDuration['label'] . '
      </th>');
  }

  /**
   * Adds the case duration table cell to the civicase list table.
   *
   * @param phpQueryObject $doc
   * @param array $caseDuration
   */
  protected function addCaseDurationTableCell(phpQueryObject &$doc, $caseDuration) {
    $doc->find('.civicase__case-list-table tbody tr[ng-repeat="item in cases"]')
      ->append('<td ng-show="!viewingCase && headers.length">
        {{item.custom_' . $caseDuration['id'] . '}}
      </td>');
  }

}
