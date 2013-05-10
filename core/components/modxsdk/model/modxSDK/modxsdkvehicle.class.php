<?php
class ModxsdkVehicle extends xPDOSimpleObject {
    
    public function prepareNode(){
        $node = array(
            'id'    => "n_vehicle_". $this->get('id'),
            'text'  => $this->get('name'),
            'qtip'  => 'Transport vehicles with MODX-objects and resolvers',
            'leaf'  => false,
            'cls'   => 'modxsdk-vehicle-icon',
            'type'  => 'vehicle',
        );
        return $node;
    }    
}