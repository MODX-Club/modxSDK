<?php
$menus = array();
/*
 * Основной контроллер
*/
$action = $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => NAMESPACE_NAME,
    'parent' => 0,
    'controller' => 'controllers/',
    'haslayout' => 1,
    'lang_topics' => 'modxsdk:default',
    'assets' => '',
) , '', true, true);
/**
 *  load action into menu
 */
$menu = $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'modxsdk',
    'parent' => 'components',
    'description' => 'modxsdk_desc',
    'icon' => 'images/icons/plugin.gif',
    'menuindex' => 0,
    'params' => '',
    'handler' => '',
    'permissions' => 'console',
    'namespace' => NAMESPACE_NAME,
) , '', true, true);
$menu->addOne($action);
unset($action);
$menus[] = $menu;
return $menus;
