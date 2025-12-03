<?php

/**
 * Get a list of settings for angular pages.
 */
class CRM_Civicaseextras_Settings {

  /**
   * Get a list of settings for angular pages.
   */
  public static function getAll(): array {
    return [
      'overdueNotificationLimit' => (int) Civi::settings()->get('civicaseCaseLastUpdatedNotificationLimit'),
    ];
  }

}
