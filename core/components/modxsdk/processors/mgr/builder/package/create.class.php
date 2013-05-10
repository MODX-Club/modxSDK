<?php

class ModxsdkPackageCreateProcessor extends modObjectCreateProcessor{
    
    public $classKey = 'ModxsdkPackage';
    public $objectType = 'modxsdkpackage';
    public $permission = 'modxsdk_new_package';
    
    
    public function beforeSave() {
        if(!$this->object->get('name')){
            return 'Please, type package name';
        }
        return parent::beforeSave();
    }
}

return 'ModxsdkPackageCreateProcessor';
?>
