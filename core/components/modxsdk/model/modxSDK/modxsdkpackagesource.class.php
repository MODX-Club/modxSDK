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
            'cls'   => 'modxsdk-packagesource-icon',
            'type'  => 'packagesource',
            'allowed_types'  => array(
                'dir',
                'file',
            ),
        );
        return $node;
    }
    
    public function getSource(){
        return $this->getOne('Source');
    }
}