<?php
class ModxsdkPackage extends xPDOSimpleObject {
    
    public function prepareNode(){
        $version = $this->getVersion();
        $node = array(
            'id'    => "n_package_". $this->get('id'),
            'text'  => $this->get('name')."-{$version}",
            'leaf'  => false,
            'cls'   => 'modxsdk-package-icon',
            'type'  => 'package',
            'allowDrop' => false,
            'menu'  => array(
                'items' => array(
                    array(
                        'text' => 'Remove package',
                        'handler' => 'this.removePackage'
                    )
                ),
            ),
        );
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