<?php

class CRM_Civicaseextras_Services_CustomValueService {

  /**
   * @var array
   *   In-memory cache of custom fields
   */
  protected $customFields = [];

  /**
   * Fetches the data for a custom field.
   *
   * @param string $groupName
   * @param string $fieldName
   *
   * @return array
   */
  public function getCustomField($groupName, $fieldName) {
    if (!isset($this->customFields[$groupName][$fieldName])) {
      $params = ['name' => $fieldName, 'custom_group_id' => $groupName];
      $field = civicrm_api3('CustomField', 'getsingle', $params);
      $this->customFields[$groupName][$fieldName] = $field;
    }

    return $this->customFields[$groupName][$fieldName];
  }

}
