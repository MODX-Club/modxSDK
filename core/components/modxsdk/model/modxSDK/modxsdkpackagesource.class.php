<?php
class ModxsdkPackagesource extends xPDOObject {
    
    
    public function prepareSourceNode(){
        
        if(!$source = $this->getSource()){
            return false;
        }
        
        $packageid = $this->get('packageid');
        $sourceid = $this->get('sourceid'); 
        
        $node = array(
            'id'    => "n_packagesource_{$packageid}_{$sourceid}/",
            'text'  => $source->get('name'),
            'qtip'  => $source->get('description'),
            'leaf'  => false,
            # 'cls'   => 'modxsdk-packagesource-icon',
            'type'  => 'packagesource',
            'allowed_types'  => array(
                'dir',
                'file',
            ),
        );
        
        $VersionData = $this->xpdo->getVersionData();
        if(version_compare($VersionData['full_version'], "2.3")){
            $node['iconCls'] = 'icon-folder';
        }
        else{
            $node['cls'] = 'modxsdk-packagesource-icon';
        }
        
        return $node;
    }
    
    public function getSource(){
        return $this->getOne('Source');
    }
}