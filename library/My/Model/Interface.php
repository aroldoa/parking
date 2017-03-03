<?php
/**
 * My_Model_Interface
 * 
 * All models use this interface
 * 
 * @package    My_Model
 */
interface My_Model_Interface
{
    public function __construct($options = null);
    public function getResource($name);
    public function init();
}