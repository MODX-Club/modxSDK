<?php


class modxSDKModelObjectsGetListProcessor extends modProcessor{

    private $node_id;


    public function initialize() {
        $this->setDefaultProperties(array(
            'id' => '',
        ));
        $this->node_id = $this->getProperty('id');
        if (empty($this->node_id) || $this->node_id == 'root') {
            $this->setProperty('id','');
        } else if (strpos($this->node_id, 'n_') === 0) {
            $this->node_id = substr($this->node_id, 2);
        }
         
        return true;
    }

    public function process() {
        
        if ($this->getSource() !== true) {
            $error = 'Could not load class ModxsdkFileMediaSource';
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, $error);
            return $this->failure($error);
        }
         
        switch($this->getProperty('type')){
            case 'file':
                return $this->getFileInfo();
                break;
            case 'class':
                return $this->getClassInfo();
                break;
            default:
                return $this->listDir();
        }
    }

    public function listDir(){
        $list = $this->source->getObjectsContainerList($this->node_id);
        return $this->modx->toJSON($list);
    }
    
    
    public function getFileInfo(){
        $list = $this->source->getFileInfo($this->node_id);
        print $this->modx->toJSON($list);;
    }
    
    
    public function getClassInfo(){
        $id = explode('_', $this->node_id);
        $list = $this->source->getClassInfo($id[1], $this->getProperty('name'));
        print $this->modx->toJSON($list);;
    }
    
    
    /**
     * Get the active Source
     * @return modMediaSource|boolean
     */
    public function getSource() {
        
        $path = $this->modx->getOption('modxsdk.core_path', null);
        if(!$path){
            $path = MODX_CORE_PATH .'components/modxsdk/';
        }
        $path .= 'model/modxSDK/'; 
        
        if(!$this->modx->loadClass('ModxsdkFileMediaSource', $path)){
            return false;
        }
        
        $source = modMediaSource::getDefaultSource($this->modx,$this->getProperty('source'));
        if (empty($source) || !$source->getWorkingContext()) {
            return false;
        }
        
        $this->source = $this->modx->newObject('ModxsdkFileMediaSource');
        
        if (!$this->source->checkPolicy('list')) {
            return 'Source access denied';
        }
        
        $this->source->fromArray($source->toArray());
        $this->source->setRequestProperties($this->getProperties());
        $this->source->initialize();
        return true;
    }
}

return 'modxSDKModelObjectsGetListProcessor';
?>
