<?php
$xpdo_meta_map['ModxsdkPackageVehicle']= array (
  'package' => 'modxSDK',
  'version' => '1.1',
  'table' => 'modxsdk_package_vehicle',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'packageid' => NULL,
    'vehicleid' => NULL,
  ),
  'fieldMeta' => 
  array (
    'packageid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'vehicleid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'composites' => 
  array (
    'Vehicle' => 
    array (
      'class' => 'ModxsdkVehicle',
      'key' => 'id',
      'local' => 'vehicleid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'aggregates' => 
  array (
    'Package' => 
    array (
      'class' => 'ModxsdkPackage',
      'key' => 'id',
      'local' => 'packageid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'packageid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'vehicleid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
