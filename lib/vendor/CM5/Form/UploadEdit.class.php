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

/**
 * Form to edit an upload
 */
class CM5_Form_UploadEdit extends Form_Html
{
	/**
	 * @param CM5_Model_Upload $u The upload to be edited.
	 */
    public function __construct(CM5_Model_Upload $u)
    {
        $this->upload = $u;
        parent::__construct(null, array('title' => 'Edit upload',
            'attribs' => array('class' => 'form upload-edit'),
		    'buttons' => array(
		        'upload' => array('label' =>'Save'),
	            'cancel' => array('label' =>'Cancel', 'type' => 'button',
	                'attribs' => array('onclick' => "window.location='" . UrlFactory::craft('upload.admin') . "'")
                )
            )
        ));
    }
    
    public function configure()
    {
		$this->addMany(
        	field_raw('oldfile', array('label' => '', 'value' =>
                tag('span', tag('span class="filename"', $this->upload->filename),
                	tag('span class="size"', html_human_fsize($this->upload->filesize, ''))))),
			field_file('file', array('label' => 'File', 'multiple' => false, 'required' => false)),
			field_textarea('description', array('display' => 'Description',
			    'hint' => 'Optional description for file', 'type' => 'textarea',
			    'value' => $this->upload->description))
        );
    }

    public function onProcessValid()
    {
        if ($this->get('file')->getValue())
        {
            // Update file
            $this->upload->updateFromUploaded($this->get('file')->getValue());
        }
        $this->upload->description = $this->get('description')->getValue();
        $this->upload->save();
        
        UrlFactory::craft('upload.admin')->redirect();
    }
};
