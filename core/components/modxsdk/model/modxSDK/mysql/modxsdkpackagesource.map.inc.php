<?php
$xpdo_meta_map['ModxsdkPackagesource']= array (
  'package' => 'modxSDK',
  'version' => '1.1',
  'table' => 'modxsdk_packagesource',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'packageid' => NULL,
    'sourceid' => NULL,
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
    'sourceid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'aggregates' => 
  array (
    'Package' => 
    array (
      'class' => 'ModxsdkPackage',
      'local' => 'packageid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Source' => 
    array (
      'class' => 'sources.modMediaSource',
      'local' => 'sourceid',
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
        'sourceid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
