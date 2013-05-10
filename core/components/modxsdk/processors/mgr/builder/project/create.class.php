<?php

class ModxsdkProjectCreateProcessor extends modObjectCreateProcessor{
    
    public $classKey = 'ModxsdkProject';
    public $permission = 'modxsdk_new_project';
    
    
    public function beforeSave() {
        if(!$this->object->get('name')){
            return 'Please, type project name';
        }
        return parent::beforeSave();
    }
}

return 'ModxsdkProjectCreateProcessor';
?>
