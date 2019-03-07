<?php

$settings = [
  'overdueNotificationLimit' => (int) Civi::settings()->get('civicaseOverdueNotificationLimit'),
];

return [
  'js' => [
    'assetBuilder://visual-bundle.js',
    'ang/civicaseextras.js',
    'ang/civicaseextras/*.js',
  ],
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
