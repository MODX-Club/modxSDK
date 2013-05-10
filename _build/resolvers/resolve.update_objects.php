<?php 
/*
 * @package modxRepository
 * @subpackage build
 * @author Fi1osof
 * http://community.modx-cms.ru/profile/Fi1osof/
 * http://modxstore.ru
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
  
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            
            if ($modx instanceof modX) {
                
                if($media = $modx->getObject('sources.modMediaSource', array(
                    'name' => 'MODX Model',
                ))
                    AND $SystemSetting = $modx->getObject('modSystemSetting', array(
                        'key'   => 'modxsdk.default_source',
                        'value' =>  '',
                    ))
                ){
                    $SystemSetting->set('value', $media->get('id'));
                    $SystemSetting->save();
                }
            }
            
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            if ($modx instanceof modX) {}
            break;
    }
}
return true;