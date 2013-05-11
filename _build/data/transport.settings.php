<?php

/*
 * @package modxSDK
 * @subpackage build
 * @author Fi1osof
 * http://community.modx-cms.ru/profile/Fi1osof/
 * http://modxstore.ru
 */

$settings = array();


$setting = $modx->newObject('modSystemSetting');
$setting->fromArray(array(
    'key' => 'modxsdk.default_source',
    'value' => '',
    'xtype' => 'numberfiel',
    'namespace' => NAMESPACE_NAME,
    'area' => 'file',
),'',true,true);
$settings[] = $setting;

$setting = $modx->newObject('modSystemSetting');
$setting->fromArray(array(
    'key' => 'modxsdk.ace_theme',
    'value' => 'ace/theme/monokai',
    'xtype' => 'textfield',
    'namespace' => NAMESPACE_NAME,
    'area' => 'file',
),'',true,true);
$settings[] = $setting;


 
return $settings;