<?php

class CRM_Civicaseextras_Services_CaseStatusService {
  /**
   * @var array
   *  List of statuses indexed by their value.
   */
  protected $statuses = array();

  /**
   * @var array
   *  List of statuses indexed by their label.
   */
  protected $statusIndexedByLabel = array();


  /**
   * Confirms that the given case has the expected status.
   *
   * @param array $case
   * @param string $expectedStatus
   * @return bool
   */
  public function confirmCaseStatus ($case, $expectedStatus) {
    $this->populateStatuses();

    $status = $this->statuses[$case['case_status_id']];

    return $status['grouping'] === $expectedStatus;
  }

  /**
   * Gets the case status object by its label property. If the status does not exist
   * it returns NULL.
   *
   * @param string $statusLabel
   * @return array
   */
  public function getStatusByLabel ($statusLabel) {
    $this->populateStatuses();

    if (isset($this->statusIndexedByLabel[$statusLabel])) {
      return $this->statusIndexedByLabel[$statusLabel];
    } else {
      return NULL;
    }
  }

  /**
   * Populates the statuses and status indexed by label properties.
   */
  protected function populateStatuses() {
    if ($this->statuses && $this->statusIndexedByLabel) {
      return;
    }

    $statuses = civicrm_api3('OptionValue', 'get', array(
      'sequential' => 1,
      'option_group_id' => 'case_status',
    ));

    foreach ($statuses['values'] as $status) {
      $this->statuses[$status['value']] = $status;
      $this->statusIndexedByLabel[$status['label']] = $status['grouping'];
    }
  }
}
