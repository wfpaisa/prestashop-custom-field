<?php
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\CoreException;
/***
 * Class CustomerCore
 */
class Customer extends CustomerCore
{
	/*
    * module: ps_customercedula
    * date: 2021-04-28 15:49:55
    * version: 1.0.1
    */
	
    public $cedula;
    public function __construct($id = null)
    {
        self::$definition['fields']['cedula'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName');
        parent::__construct($id);
    }
}
