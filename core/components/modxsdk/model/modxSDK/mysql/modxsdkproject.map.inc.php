<?php
$xpdo_meta_map['ModxsdkProject']= array (
  'package' => 'modxSDK',
  'version' => '1.1',
  'table' => 'modxsdk_project',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'description' => NULL,
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
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'composites' => 
  array (
    'ProjectPackages' => 
    array (
      'class' => 'ModxsdkProjectPackage',
      'key' => 'id',
      'local' => 'id',
      'foreign' => 'projectid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
