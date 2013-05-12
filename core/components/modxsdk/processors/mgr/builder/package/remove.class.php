<?php
 

/**
 * Description of remove
 *
 * @author Fi1osof
 */
class ModxsdkPackageRemoveProcessor extends modObjectRemoveProcessor{
    public $classKey = 'ModxsdkPackage';
    public $primaryKeyField = 'id';
    
    public $permission = 'modxsdk_remove_package';
    
    public function beforeRemove() {
        if($this->getProperty($this->primaryKeyField) != $this->object->get($this->primaryKeyField)){
            return 'Primary key not valid';
        }
        return parent::beforeRemove();
    }
}

return 'ModxsdkPackageRemoveProcessor';
?>
