<?php
/*
 *  This file is part of CM5 <http://code.0x0lab.org/p/cm5>.
 *  
 *  Copyright (c) 2010 Sque.
 *  
 *  CM5 is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published 
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  CM5 is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with CM5.  If not, see <http://www.gnu.org/licenses/>.
 *  
 *  Contributors:
 *      Sque - initial API and implementation
 */

//! Interface to implement configurable modules
abstract class CM5_Configurable
{
    //! Implement with objects configuration nickname
    abstract public function config_nickname();
        
    //! Get an array with configuration options
    /**
     * Each entry is an associative array with the following fields.
     *  - @b name The name of this option
     *  - @b display The display to be shown 
     *  - @b type The type of the option (text, select, checkbox)
     *  - @b options If the type is option based
     *  .
     */
    public function config_options()
    {
        return array();
    }
    
    //! Default configuration of the module
    /**
     * An associative array with default configuration of the module.
     */
    public function default_config()
    {
        return array();
    }
    
    private $config = null;
    
    //! Get module configuration
    public function get_config()
    {
        // Return instance object
        if ($this->config !== null)
            return $this->config;

        // Read configuration from global config
        $gconfig = GConfig::get_instance();
        $nickname = $this->config_nickname();
        if (isset($gconfig->module->$nickname))
            $this->config = new Zend_Config(
                array_merge($this->default_config(), $gconfig->module->$nickname->toArray()), true);
        else
            $this->config = new Zend_Config($this->default_config(), true);

        return $this->config;
    }
    
    //! Save module configuration
    public function save_config()
    {
        $gconfig = GConfig::get_writable_copy();
        $gconfig->module->{$this->config_nickname()} = $this->config;
        GConfig::update($gconfig);
        
        if (method_exists($this, 'on_save_config'))
            $this->on_save_config();
    }
    
}
