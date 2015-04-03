<?php
$settings = array();
/*
 * Default source
*/
$setting = $modx->newObject('modSystemSetting');
$setting->fromArray(array(
    'key' => 'modxsdk.default_source',
    'value' => '',
    'xtype' => 'numberfield',
    'namespace' => NAMESPACE_NAME,
    'area' => 'file',
) , '', true, true);
$settings[] = $setting;
/*
 *  Theme
*/
$setting = $modx->newObject('modSystemSetting');
$setting->fromArray(array(
    'key' => 'modxsdk.ace_theme',
    'value' => 'ace/theme/monokai',
    'xtype' => 'textfield',
    'namespace' => NAMESPACE_NAME,
    'area' => 'file',
) , '', true, true);
$settings[] = $setting;
/*
 *  Turn on PHP Beautifier
*/
$setting = $modx->newObject('modSystemSetting');
$setting->fromArray(array(
    'key' => 'modxsdk.php_beautifier',
    'value' => 0,
    'xtype' => 'combo-boolean',
    'namespace' => NAMESPACE_NAME,
    'area' => 'file',
) , '', true, true);
$settings[] = $setting;
# Flush
unset($setting, $setting_name);
return $settings;
