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

// Security check for install folder
if (file_exists(dirname(__FILE__) . '/install')) {
	$install_url = dirname($_SERVER['SCRIPT_NAME']) . "/install";
echo <<< EOF
<h1>ERROR: remove "install" folder before continuing...</h1>
If this is a fresh install visit: <a href="{$install_url}">install script</a>.<br/>
Otherwise if installation is finished remove completly "install" folder so that 
the site can start working
EOF;
	exit;
}

require_once dirname(__FILE__) . '/bootstrap.php';
require_once dirname(__FILE__) . '/web/layouts.php';

// DO NOT EDIT THIS FILE TO CHANGE DEFAULT PAGE
/**
 * This file is here to act as url router. To edit actual web pages
 * check /web folder. If you want a different global url behaviour
 * then you should it here.
 */

// Include all sub directories under /web
function is_valid_sub($sub)
{
    return is_file(dirname(__FILE__) . "/web/sub/$sub.php");
}

function include_sub($sub)
{
    require dirname(__FILE__)  . "/web/sub/$sub.php";
}

Stupid::add_rule('include_sub',
    array('type' => 'url_path', 'chunk[1]' => '/^([\w]+)$/'),
    array('type' => 'func', 'func' => 'is_valid_sub')
);

Stupid::set_default_action(array(CM5_Core::get_instance(), 'serve'));
Stupid::chain_reaction();

?>
