<?php

class CRM_Civicaseextras_Services_CustomValueService {

  /**
   * @var array
   *   In-memory cache of custom fields
   */
  protected static $customFields = [];

  /**
   * Fetches the data for a custom field.
   *
   * @param string $groupName
   * @param string $fieldName
   *
   * @return array
   */
  public static function getCustomField($groupName, $fieldName) {
    if (!isset(self::$customFields[$groupName][$fieldName])) {
      $params = ['name' => $fieldName, 'custom_group_id' => $groupName];
      $field = civicrm_api3('CustomField', 'getsingle', $params);
      self::$customFields[$groupName][$fieldName] = $field;
    }

    return self::$customFields[$groupName][$fieldName];
  }
}
