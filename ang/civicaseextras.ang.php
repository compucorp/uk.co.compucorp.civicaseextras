<?php

use CRM_Civicase_Helper_GlobRecursive as GlobRecursive;

/**
 * Returns a list of Civicaseextras JS files to be bundled.
 *
 * @return array
 */
function getCivicaseExtrasJSFiles () {
  return array_merge([
    Civi::service('asset_builder')->getUrl('visual-bundle.js'),
    'ang/civicaseextras.js'
  ], GlobRecursive::getRelativeToExtension(
    'uk.co.compucorp.civicaseextras',
    'ang/civicaseextras/*.js'
  ));
}

return [
  'js' => getCivicaseExtrasJSFiles(),
  'css' => [
    Civi::service('asset_builder')->getUrl('visual-bundle.css'),
    'css/*.css',
  ],
  'partials' => [
    'ang/civicaseextras',
  ],
  'settingsFactory' => ['CRM_Civicaseextras_Settings', 'getAll'],
  'requires' => [
    'crmUtil'
  ],
  'basePages' => [],
];
