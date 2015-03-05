<?php
require_once MODX_PROCESSORS_PATH . 'browser/file/update.class.php';
class modxSDKFileUpdateProcessor extends modBrowserFileUpdateProcessor
{
    public function process() 
    {
        /* get base paths and sanitize incoming paths */
        $filePath = rawurldecode($this->getProperty('file', ''));
        $loaded = $this->getSource();
        if (!($this->source instanceof modMediaSource)) 
        {
            return $loaded;
        }
        if (!$this->source->checkPolicy('save')) 
        {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }
        $content = $this->_beautify();
        $path = $this->source->updateObject($filePath, $content);
        if (empty($path)) 
        {
            $msg = '';
            $errors = $this->source->getErrors();
            foreach ($errors as $k => $msg) 
            {
                $this->addFieldError($k, $msg);
            }
            return $this->failure($msg);
        }
        return $this->success('', array(
            'file' => $path,
            'content' => $content
        ));
    }
    # beautify
    protected function _beautify() 
    {
        $content = $this->getProperty('content');
        $path = MODX_CORE_PATH . 'components/modxsdk/model/';
        $this->modx->getService('phpB', 'modbeautifier.modBeautifier', $path);
        if ($this->modx->phpB) 
        {
            $this->modx->phpB->setInputString($content);
            $this->modx->phpB->process();
            $content = $this->modx->phpB->get();
        }
        return $content;
    }
}
return 'modxSDKFileUpdateProcessor';
