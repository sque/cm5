<?php

class CM5_Module_Migration_UploadForm extends Form_Html
{
    public $upload_id = null;
    
    public function __construct()
    {   
        parent::__construct(null, array(
                'title' => 'Upload archive to server',
            	'attribs' => array('class' => 'form'),
                'buttons' => array(
                    'Process' => array('type' => 'submit')
                )
            )
        );
    }
    
    public function onInitialized()
    {
    	$current_uploads = array();
        foreach(CM5_Model_Upload::open_all() as $u)
            if (substr($u->filename, -7) == '.xml.gz')
                $current_uploads[$u->id] = "{$u->filename}  (" . date_exformat($u->lastmodified)->human_diff(null, false) . ")" ;

    	$this->addMany(
    		field_select('uploaded', array('label' => 'Archives already uploaded on server:', 
                    'optionlist' => $current_uploads)),
    		field_file('new-archive', array('label' => 'Or upload a new archive on server',
                    'hint' => '.xml.gz file generated by export process ( limit ' . ini_get('upload_max_filesize') . ')'))
    	);
    	
    	if (empty($current_uploads))
        {
            $f = & $this->remove('uploaded');
            $f['type'] = 'custom';
            $f['value'] = '&nbsp;&nbsp;&nbsp;(No archive was found...)';
        }
    }
    
    public function onProcessPost()
    {
        $newarchive = $this->get('new-archive');
        if (($newarchive == null) || ($newarchive->getValue() == null))
            return;
        
        $upload = $newarchive->getValue();
        if (substr($upload->getName(), -7) !== '.xml.gz')
            $this->invalidate_field('archive', 'The file must be a valid archive ');
            
        if (gzdecode(file_get_contents($upload->getTempName())) === false)
            $this->get('archive')->invalidate('The file must be a valid archive ');
    }
    
    public function onProcessValid()
    {
        $values = $this->getValues();
        if (!empty($values['uploaded']))
        {
            $this->upload_id = $values['uploaded'];
        }
        else if ($values['new-archive'])
        {
            $f = CM5_Model_Upload::createFromUploaded($values['new-archive']);
            $this->upload_id = $f->id;
        }
    }
}