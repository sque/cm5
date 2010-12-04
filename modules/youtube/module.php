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

class CM5_Module_YouTube extends CM5_Module
{
    //! The name of the module
    public function info()
    {
        return array(
            'nickname' => 'youtube',
            'title' => 'YouTube embeded video',
            'description' => 'Capture YouTube urls inside articles and make them embeded videos.'
        );
    }
    
    //! Initialize module
    public function init()
    {
        $c = CM5_Core::get_instance();
        $c->events()->connect('page.pre-render', array($this, 'event_pre_render'));
    }
    
    public function default_config()
    {
        return array(
            'video-width' => '425',
            'video-height' => '344',
            'privacy-enchanced' => true,
            'border' => false,
            'controls-color-1' => 'b1b1b1',
            'controls-color-2' => 'd2d2d2',
        );
    }
    
    public function on_save_config()
    {
        CM5_Core::get_instance()->invalidate_page_cache(null);
    }
    
    public function config_options()
    {
        return array(
             'video-width' => array('display' => 'Video width:'),
             'video-height' => array('display' => 'Video height:'),
             'controls-color-1' => array('display' => 'Control color 1:', 'type' => 'color'),
             'controls-color-2' => array('display' => 'Control color 2:', 'type' => 'color'),
             'border' => array('display' => 'Show border:', 'type' => 'checkbox'),
             'privacy-enchanced' => array('display' => 'Privacy enchanced (cookie less youtube):',
                'type' => 'checkbox'),
        	'use-iframe' => array('display' => 'Use iframe (HTML5 capable)', 'type' => 'checkbox',
        		'hint' => 'Not all the options are supported in iframe mode.')
        );                    
    }
    
    public function create_embed_code($matches)
    {
        $vid = $matches[1];
        $host = ($this->get_config()->{'privacy-enchanced'}?'www.youtube-nocookie.com':'www.youtube.com');
        $color1 = $this->get_config()->{'controls-color-1'};
        $color2 = $this->get_config()->{'controls-color-2'};
        $width =  $this->get_config()->{'video-width'};
        $height =  $this->get_config()->{'video-height'};
        
        $link = "http://{$host}/v/{$vid}?fs=1&amp;hl=en_US&amp;color1=0x{$color1}&amp;color2=0x{$color2}";
        if ($this->get_config()->border)
            $link .= '&amp;border=1';
        
        if ($this->get_config()->{'use-iframe'}) 
        	return "<iframe title=\"YouTube video player\" class=\"youtube-player\" type=\"text/html\" " .
        		"width=\"${width}\" height=\"{$height}\" src=\"http://{$host}/embed/{$vid}?rel=0\" " .
        		"frameborder=\"0\"></iframe>";
        else
        	return "<object width=\"{$width}\" height=\"{$height}\"><param name=\"movie\" value=\"${link}\">" .
        		"</param><param name=\"allowFullScreen\" value=\"true\"></param><param " .
        		"name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"${link}\" " .
        		"type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" " .
        		"allowfullscreen=\"true\" width=\"{$width}\" height=\"{$height}\"></embed></object>";
        
    }
    
    private function replace_links(CM5_Model_Page $p)
    {

        if (strstr($p->body, 'www.youtube.com/watch') === false)
            return;

        $p->body = preg_replace_callback('#\bhttp://www.youtube.com/watch\?v=(?P<vid>[\w\-]+)[&\w=\-;]*#m',
            array($this, 'create_embed_code'),
            $p->body);
    }
    
    
    //! Handler for pre rendering
    public function event_pre_render($event)
    {
        $p = $event->filtered_value;
        
        // Execute subpages
        $this->replace_links($p);
    }
}

CM5_Module_YouTube::register();
?>
