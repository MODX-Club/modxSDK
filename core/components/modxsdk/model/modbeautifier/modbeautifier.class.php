<?php
require_once dirname(dirname(__DIR__)) . '/external/PHP_Beautifier/Beautifier.php';
class modBeautifier extends PHP_Beautifier
{
    public function __construct() 
    {
        parent::__construct();
        $this->addFilter('ArrayNested');
        $this->addFilter('IndentStyles', array(
            'style' => 'bsd'
        ));
        $this->addFilter('NewLines', array(
            'before' => T_COMMENT,
            'after' => T_COMMENT
        ));
        $this->setIndentChar(' ');
        $this->setIndentNumber(4);
        $this->setNewLine("\n");
    }
}
