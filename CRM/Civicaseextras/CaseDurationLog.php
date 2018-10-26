<?php

use CRM_Dictionaries_ActivityStatus as ActivityStatus;
use CRM_Dictionaries_ActivityTypes as ActivityTypes;
use CRM_CiviCaseextras_Services_ActivityTypesService as ActivityTypesService;

/**
 * Handles the case duration log used to calculate total case durations.
 */
class CRM_CiviCaseextras_CaseDurationLog {

  /**
   * @var array
   */
  private $statuses = array();

  /**
   * Array mapping available case statuses to either Opened or Closed groupings.
   *
   * @var array
   */
  private $statusGroupingsPerLabel = array();

  /**
   * Array of cases that have been preprocessed, to be resolved once activity is
   * saved.
   *
   * @var array
   */
  private $pendingCases = array();

  /**
   * @var ActivityTypesService
   *   A reference to the Activity Types Service
   */
  protected $activityTypesService;

  /**
   * @param $activityTypesService
   */
  public function __construct(ActivityTypesService $activityTypesService) {
    $statuses = civicrm_api3('OptionValue', 'get', array(
      'sequential' => 1,
      'option_group_id' => 'case_status',
    ));

    foreach ($statuses['values'] as $status) {
      $this->statuses[$status['value']] = $status;
      $this->statusGroupingsPerLabel[$status['label']] = $status['grouping'];
    }

    $this->activityTypesService = $activityTypesService;
  }

  /**
   * Preprocesses a case activity creation event and stores its data to be
   * be handled once the activity is actually sored in the database and we have
   * an ID for it.
   *
   * @param array $activity
   *   List of parameters being used to store the new activity
   */
  public function preProcessCaseActivity(&$activity) {
    $isCaseChangingStatus = $this->activityTypesService
      ->isActivityOfGivenType($activity, ActivityTypes::CHANGE_CASE_STATUS);
    $hasParamsDefined = !empty($activity['activity_date_time']) &&
      !empty($activity['case_status_id']) &&
      !empty($activity['case_id']);

    if ($isCaseChangingStatus && $hasParamsDefined) {
      $this->pendingCases[$activity['case_id']] = $activity;
    }
  }

  /**
   * Processes a case's activity after it hs been stored in DB, to check if it
   * was used to wither open or close a case and affect the case's duration log
   * accordingly.
   *
   * @param CRM_Core_DAO $activityDAO
   */
  public function postProcessCaseActivity($activityDAO) {
    if ($this->isCaseOpening($activityDAO)) {
      $this->startLog($activityDAO->id, $activityDAO->activity_date_time, $activityDAO->case_id);
    }
    elseif ($this->isCaseClosing($activityDAO)) {
      $this->endLog($activityDAO->id, $activityDAO->activity_date_time, $activityDAO->case_id);
    }

    $this->calculateCaseDuration($activityDAO->case_id);
  }

  /**
   * Checks if the case associated to the activity is being opened.
   *
   * @param CRM_Core_DAO $activityDAO
   *
   * @return bool
   */
  private function isCaseOpening($activity) {
    $isCaseChangingStatus = $this->activityTypesService
      ->isActivityOfGivenType($activity, ActivityTypes::CHANGE_CASE_STATUS);
    $isCaseOpening = $this->activityTypesService
      ->isActivityOfGivenType($activity, ActivityTypes::OPEN_CASE);
    $isCasePending = isset($this->pendingCases[$activityDAO->case_id]);

    if (!$isCaseOpening && $isCaseChangingStatus && $isCasePending) {
      $caseStatusID = $this->pendingCases[$activityDAO->case_id]['case_status_id'];
      $isCaseOpening = $this->statuses[$caseStatusID]['grouping'] === ActivityStatus::OPENED;
    }

    if ($isCaseOpening) {
      $query = "
        SELECT *
        FROM civicrm_case_duration_log
        WHERE case_id = %1
        AND end_task IS NULL
      ";
      $dbResult = CRM_Core_DAO::executeQuery($query, [
        1 => [$activityDAO->case_id, 'Integer']
      ]);

      if ($dbResult->N === 0) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Determines if the case for the activity is closing.
   *
   * @param CRM_Core_DAO $activityDAO
   *
   * @return bool
   */
  private function isCaseClosing($activity) {
    $isCaseChangingStatus = $this->activityTypesService
      ->isActivityOfGivenType($activity, ActivityTypes::CHANGE_CASE_STATUS);
    $isCaseClosing = FALSE;

    if ($isCaseChangingStatus) {
      $caseStatusID = $this->pendingCases[$activityDAO->case_id]['case_status_id'];
      $isCaseClosing = $this->statuses[$caseStatusID]['grouping'] === ActivityStatus::CLOSED;
    }

    return $isCaseClosing;
  }

  /**
   * Processes the provided activity, checking if it corresponds to any change
   * in case status, to create the case duration log.
   *
   * @param CRM_Core_DAO $activityDAO
   */
  public function processOldCaseActivity($activityDAO) {
    if ($this->wasCaseOpening($activityDAO)) {
      $this->startLog($activityDAO->id, $activityDAO->activity_date_time, $activityDAO->case_id);
    }
    elseif ($this->wasCaseClosing($activityDAO)) {
      $this->endLog($activityDAO->id, $activityDAO->activity_date_time, $activityDAO->case_id);
    }
  }

  /**
   * Creates a new record to store the cases duration.
   *
   * @param $activityID
   * @param $startDate
   * @param $caseID
   */
  private function startLog($activityID, $startDate, $caseID) {
    $query = "
      INSERT INTO civicrm_case_duration_log(case_id, start_task, start_date)
      VALUES (%1, %2, %3)
    ";
    $params = array(
      1 => array($caseID, 'Integer'),
      2 => array($activityID, 'Integer'),
      3 => array($startDate, 'String'),
    );
    CRM_Core_DAO::executeQuery($query, $params);
  }

  /**
   * Ends an open case log by seting end task, end date and calculating
   * duration.
   *
   * @param $activityID
   * @param $endDate
   * @param $caseID
   */
  private function endLog($activityID, $endDate, $caseID) {
    $query = "
      UPDATE civicrm_case_duration_log
      SET end_task = %1, end_date = %2, duration = datediff(end_date, start_date)
      WHERE case_id = %3
      AND end_task IS NULL
    ";
    $params = array(
      1 => array($activityID, 'Integer'),
      2 => array($endDate, 'String'),
      3 => array($caseID, 'Integer'),
    );
    CRM_Core_DAO::executeQuery($query, $params);
  }

  /**
   * Uses data of the provided case activity object to check if the case is
   * being opened.
   *
   * @param CRM_Core_DAO $caseActivity
   *   Object with the data for the case and activity.
   *
   * @return bool
   *   True if the current activity is opening the case, false otherwise
   */
  private function wasCaseOpening($activity) {
    $isCaseChangingStatus = $this->activityTypesService
      ->isActivityOfGivenType($activity, ActivityTypes::CHANGE_CASE_STATUS);
    $isActivityOpeningTheCase  = $this->activityTypesService
      ->isActivityOfGivenType($activity, ActivityTypes::OPEN_CASE);
    $isCaseStatusOpened = $this->extractStatusFromSubject($caseActivity->subject) === ActivityStatus::OPENED;

    $changeStatusToOpen = $isCaseChangingStatus && $isCaseStatusOpened;

    if ($isActivityOpeningTheCase || $changeStatusToOpen) {
      $query = "
        SELECT *
        FROM civicrm_case_duration_log
        WHERE case_id = %1
        AND end_task IS NULL
      ";
      $dbResult = CRM_Core_DAO::executeQuery($query, [
        1 => [$caseActivity->case_id, 'Integer'],
      ]);

      if ($dbResult->N === 0) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Checks if the given case activity is closing the case.
   *
   * @param CRM_Core_DAO $caseActivity
   *   Object with the data for the case and activity.
   *
   * @return bool
   *   True if the current activity is closing the case, false otherwise
   */
  private function wasCaseClosing($activity) {
    $isCaseChangingStatus = $this->activityTypesService
      ->isActivityOfGivenType($activity, ActivityTypes::CHANGE_CASE_STATUS);
    $isActivityClosingTheCase = $this->extractStatusFromSubject($caseActivity->subject) === ActivityStatus::CLOSED;

    return $isCaseChangingStatus && $isActivityClosingTheCase;
  }

  /**
   * Given an activity's subject, it tries to extract from the new case status,
   * if it was opened or closed.
   *
   * @param string $activitySubject
   *
   * @return string|null
   *   Returns 'Opened' or 'Closed' if it is able to extract the case status from
   *   the subject. Otherwise it returns null.
   */
  private function extractStatusFromSubject($activitySubject) {
    if (stripos($activitySubject, 'Case status changed from') !== FALSE) {
      $subjectData = explode(' ', $activitySubject);
      $newCaseStatus = $subjectData[count($subjectData) - 1];

      if (isset($this->statusGroupingsPerLabel[$newCaseStatus])) {
        return $this->statusGroupingsPerLabel[$newCaseStatus];
      }
    }

    return NULL;
  }

  /**
   * Calculates the case's duration and stores it in the duration custom field.
   *
   * @param $caseID
   */
  public function calculateCaseDuration($caseID) {
    $statsGroup = $this->getStatsGroup();
    $durationField = $this->getDurationField($statsGroup);

    $query = "
      SELECT case_id, SUM(CASE WHEN duration IS NULL THEN datediff(CURDATE(), start_date) ELSE duration END) AS duration
      FROM civicrm_case_duration_log
      WHERE case_id = %1
      GROUP BY case_id
    ";
    $params = array(1 => array($caseID, 'Integer'));
    $durationResult = CRM_Core_DAO::executeQuery($query, $params);
    $durationResult->fetch();

    civicrm_api3('Case', 'create', array(
      'custom_' . $durationField['id'] => $durationResult->duration,
      'id' => 111,
    ));
  }

  /**
   * Calculates duration for all cases and stores it in the corresponding field.
   */
  public function calculateAllCasesDuration() {
    $statsGroup = $this->getStatsGroup();
    $durationField = $this->getDurationField($statsGroup);

    CRM_Core_DAO::executeQuery('TRUNCATE TABLE ' . $statsGroup['table_name']);
    CRM_Core_DAO::executeQuery("
      INSERT INTO {$statsGroup['table_name']} (entity_id, {$durationField['column_name']})
        SELECT case_id, SUM(CASE WHEN duration IS NULL THEN datediff(CURDATE(), start_date) ELSE duration END) AS duration
        FROM civicrm_case_duration_log
        GROUP BY case_id
    ");
  }

  /**
   * Obtains data for the custom group where case stats are stored.
   *
   * @return array
   */
  private function getStatsGroup() {
    $customGroupResult = civicrm_api3('CustomGroup', 'get', array(
      'sequential' => 1,
      'name' => 'Case_Stats',
    ));

    return array_shift($customGroupResult['values']);
  }

  /**
   * Obtains data for the custom field where duration is stored.
   *
   * @param array $customGroup
   *   Array with the data for the custom group to which duration belongs.
   *
   * @return array
   */
  private function getDurationField($customGroup) {
    $fieldResult = civicrm_api3('CustomField', 'get', array(
      'sequential' => 1,
      'custom_group_id' => $customGroup['id'],
      'name' => 'duration',
    ));

    return array_shift($fieldResult['values']);
  }

}
