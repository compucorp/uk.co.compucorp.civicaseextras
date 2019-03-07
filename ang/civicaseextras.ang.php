<?php
/**
 * Get a list of JS files
 */
function getCivicaseExtrasJSFiles () {
  return array_merge(array(
    'assetBuilder://visual-bundle.js',
    'ang/civicaseextras.js'
  ), glob_recursive(dirname(__FILE__) . '/civicaseextras/*.js'));
}

$settings = [
  'overdueNotificationLimit' => (int) Civi::settings()->get('civicaseOverdueNotificationLimit'),
];

return [
  'js' => getCivicaseExtrasJSFiles(),
  'css' => [
    'assetBuilder://visual-bundle.css',
    'css/*.css',
  ],
  'partials' => [
    'ang/civicaseextras',
  ],
  'settings' => $settings,
  'requires' => [
    'crmUtil'
  ],
  'basePages' => [],
];
