<?php


/**
* Template
*/
class Template extends ContentRender
{

    /**
    *
    * The static get method will reach the template given in parameter quickly and can also send data to this template scope
    *
    **/
	static function get($templateName, $dirPath = null , $data = null , $dataName = null )
	{
		if(!is_null($dataName) && !is_null($data)) {
			$$dataName 	= $data;
			$data 		= null;
		}
		if(is_null($dirPath)) {
			$template = get_template_directory() . "/app/templates/" . $templateName . ".php";
		} else {
			$template = $dirPath . $templateName . '.php';
		}
		include( $template );
	}
}

