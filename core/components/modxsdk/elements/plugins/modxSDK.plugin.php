<?php
switch($modx->event->name) {
    case 'OnManagerPageInit':
        if(!$path = $modx->getOption('modxsdk.manager_url')){
            $path = $modx->getOption('manager_url').'components/modxsdk/';
        }
        $cssFile = $path.'css/modxsdk.css';
        $modx->regClientCSS($cssFile);
    break;
}