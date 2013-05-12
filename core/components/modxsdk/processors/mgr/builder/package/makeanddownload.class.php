<?php
/*
    Make and download Package
*/
require_once dirname(dirname(dirname(__FILE__))).'/vapor/package/createpackage.class.php';
require_once dirname(__FILE__).'/download.class.php';

class modxSDKMgrBuilderPackageMakeanddownloadProcessor extends modProcessor{
    
    public function process(){
        // Make package
        $processor = new modxSDKMgrVaporPackageCreatepackageProcessor($this->modx, array(
            'package_id'    => (int)$this->getProperty('package_id'),    
        ));
        if(!$response = $processor->run()){
            return $this->failure('Coluld not make package');
        }
        if($response->isError()){
            return $response->getResponse();
        }
        
        // Download Package
        $processor = new modxSDKBuilderPackageDownloadProcessor($this->modx, $response->getObject());
        return $processor->run();
    }
    
}
return 'modxSDKMgrBuilderPackageMakeanddownloadProcessor';