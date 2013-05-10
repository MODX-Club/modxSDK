<?php
$pkgName = 'modxSDK';

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('modxsdk.core_path',null,$modx->getOption('core_path').'components/modxsdk/').'model/';
            $modx->addPackage($pkgName, $modelPath);

            $manager = $modx->getManager();
            $modx->setLogLevel(modX::LOG_LEVEL_ERROR);
            
            $objects  = array(
                'ModxsdkPackage',
                'ModxsdkPackagesource',
                'ModxsdkPackageVehicle',
                'ModxsdkProject',
                'ModxsdkProjectPackage',
                'ModxsdkVehicle',
            );
            
            foreach($objects as $o){
                $manager->createObjectContainer($o);
            }
            $modx->setLogLevel(modX::LOG_LEVEL_INFO);
            break;
    }
}
return true;