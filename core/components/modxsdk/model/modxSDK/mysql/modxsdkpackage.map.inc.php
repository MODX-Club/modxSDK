<?php
$xpdo_meta_map['ModxsdkPackage']= array (
  'package' => 'modxSDK',
  'version' => '1.1',
  'table' => 'modxsdk_package',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'version_major' => 0,
    'version_minor' => 0,
    'version_patch' => 0,
    'version_type' => 'beta',
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'version_major' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'version_minor' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'version_patch' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'version_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'beta',
    ),
  ),
  'composites' => 
  array (
    'PackageVehicles' => 
    array (
      'class' => 'ModxsdkPackageVehicle',
      'local' => 'id',
      'foreign' => 'packageid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'PackageProjects' => 
    array (
      'class' => 'ModxsdkProjectPackage',
      'key' => 'id',
      'local' => 'id',
      'foreign' => 'packageid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Source' => 
    array (
      'class' => 'ModxsdkPackagesource',
      'local' => 'id',
      'foreign' => 'packageid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
  ),
  'indexes' => 
  array (
    'name' => 
    array (
      'alias' => 'name',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'name' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'version_major' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'version_minor' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'version_patch' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'version_type' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
