<?php


class modxSDKBuilderObjectsGetNodesProcessor extends modProcessor{

    protected $node_id;
    
    protected $type;
    
    protected $packageid;

    public function initialize() {
        
        if(!$this->type = $this->getProperty('type', false)){
            return false;
        }
        
        $this->setDefaultProperties(array(
            'id' => '',
        ));
        
        if(!$this->node_id = $this->getProperty('id')){
            return $this->failure('Could not get node ID');
        }
        
        if (strpos($this->node_id, 'n_') === 0) {
            $this->node_id = substr($this->node_id, 2);
        }
         
        return true;
    }

    public function process() {
        $nodes = array();
        
        switch($this->type){
            case 'project':
                $nodes = $this->getProjectNodes();
                break;
            case 'package':
                $nodes = $this->getPackageNodes();
                break;
            case 'vehicles':
                $nodes = $this->getPackageVehicles();
                break;
            case 'sources':
                $nodes = $this->getPackageSources();
                break;
            case 'packagesource':
            case 'dir':
                $nodes = $this->getDirectoryList();
                break;
            default:;
        }
        
        return $this->toJSON($nodes);
    } 
    
    
    /*
     * getProjectNodes
     */
    
    public function getProjectNodes(){
        $nodes = array();
        
        /*
         * Collect packages
         */
        if($projectid = $this->getProperty('projectid')){
            $q = $this->modx->newQuery('ModxsdkPackage');
            $q->innerJoin('ModxsdkProjectPackage', 'PackageProjects');
            $q->where(array(
                'PackageProjects.projectid' => $projectid
            ));

            if($packages = $this->modx->getCollection('ModxsdkPackage', $q)){
                foreach($packages as $package){
                    if($node = $package->prepareNode()){
                        $nodes[] = $node;
                        # print_r($node);
                    }
                }
            }
        }
        
        return $nodes;
    }
    
    
    /*
     * get package nodes
     */
    public function getPackageNodes(){
        # $Vehicles = $this->getVehicles();
        $Sources = $this->getDirectoryRoot();
        $nodes = array(
            # $Vehicles,
            $Sources,
        );
        
        return $nodes;
    }
    
    public function getVehicles(){
        $cls = array(
            'modxsdk-vehicles-icon icon-folder'
        );
        
        $classes = implode(' ', $cls);
        
        $node = array(
            'id'    => "n_".$this->node_id.'_vehicles',
            'text'  => 'Vehicles',
            'qtip'  => 'Package vehicles',
            'type'  => 'vehicles',
            'leaf'  => false,
            'allowed_types'  => array(
                'chunk',
                'snippet',
                'template',
            ),
        );
        
        $VersionData = $this->modx->getVersionData();
        if(version_compare($VersionData['full_version'], "2.3")){
            $node['iconCls'] = $classes;
        }
        else{
            $node['cls'] = $classes;
        }
        
        return $node;
    }
    
    public function getDirectoryRoot(){
        $cls = array(
            'icon-folder'
        );
        
        $classes = implode(' ', $cls);
        
        $node = array(
            'id'    => "n_".$this->node_id.'_sources',
            'text'  => 'Media Sources',
            'qtip'  => 'Media Sources',
            'type'  => 'sources',
            # 'cls'   => implode(' ', $cls),
            'leaf'  => false,
            'allowed_types'  => array(),
        );
        
        $VersionData = $this->modx->getVersionData();
        if(version_compare($VersionData['full_version'], "2.3")){
            $node['iconCls'] = $classes;
        }
        else{
            $node['cls'] = $classes;
        }
        
        return $node;
    }
    
    
    /*
     * Get Vehicles 
     */
    
    public function getPackageVehicles(){
         $nodes = array();
        
         $id = explode("_", $this->node_id);
          
        /*
         * Collect vehicles
         */
        if($this->packageid = intval($id[1])){
            $q = $this->modx->newQuery('ModxsdkVehicle');
            $q->innerJoin('ModxsdkPackageVehicle', 'VehiclePackage');
            $q->where(array(
                'VehiclePackage.packageid' => $this->packageid
            ));

            if($vehicles = $this->modx->getCollection('ModxsdkVehicle', $q)){
                foreach($vehicles as $vehicle){
                    if($node = $vehicle->prepareNode()){
                        $nodes[] = $node;
                    }
                }
            }
        }
        
        return $nodes;
    }
    
    /*
     * Get Package Sources 
     */
    
    public function getPackageSources(){
        $nodes = array();
        
        $id = explode("_", $this->node_id);
          
        /*
         * Collect sources
         */
        if($this->packageid = intval($id[1])){
            if($packagesource = $this->modx->getCollection('ModxsdkPackagesource', array(
                'packageid' => $this->packageid
            ))){
                foreach($packagesource as $packagesourc){
                    if($node = $packagesourc->prepareSourceNode()){
                        $nodes[] = $node;
                    }
                }
            }
        }
        
        return $nodes;
    }
    
    
    /*
     * List Direcory
     */
    
    public function getDirectoryList(){
        $nodes = array();
        $array = explode('/', $this->node_id, 2);
        $path = "/{$array[1]}";
        
        $id  = explode('_',  $array[0]);
        
        $this->packageid = $id[1];
        $sourceid = $id[2];
        
        $this->setProperty('sourceid', $sourceid);
        
        if($this->getSource() !== true){
            return $nodes;
        } 
        return $this->source->getPackageSourceContainerList($path);
    }
    
    /**
     * Get the active Source
     * @return modMediaSource|boolean
     */
    public function getSource() {
        
        if(!$this->modx->loadClass('ModxsdkFileMediaSource')){
            return false;
        }
        
        $source = ModxsdkFileMediaSource::getDefaultSource($this->modx,$this->getProperty('sourceid'));
        if (empty($source) || !$source->getWorkingContext()) {
            return false;
        }
        
        $this->source = $this->modx->newObject('ModxsdkFileMediaSource');
        if (!$this->source->checkPolicy('list')) {
            return 'Source access denied';
        }
        
        $this->source->fromArray($source->toArray());
        $this->source->fromArray(array(
            'packageid' => $this->packageid,
        ));
        $this->source->set('id', $source->get('id'));
        $this->source->setRequestProperties($this->getProperties());
        $this->source->initialize();
        
        return true;
    }
}

return 'modxSDKBuilderObjectsGetNodesProcessor';
?>
