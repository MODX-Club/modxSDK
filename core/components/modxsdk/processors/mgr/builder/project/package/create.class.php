<?php

require_once dirname(dirname(dirname(__FILE__))).'/package/create.class.php';

class ModxsdkProjectPackageCreateProcessor extends ModxsdkPackageCreateProcessor{ 
    
    protected $packageProject;
    
    public function beforeSave(){
        
        $canSave = parent::beforeSave();
        if($canSave !== true){
            return $canSave;
        }
        
        $addPackageProject = $this->addPackageProject();
        if($addPackageProject !== true){
            return $addPackageProject;
        }
        
        return true;
    }
    
    
    public function addPackageProject(){
        if(!$projectid = intval($this->getProperty('projectid'))){
            return 'Could not get project ID';
        }
        
        if(!$this->packageProject = $this->modx->newObject('ModxsdkProjectPackage')){
             return 'Could not get ModxsdkProjectPackage object';
        }
        $this->packageProject->set('projectid', $projectid);
        
        $this->object->addMany($this->packageProject);
        
        return true;
    }
}

return 'ModxsdkProjectPackageCreateProcessor';
?>
