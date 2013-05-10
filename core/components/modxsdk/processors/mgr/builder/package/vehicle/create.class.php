<?php

require_once dirname(dirname(dirname(__FILE__))).'/vehicle/create.class.php';

class ModxsdkPackageVehicleCreateProcessor extends ModxsdkVehicleCreateProcessor{
    protected $vehiclePackage;
    
    public function beforeSave(){
        $canSave = parent::beforeSave();
        if($canSave !== true){
            return $canSave;
        }
        
        $addVehiclePackage = $this->addVehiclePackage();
        if($addVehiclePackage !== true){
            return $addVehiclePackage;
        }
        
        return true;
    }
    
    
    public function addVehiclePackage(){
        if(!$packageid = $this->getProperty('packageid')){
            return 'Could not get package ID';
        }
        
        if(!$this->vehiclePackage = $this->modx->newObject('ModxsdkPackageVehicle')){
             return 'Could not get ModxsdkPackageVehicle object';
        }
        $this->vehiclePackage->set('packageid', $packageid);
        
        $this->object->addMany($this->vehiclePackage);
        
        return true;
    }
}
return 'ModxsdkPackageVehicleCreateProcessor';
?>
