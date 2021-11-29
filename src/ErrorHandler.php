<?php
namespace SmartApi;

abstract class ErrorHandler implements \SmartApi\Interfaces\IErrorHandler
{
    private $initFunction, $type;
    /**
     *	@description	
     *	@param	
     */
    public function __construct($func, int $type = null)
    {
        $this->initFunction = $func;
        $this->type = (is_null($type))? E_ERROR : $type;
    }
    /**
     *	@description	
     *	@param	
     */
    public function start()
    {
        set_error_handler($this->initFunction, $this->type);
    }
}