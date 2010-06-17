<?php
/*
 *  This file is part of PHPLibs <http://phplibs.kmfa.net/>.
 *  
 *  Copyright (c) 2010 < squarious at gmail dot com > .
 *  
 *  PHPLibs is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  PHPLibs is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with PHPLibs.  If not, see <http://www.gnu.org/licenses/>.
 *  
 */


class Layout_Admin extends Layout
{
    private $mainmenu = null;
    
    public function get_mainmenu()
    {
        return $this->mainmenu;
    }

    private function init_menu()
    {
        $this->mainmenu = new SmartMenu(array('class' => 'menu'));
        $this->events()->connect('pre-flush', create_function('$event',
        '
            $layout = $event->arguments["layout"];
            $layout->get_document()->get_body()->getElementById("main-menu")
                ->append($layout->get_mainmenu()->render());
        '));

        $this->mainmenu->create_link('Pages', '/admin/page');
        $this->mainmenu->create_link('Files', '/admin/files');
        $this->mainmenu->create_link('Users', '/admin/user');
    }
    
    protected function __init_layout()
    {   
        $this->activate();
        $doc = $this->get_document();    
        $this->get_document()->title = Config::get('site.title') . ' | Admin panel';
        $this->get_document()->add_ref_css(surl('/static/css/admin.css'));
        $this->get_document()->add_ref_js(surl('/static/js/jquery-1.4.2.min.js'));
        $this->get_document()->add_ref_js(surl('/static/js/jquery-ui-1.8.2.custom.min.js'));

        etag('div id="wrapper"')->push_parent();
        etag('div id="header"',
            tag('h1', Config::get('site.title')),
            tag('div id="main-menu"')
        );
        etag('div id="main"',
            $def_content = 
            tag('div id="content"')
        );
        etag('div id="footer"', 
            tag('a', 'PHPlibs', array('href' => 'http://phplibs.kmfa.net')),' skeleton'
        );
        
        if (Config::get('site.google_analytics'))
            etag('script type="text/javascript" html_escape_off',
            " var _gaq = _gaq || [];
              _gaq.push(['_setAccount', '" . Config::get('site.google_analytics') ."']);
              _gaq.push(['_trackPageview']);

              (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
              })();");
        $this->set_default_container($def_content);

        // Search widgeet
        $this->init_menu();
        $this->deactivate();
    }
}
?>
