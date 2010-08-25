<?php

class UI_ModuleConfigure extends Output_HTML_Form
{
    public function __construct($module)
    {
        $this->module = $module;
        $this->mconfig = $module->get_config();
        $fields = array();
        foreach($module->config_options() as $id => $opt)
        {
            $fields[$id] = array('display' => $opt['display']);
            $f =  & $fields[$id];
            if (isset($opt['type']))
            {
                if ($opt['type'] === 'checkbox')
                    $f['type'] = 'checkbox';
                if ($opt['type'] === 'select')
                {
                    $f['type'] = 'dropbox';
                    $f['optionlist'] = $opt['options'];
                }

            }
            $f['value'] = $this->mconfig->{$id};
        }   
        parent::__construct(
            $fields,
        array('title' => 'Configure: ' . $this->module->info_property('title'),
            'css' => array('ui-form'),
		    'buttons' => array(
		        'upload' => array('display' =>'Save'),
	            'cancel' => array('display' =>'Cancel', 'type' => 'button',
	                'onclick' => "window.location='" . UrlFactory::craft('module.admin') . "'")
                )
            )
        );
    }
    
    public function on_valid($values)
    {
        foreach($values as $id => $value)
            $this->mconfig->{$id} = $value;

        $this->module->save_config();
        UrlFactory::craft('module.admin')->redirect();
    }
};

?>