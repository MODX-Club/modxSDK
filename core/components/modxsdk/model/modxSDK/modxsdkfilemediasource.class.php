<?php
 
if(!$this->loadClass('sources.modMediaSource')){
    $this->log(xPDO::LOG_LEVEL_ERROR, 'Could not load class modMediaSource');
}

if(!$this->loadClass('sources.modFileMediaSource')){
    $this->log(xPDO::LOG_LEVEL_ERROR, 'Could not load class modFileMediaSource');
}

class ModxsdkFileMediaSource extends modFileMediaSource{

    /*
     * Get objects list
     */
    
    public function __getObjectsContainerList($path) { 
        
        $properties = $this->getPropertyList();
        $imagesExts = explode(',', $this->getOption('imageExtensions',$properties,'jpg,jpeg,png,gif'));
        $path = $this->fileHandler->postfixSlash($path);
        $bases = $this->getBases($path);
        if (empty($bases['pathAbsolute'])) return array();
        $fullPath = $bases['pathAbsolute'].ltrim($path,'/');
                
        $useMultibyte = $this->getOption('use_multibyte',$properties,false);
        $encoding = $this->getOption('modx_charset',$properties,'UTF-8');
        $hideFiles = !empty($properties['hideFiles']) && $properties['hideFiles'] != 'false' ? true : false;
        $editAction = $this->getEditActionId();
        
        
        $allowedExtensions = $this->getOption('allowedExtensions',$properties,'php');
        $allowedExtensions = explode(',', $allowedExtensions);
         
        $skipFiles = $this->getOption('skipFiles',$properties,'.svn,.git,_notes,.DS_Store,nbproject,.idea');
        $skipFiles = explode(',',$skipFiles);
        if ($this->xpdo->getParser()) {
            $this->xpdo->parser->processElementTags('',$skipFiles,true,true);
        }
        $skipFiles[] = '.';
        $skipFiles[] = '..';

        $canSave = $this->checkPolicy('save');
        $canRemove = $this->checkPolicy('remove');
        $canCreate = $this->checkPolicy('create');

        $directories = array();
        $files = array();
        if (!is_dir($fullPath)) return array();

        /* iterate through directories */
        /** @var DirectoryIterator $file */
        foreach (new DirectoryIterator($fullPath) as $file) {
            if (in_array($file,$skipFiles)) continue;
            if (!$file->isReadable()) continue;

            $fileName = $file->getFilename();
            if (in_array(trim($fileName,'/'),$skipFiles)) continue;
            if (in_array($fullPath.$fileName,$skipFiles)) continue;
            $filePathName = $file->getPathname();
            $octalPerms = substr(sprintf('%o', $file->getPerms()), -4);

            /* handle dirs */
            $cls = array();
            if ($file->isDir() && $this->hasPermission('directory_list')) {
                $cls[] = 'folder';
                if ($this->hasPermission('directory_chmod') && $canSave) $cls[] = 'pchmod';
                if ($this->hasPermission('directory_create') && $canCreate) $cls[] = 'pcreate';
                if ($this->hasPermission('directory_remove') && $canRemove) $cls[] = 'premove';
                if ($this->hasPermission('directory_update') && $canSave) $cls[] = 'pupdate';
                if ($this->hasPermission('file_upload') && $canCreate) $cls[] = 'pupload';
                if ($this->hasPermission('file_create') && $canCreate) $cls[] = 'pcreate';

                $directories[$fileName] = array(
                    'id' => $bases['urlRelative'].rtrim($fileName,'/').'/',
                    'text' => $fileName,
                    'cls' => implode(' ',$cls),
                    'type' => 'dir',
                    'leaf' => false,
                    'path' => $bases['pathAbsoluteWithPath'].$fileName,
                    'pathRelative' => $bases['pathRelative'].$fileName,
                    'perms' => $octalPerms,
                    'menu' => array(),
                );
                $directories[$fileName]['menu'] = array('items' => $this->getListContextMenu($file,$directories[$fileName]));
            }

            /* get files in current dir */
            if ($file->isFile() && !$hideFiles && $this->hasPermission('file_list')) {
                $ext = pathinfo($filePathName,PATHINFO_EXTENSION);
                $ext = $useMultibyte ? mb_strtolower($ext,$encoding) : strtolower($ext);
                
                if(!in_array($ext, $allowedExtensions)) continue;
                $cls = array();
                $cls[] = 'icon-file';
                $cls[] = 'icon-'.$ext;

                if (!empty($properties['currentFile']) && rawurldecode($properties['currentFile']) == $fullPath.$fileName && $properties['currentAction'] == $editAction) {
                    $cls[] = 'active-node';
                }

                if ($this->hasPermission('file_remove') && $canRemove) $cls[] = 'premove';
                if ($this->hasPermission('file_update') && $canSave) $cls[] = 'pupdate';

                if (!$file->isWritable()) {
                    $cls[] = 'icon-lock';
                }
                $encFile = rawurlencode($fullPath.$fileName);
                $page = !empty($editAction) ? '?a='.$editAction.'&file='.$bases['urlRelative'].$fileName.'&wctx='.$this->ctx->get('key').'&source='.$this->get('id') : null;
                $url = ($bases['urlIsRelative'] ? $bases['urlRelative'] : $bases['url']).$fileName;

                /* get relative url from manager/ */
                $fromManagerUrl = $bases['url'].trim(str_replace('//','/',$path.$fileName),'/');
                $fromManagerUrl = ($bases['urlIsRelative'] ? '../' : '').$fromManagerUrl;
                $files[$fileName] = array(
                    'id' => $bases['urlRelative'].$fileName,
                    'text' => $fileName,
                    'cls' => implode(' ',$cls),
                    'type' => 'file',
                    'leaf' => false,
                    'qtip' => in_array($ext,$imagesExts) ? '<img src="'.$fromManagerUrl.'" alt="'.$fileName.'" />' : '',
                    // 'page' => $this->fileHandler->isBinary($filePathName) ? $page : null,
                    'perms' => $octalPerms,
                    'path' => $bases['pathAbsoluteWithPath'].$fileName,
                    'pathRelative' => $bases['pathRelative'].$fileName,
                    'directory' => $bases['path'],
                    'url' => $bases['url'].$url,
                    'urlAbsolute' => $bases['urlAbsolute'].ltrim($url,'/'),
                    'file' => $encFile,
                    'menu' => array(),
                    'source' => $encFile,
                ); 
                $files[$fileName]['menu'] = array('items' => $this->getListContextMenu($file,$files[$fileName]));
            }
        }

        $ls = array();
        /* now sort files/directories */
        ksort($directories);
        foreach ($directories as $dir) {
            $ls[] = $dir;
        }
        ksort($files);
        foreach ($files as $file) {
            $ls[] = $file;
        }

        return $ls;
    }
    
    
    /**
     * Return an array of files and folders at this current level in the directory structure
     * 
     * @param string $path
     * @return array
     */
    public function getPackageSourceContainerList($path) {
        $packageid = $this->get('packageid');
        $sourceid = $this->get('id');
        
        
        $properties = $this->getPropertyList();
        $path = $this->fileHandler->postfixSlash($path);
        $bases = $this->getBases($path);
        if (empty($bases['pathAbsolute'])) return array();
        $fullPath = $bases['pathAbsolute'].ltrim($path,'/');
                
        $useMultibyte = $this->getOption('use_multibyte',$properties,false);
        $encoding = $this->getOption('modx_charset',$properties,'UTF-8');
        $hideFiles = !empty($properties['hideFiles']) && $properties['hideFiles'] != 'false' ? true : false;
        $editAction = $this->getEditActionId();

        $imagesExts = $this->getOption('imageExtensions',$properties,'jpg,jpeg,png,gif');
        $imagesExts = explode(',',$imagesExts);
        $skipFiles = $this->getOption('skipFiles',$properties,'.svn,.git,_notes,.DS_Store,nbproject,.idea');
        $skipFiles = explode(',',$skipFiles);
        if ($this->xpdo->getParser()) {
            $this->xpdo->parser->processElementTags('',$skipFiles,true,true);
        }
        $skipFiles[] = '.';
        $skipFiles[] = '..';

        $canSave = $this->checkPolicy('save');
        $canRemove = $this->checkPolicy('remove');
        $canCreate = $this->checkPolicy('create');

        $directories = array();
        $files = array();
        if (!is_dir($fullPath)) return array();

        /* iterate through directories */
        /** @var DirectoryIterator $file */
        foreach (new DirectoryIterator($fullPath) as $file) {
            if (in_array($file,$skipFiles)) continue;
            if (!$file->isReadable()) continue;

            $fileName = $file->getFilename();
            if (in_array(trim($fileName,'/'),$skipFiles)) continue;
            if (in_array($fullPath.$fileName,$skipFiles)) continue;
            $filePathName = $file->getPathname();
            $octalPerms = substr(sprintf('%o', $file->getPerms()), -4);

            /* handle dirs */
            $cls = array();
            if ($file->isDir()) {
                if($this->hasPermission('directory_list')){
                    $cls[] = 'folder icon-folder';
                    if ($this->hasPermission('directory_chmod') && $canSave) $cls[] = 'pchmod';
                    if ($this->hasPermission('directory_create') && $canCreate) $cls[] = 'pcreate';
                    if ($this->hasPermission('directory_remove') && $canRemove) $cls[] = 'premove';
                    if ($this->hasPermission('directory_update') && $canSave) $cls[] = 'pupdate';
                    if ($this->hasPermission('file_upload') && $canCreate) $cls[] = 'pupload';
                    if ($this->hasPermission('file_create') && $canCreate) $cls[] = 'pcreate';
                    
                    $classes = implode(' ',$cls);
                    
                    $directories[$fileName] = array(
                        'id' => "n_dir_{$packageid}_{$sourceid}/". $bases['urlRelative'].rtrim($fileName,'/').'/',
                        'text' => $fileName,
                        # 'cls' => implode(' ',$cls),
                        'type' => 'dir',
                        'leaf' => false,
                        'path' => $bases['pathAbsoluteWithPath'].$fileName,
                        'pathRelative' => $bases['pathRelative'].$fileName,
                        'perms' => $octalPerms,
                        'menu' => array(),
                        'allowed_types'  => array(
                            'file',
                            'dir',
                        ),
                    );
                    $VersionData = $this->xpdo->getVersionData();
                    if(version_compare($VersionData['full_version'], "2.3")){
                        $directories[$fileName]['iconCls'] = $classes;
                    }
                    else{
                        $directories[$fileName]['cls'] = $classes;
                    }
                    
                    $directories[$fileName]['menu'] = array('items' => $this->getPackageSourceListContextMenu($file,$directories[$fileName]));
                }
            }

            /* get files in current dir */
            else if ($file->isFile() && !$hideFiles && $this->hasPermission('file_list')) {
                $ext = pathinfo($filePathName,PATHINFO_EXTENSION);
                $ext = $useMultibyte ? mb_strtolower($ext,$encoding) : strtolower($ext);

                $cls = array();
                $cls[] = 'icon-file';
                $cls[] = 'icon-'.$ext;

                if (!empty($properties['currentFile']) && rawurldecode($properties['currentFile']) == $fullPath.$fileName && $properties['currentAction'] == $editAction) {
                    $cls[] = 'active-node';
                }

                if ($this->hasPermission('file_remove') && $canRemove) $cls[] = 'premove';
                if ($this->hasPermission('file_update') && $canSave) $cls[] = 'pupdate';

                if (!$file->isWritable()) {
                    $cls[] = 'icon-lock';
                }
                $encFile = rawurlencode($fullPath.$fileName);
                $page = !empty($editAction) ? '?a='.$editAction.'&file='.$bases['urlRelative'].$fileName.'&wctx='.$this->ctx->get('key').'&source='.$this->get('id') : null;
                $url = ($bases['urlIsRelative'] ? $bases['urlRelative'] : $bases['url']).$fileName;

                /* get relative url from manager/ */
                $fromManagerUrl = $bases['url'].trim(str_replace('//','/',$path.$fileName),'/');
                $fromManagerUrl = ($bases['urlIsRelative'] ? '../' : '').$fromManagerUrl;
                
                $classes = implode(' ',$cls);
                
                $files[$fileName] = array(
                    'id' => $bases['urlRelative'].$fileName,
                    'text' => $fileName,
                    # 'cls' => implode(' ',$cls),
                    'type' => 'file',
                    'leaf' => true,
                    'qtip' => in_array($ext,$imagesExts) ? '<img src="'.$fromManagerUrl.'" alt="'.$fileName.'" />' : '',
                    // 'page' => $this->fileHandler->isBinary($filePathName) ? $page : null,
                    'perms' => $octalPerms,
                    'path' => $bases['pathAbsoluteWithPath'].$fileName,
                    'pathRelative' => $bases['pathRelative'].$fileName,
                    'directory' => $bases['path'],
                    'url' => $bases['url'].$url,
                    'urlAbsolute' => $bases['urlAbsolute'].ltrim($url,'/'),
                    'file' => $encFile,
                    'menu' => array(),
                    'source'    => $this->id,
                );
                    
                $VersionData = $this->xpdo->getVersionData();
                if(version_compare($VersionData['full_version'], "2.3")){
                    $files[$fileName]['iconCls'] = $classes;
                }
                else{
                    $files[$fileName]['cls'] = $classes;
                }
                
                $files[$fileName]['menu'] = array('items' => $this->getListContextMenu($file,$files[$fileName]));
            }
        }

        $ls = array();
        /* now sort files/directories */
        ksort($directories);
        foreach ($directories as $dir) {
            $ls[] = $dir;
        }
        ksort($files);
        foreach ($files as $file) {
            $ls[] = $file;
        }

        return $ls;
    }    
    
    
    public function __getFileInfo($path){
        // print '<pre>';
        $nodes = array();
        if(!$info = $this->getObjectContents($path)){
            return false;
        }
        $fileName = $info['basename'];
        
        $bases = $this->getBases($path);
        // print_r($info);
         
        
        $cls = array(
            'modxsdk-class-icon'
        ); 
        
        
        $classes = array();
        
        if(preg_match_all('/^[ \t]*(abstract|)[ \t]*class +([0-9\_a-z]+)/im', $info['content'], $match)){
            
            for($x=0; $x<count($match[2]);$x++){
                $classes[$match[2][$x]] = $match[1][$x];
            }
        }
        
        
        foreach($classes as $className => $t){
            $text = '';
 
            if($t) $text .= "{$t} ";
            
            if(self::getClassAbsoluteParent($this->xpdo, $className) == 'xPDOObject'){
                $cls[] = 'xPDOObject';
                $cls[] = 'xpdobject-class-icon'; 
                $text .= "xPDO ";
            }
            
            $text .= "class $className";  
            $url = ($bases['urlIsRelative'] ? $bases['urlRelative'] : $bases['url']).$fileName;
            $nodes[] = array(
                'id' => "class_{$path}_{$className}",
                'text'  => $text,
                'name'  => $className,
                'cls'   => implode(' ',$cls),
                'type'  => 'class',
                'leaf'  => false,
                //'qtip' => in_array($ext,$imagesExts) ? '<img src="'.$fromManagerUrl.'" alt="'.$fileName.'" />' : '',
                //'page' => $this->fileHandler->isBinary($filePathName) ? $page : null,
                //'perms' => $octalPerms,
                'path' => $bases['pathAbsoluteWithPath'].$fileName,
                'pathRelative' => $bases['pathRelative'].$fileName,
                'directory' => $bases['path'],
                'url' => $bases['url'].$url,
                'urlAbsolute' => $bases['urlAbsolute'].ltrim($url,'/'),
                'menu' => array(),
            ); 
        }
        
        return $nodes;
    }
    
    public function __getClassInfo($path, $className){
        $nodes = array(); 
        
        if(!$className || !$path){
            return false;
        }
        
        if(!$info = $this->getObjectContents($path)){
            return false;
        }
        $fileName = $info['basename'];
        $bases = $this->getBases($path);
        
        if(!class_exists($className)){
            require_once $info['path'];
        }
        
        if(!class_exists($className)){
            return false;
        }
        
        $class = new ReflectionClass($className);
        
        /*
         * collect constants
         */
        $consts = $class->getConstants();
        ksort($consts);
        
        foreach($consts as $k => $v){
            $cls = array(
                'modxsdk-const-icon'
            ); 
            $url = ($bases['urlIsRelative'] ? $bases['urlRelative'] : $bases['url']).$fileName;
            $nodes[] = array(
                'id' => "const_{$path}_{$className}_{$k}",
                'text' => $k,
                'cls' => implode(' ',$cls),
                'type' => 'var',
                'name' => $k,
                'leaf' => true,
                //'qtip' => in_array($ext,$imagesExts) ? '<img src="'.$fromManagerUrl.'" alt="'.$fileName.'" />' : '',
                //'page' => $this->fileHandler->isBinary($filePathName) ? $page : null,
                //'perms' => $octalPerms,
                'path' => $bases['pathAbsoluteWithPath'].$fileName,
                'pathRelative' => $bases['pathRelative'].$fileName,
                'directory' => $bases['path'],
                'url' => $bases['url'].$url,
                'urlAbsolute' => $bases['urlAbsolute'].ltrim($url,'/'),
                'menu' => array(),
            ); 
        }
        
        
        /*
         * collect vars
         */
        $vars = get_class_vars($className);
        ksort($vars);
        
        foreach($vars as $k => $v){
            $cls = array(
                'modxsdk-var-icon'
            ); 
            $url = ($bases['urlIsRelative'] ? $bases['urlRelative'] : $bases['url']).$fileName;
            $nodes[] = array(
                'id' => "var_{$path}_{$className}_{$k}",
                'text' => $k,
                'cls' => implode(' ',$cls),
                'type' => 'var',
                'name' => $k,
                'leaf' => true,
                //'qtip' => in_array($ext,$imagesExts) ? '<img src="'.$fromManagerUrl.'" alt="'.$fileName.'" />' : '',
                //'page' => $this->fileHandler->isBinary($filePathName) ? $page : null,
                //'perms' => $octalPerms,
                'path' => $bases['pathAbsoluteWithPath'].$fileName,
                'pathRelative' => $bases['pathRelative'].$fileName,
                'directory' => $bases['path'],
                'url' => $bases['url'].$url,
                'urlAbsolute' => $bases['urlAbsolute'].ltrim($url,'/'),
                'menu' => array(),
            ); 
        }
        
        /*
         * collect methods
         */
        $methods = get_class_methods($className); 
        
        foreach($methods as $k){
            $cls = array(
                'modxsdk-method-icon'
            );
            
            if(substr($k, 0, 2) == "__"){
                $cls[] = 'modxsdk-oop-icon';
            }
            
            $url = ($bases['urlIsRelative'] ? $bases['urlRelative'] : $bases['url']).$fileName;
            $nodes[] = array(
                'id' => "method_{$path}_{$className}_{$k}",
                'text' => $k,
                'cls' => implode(' ',$cls),
                'type' => 'method',
                'name' => $k,
                'leaf' => true,
                //'qtip' => in_array($ext,$imagesExts) ? '<img src="'.$fromManagerUrl.'" alt="'.$fileName.'" />' : '',
                //'page' => $this->fileHandler->isBinary($filePathName) ? $page : null,
                //'perms' => $octalPerms,
                'path' => $bases['pathAbsoluteWithPath'].$fileName,
                'pathRelative' => $bases['pathRelative'].$fileName,
                'directory' => $bases['path'],
                'url' => $bases['url'].$url,
                'urlAbsolute' => $bases['urlAbsolute'].ltrim($url,'/'),
                'menu' => array(),
            ); 
        }
        
        return $nodes;
    }
    
    public function getPackageSourceListContextMenu(DirectoryIterator $file,array $fileArray) {
        $canSave = $this->checkPolicy('save');
        $canRemove = $this->checkPolicy('remove');
        $canCreate = $this->checkPolicy('create');
        $canView = $this->checkPolicy('view');

        $menu = array();
        if (!$file->isDir()) { /* files */
            if ($this->hasPermission('file_update') && $canSave) {
                if (!empty($fileArray['page'])) {
                    $menu[] = array(
                        'text' => $this->xpdo->lexicon('file_edit'),
                        'handler' => 'this.editFile',
                    );
                    $menu[] = array(
                        'text' => $this->xpdo->lexicon('quick_update_file'),
                        'handler' => 'this.quickUpdateFile',
                    );
                }
                $menu[] = array(
                    'text' => $this->xpdo->lexicon('rename'),
                    'handler' => 'this.renameFile',
                );
            }
            if ($this->hasPermission('file_view') && $canView) {
                $menu[] = array(
                    'text' => $this->xpdo->lexicon('file_download'),
                    'handler' => 'this.downloadFile',
                );
            }
            if ($this->hasPermission('file_remove') && $canRemove) {
                if (!empty($menu)) $menu[] = '-';
                $menu[] = array(
                    'text' => $this->xpdo->lexicon('file_remove'),
                    'handler' => 'this.removeFile',
                );
            }
        } else { /* directories */
            if ($this->hasPermission('directory_create') && $canCreate) {
                $menu[] = array(
                    'text' => $this->xpdo->lexicon('file_folder_create_here'),
                    'handler' => 'this.createDirectory',
                );
            }
            /*if ($this->hasPermission('directory_chmod') && $canSave) {
                $menu[] = array(
                    'text' => $this->xpdo->lexicon('file_folder_chmod'),
                    'handler' => 'this.chmodDirectory',
                );
            }
            if ($this->hasPermission('directory_update') && $canSave) {
                $menu[] = array(
                    'text' => $this->xpdo->lexicon('rename'),
                    'handler' => 'this.renameDirectory',
                );
            }
            $menu[] = array(
                'text' => $this->xpdo->lexicon('directory_refresh'),
                'handler' => 'this.refreshActiveNode',
            );
            if ($this->hasPermission('file_upload') && $canCreate) {
                $menu[] = '-';
                $menu[] = array(
                    'text' => $this->xpdo->lexicon('upload_files'),
                    'handler' => 'this.uploadFiles',
                );
            }*/
            if ($this->hasPermission('file_create') && $canCreate) {
                /*$menu[] = array(
                    'text' => $this->xpdo->lexicon('file_create'),
                    'handler' => 'this.createFile',
                );*/
                $menu[] = array(
                    'text' => $this->xpdo->lexicon('quick_create_file'),
                    'handler' => 'this.quickCreateFile',
                );
            }/*
            if ($this->hasPermission('directory_remove') && $canRemove) {
                $menu[] = '-';
                $menu[] = array(
                    'text' => $this->xpdo->lexicon('file_folder_remove'),
                    'handler' => 'this.removeDirectory',
                );
            }*/
        }
        return $menu;
    }
    
    static public function __getClassAbsoluteParent(xPDO & $xpdo, $className){
        if(!$className){return false;}
        if(!class_exists($className) && !$xpdo->loadClass($className)){
            return false;
        }
        return self::_getClassAbsoluteParent($className);
    }
    
    static public function ___getClassAbsoluteParent($className){
        if(!$parent = get_parent_class($className)){
            return false;
        }
        if($absParent = self::_getClassAbsoluteParent($parent)){
            return $absParent;
        }
        else{
            return $parent;
        }
    }
    
    /*function getClassInfo($className, $file){
        require_once $file;
        $vars = (array)get_class_vars($className);
        $methods = (array)get_class_vars($className);
        // if(!$this->modx->loadClass($className))
    }*/
}

?>
