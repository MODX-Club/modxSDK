<?php
require_once dirname(dirname(__FILE__)).'/snapshot/create.class.php';

class modxSDKMgrVaporPackageCreatepackageProcessor extends modxSDKMgrVaporSnapshotCreateProcessor{
    
    public $permission = 'modxsdk_make_package';
    
    public function initialize(){
        if(!$id = (int)$this->getProperty('package_id')){
            return "Please, type package_id";
        }
        
        if(!$package = $this->modx->getObject('ModxsdkPackage', $id)){
            return 'Could not get package';
        }
        
        $version_array = array();
        $version_array[] = $package->get('version_major');
        $version_array[] = $package->get('version_minor');
        $version_array[] = $package->get('version_patch');
        
        $sources = array();
        if($ModxsdkPackagesources = $this->modx->getCollection('ModxsdkPackagesource', array(
            'packageid' => $id,    
        ))){
            foreach($ModxsdkPackagesources as $ModxsdkPackagesource){
                $sources[] = $ModxsdkPackagesource->get('sourceid');
            }
        }
        
        
        $this->setProperties(array(
            'pkg_name'      => $package->get('name'),   
            'pkg_version'   => implode('.', $version_array),   
            'pkg_release'   => $package->get('version_type'),
            'sources'       => $sources
        ));
        
        return parent::initialize();
    }
}

return 'modxSDKMgrVaporPackageCreatepackageProcessor';