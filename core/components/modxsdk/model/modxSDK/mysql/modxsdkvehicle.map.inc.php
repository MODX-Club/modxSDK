<?php
$xpdo_meta_map['ModxsdkVehicle']= array (
  'package' => 'modxSDK',
  'version' => '1.1',
  'table' => 'modxsdk_vehicle',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'composites' => 
  array (
    'VehiclePackage' => 
    array (
      'class' => 'ModxsdkPackageVehicle',
      'local' => 'id',
      'foreign' => 'vehicleid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
