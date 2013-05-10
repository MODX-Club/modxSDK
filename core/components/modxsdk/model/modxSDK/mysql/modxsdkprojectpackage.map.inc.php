<?php
$xpdo_meta_map['ModxsdkProjectPackage']= array (
  'package' => 'modxSDK',
  'version' => '1.1',
  'table' => 'modxsdk_project_package',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'projectid' => NULL,
    'packageid' => NULL,
  ),
  'fieldMeta' => 
  array (
    'projectid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'packageid' => 
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
    'Project' => 
    array (
      'class' => 'ModxsdkProject',
      'key' => 'id',
      'local' => 'projectis',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
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
    'projectid' => 
    array (
      'alias' => 'projectid',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'projectid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'packageid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
