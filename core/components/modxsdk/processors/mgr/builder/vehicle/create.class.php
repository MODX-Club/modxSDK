<?php

class ModxsdkVehicleCreateProcessor extends modObjectCreateProcessor{
    
    public $classKey = 'ModxsdkVehicle';
    public $objectType = 'modxsdkvehicle';
    public $permission = 'modxsdk_new_vehicle';
    
    
    public function beforeSave() {
        if(!$this->object->get('name')){
            return 'Please, type vehicle name';
        }
        return parent::beforeSave();
    }
}

return 'ModxsdkVehicleCreateProcessor';
?>
