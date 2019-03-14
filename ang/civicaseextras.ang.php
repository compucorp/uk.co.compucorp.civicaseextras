<?php

/**
 * Returns a list of Civicaseextras JS files to be bundled.
 *
 * @return array
 */
function getCivicaseExtrasJSFiles () {
  return array_merge(array(
    'assetBuilder://visual-bundle.js',
    'ang/civicaseextras.js'
  ), glob_recursive(dirname(__FILE__) . '/civicaseextras/*.js'));
}

/**
 * Returns a list of Civicaseextras settings that will be shared with the front-end.
 *
 * @return array
 */
function getCivicaseExtrasSettings () {
  return [
    'overdueNotificationLimit' => (int) Civi::settings()->get('civicaseCaseLastUpdatedNotificationLimit'),
  ];
}

return [
  'js' => getCivicaseExtrasJSFiles(),
  'css' => [
    'assetBuilder://visual-bundle.css',
    'css/*.css',
  ],
  'partials' => [
    'ang/civicaseextras',
  ],
  'settings' => getCivicaseExtrasSettings(),
  'requires' => [
    'crmUtil'
  ],
  'basePages' => [],
];
