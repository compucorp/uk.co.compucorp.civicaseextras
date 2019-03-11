<?php

return [
  'civicaseOverdueNotificationLimit' => [
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'civicaseOverdueNotificationLimit',
    'quick_form_type' => 'Element',
    'type' => 'Integer',
    'default' => 90,
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => 2,
      'maxlength' => 4,
    ),
    'add' => '4.7',
    'title' => 'Display Overdue notification after days',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'This adds a visual indicator for cases where there has not been any activity for the selected number of days. This will be visible on the Manage Cases screen on the last updated date.',
    'help_text' => '',
  ],
];
