<?php

class ModxsdkPackagesourceCreateProcessor extends modObjectCreateProcessor{
    
    public $classKey = 'ModxsdkPackagesource';
    public $objectType = 'modxsdkpackagesource';
    public $permission = 'modxsdk_new_packagesource';
    
    
    public function beforeSave() {
        if(!$packageid = intval($this->getProperty('packageid')) OR !$sourceid = intval($this->getProperty('sourceid'))){
            return false;
        }
        
        if($this->modx->getObject($this->classKey, array(
            'packageid' =>  $packageid,
            'sourceid'  =>  $sourceid,
        ))){
            return "Media source for this package already exists";
        }
        
        $this->object->set('packageid', $packageid); 
        $this->object->set('sourceid', $sourceid); 
        return parent::beforeSave();
    }
}

return 'ModxsdkPackagesourceCreateProcessor';
?>
