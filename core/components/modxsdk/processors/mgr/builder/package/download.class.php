<?php

require_once MODX_PROCESSORS_PATH. 'browser/file/download.class.php';

class modxSDKBuilderPackageDownloadProcessor extends modBrowserFileDownloadProcessor{
    
    public function checkPermissions() {
        return $this->modx->hasPermission('modxsdk_download_packages');
    }    
    
    public function initialize(){
        if(!$signature = $this->getProperty('signature')){
            return 'Package signature was not recived';
        }
        
        $this->setDefaultProperties(array(
            'download'  => true,
        ));
        
        $this->setProperties(array(
            'file' => "core/packages/{$signature}.transport.zip",
        ));
        
        $source = $this->getSource();
        if ($source !== true) {
            return $source;
        }  
        
        return parent::initialize();
    }
    
    public function process() {

        if (!$this->source->checkPolicy('view')) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }
        
        if ($this->getProperty('download',false)) {
            return $this->download();
        } else {
            return $this->getObjectUrl();
        }
    }
}

return 'modxSDKBuilderPackageDownloadProcessor';