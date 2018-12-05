<?php

class CRM_Civicaseextras_Services_ActivityTypesService {

  /**
   * @var array
   *  list of activity type names indexed by their value.
   */
  protected $activityTypes;

  /**
   * Determines if the activity is of the expected type.
   *
   * @param array $activity
   * @param string $expectedActivityType
   */
  public function isActivityOfGivenType($activity, $expectedActivityType) {
    $this->populateActivityTypes();

    $activityType = CRM_Utils_Array::value($activity['activity_type_id'], $this->activityTypes);

    return $activityType === $expectedActivityType;
  }

  /**
   * Populates the activities types property. The activity types will only be populated once.
   */
  protected function populateActivityTypes() {
    if ($this->activityTypes) {
      return;
    }

    $activityTypes = civicrm_api3('OptionValue', 'get', array(
      'sequential' => 1,
      'option_group_id' => 'activity_type',
    ));

    foreach ($activityTypes['values'] as $type) {
      $this->activityTypes[$type['value']] = $type['name'];
    }
  }

}
