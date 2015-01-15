<?php
class ModxsdkPackage extends xPDOSimpleObject {
    
    public function prepareNode(){
        $version = $this->getVersion();
        
        $menus = array();
        
        if($this->xpdo->hasPermission('modxsdk_make_package')){
            $menus[] = array(
                'text' => 'Make package',
                'handler' => 'this.makePackage',
            );
        }
        
        if($this->xpdo->hasPermission('modxsdk_download_packages')){
            $menus[] = array(
                'text' => 'Make and download package',
                'handler' => 'this.makeAndDownloadPackage',
            );
        }
        
        if($this->xpdo->hasPermission('modxsdk_remove_package')){
            $menus[] = array(
                'text' => 'Remove package',
                'handler' => 'this.removePackage',
            );
        }
        
        $classes = 'modxsdk-package-icon';
        
        $node = array(
            'id'    => "n_package_". $this->get('id'),
            'text'  => $this->get('name')."-{$version}",
            'leaf'  => false,
            'type'  => 'package',
            'allowDrop' => false,
            'menu'  => array(
                'items' => $menus,
            ),
        );
        
        $VersionData = $this->xpdo->getVersionData();
        if(version_compare($VersionData['full_version'], "2.3")){
            $node['iconCls'] = $classes;
        }
        else{
            $node['cls'] = $classes;
        }
        
        return $node;
    }
    
    public function getVersion(){
        $version_major = $this->get('version_major');
        $version_minor = $this->get('version_minor');
        $version_patch = $this->get('version_patch');
        $version_type = $this->get('version_type');
        return "{$version_major}.{$version_minor}.{$version_patch}-{$version_type}";
    }
}