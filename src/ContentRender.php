<?php

/**
* ContentRender
*/
abstract class ContentRender
{
	use errorHandlerTrait;


/**
*   ==============================================================
*
*   PROTECTED
*
*   ==============================================================
*/



	/**
	*
	* Return the full path to templates root directory
	*
	**/
	protected function getTemplatesPath() {
		return get_template_directory() . "/app/templates/";
	}



/**
*   ==============================================================
*
*   CONSTRUCTOR
*
*   ==============================================================
*/



	public function __construct( $data )
	{
		$this->data = $data;
	}


/**
*   ==============================================================
*
*   PUBLIC
*
*   ==============================================================
*/



	/**
	*
	* Set the template to use for rendering
	*
	* There is 2 way to use this method :
	* 	- With one parameter : fill it with the path to your template from the template directory root + the name of the file without the extension
	* 	- With 2 parameters : Fill in the first parameter with the name of the template file without the extension and the second with the full path to the file.
	*
	**/
	public function setTemplate($templateName, $dirPath = null) {
		if(is_null($dirPath)) {
			$this->template = $this->getTemplatesPath() . $templateName . '.php';
		} else {
			$this->template = $dirPath . $templateName . '.php';
		}
		return $this;
	}


	/**
	*
	* Send data to the template scope
	* Use the key/value structure
	*
	**/
	public function setParam($name, $value) {
		$this->$name = $value;
		return $this;
	}


	/**
	*
	* Rendering function to render the set template and sending the related data
	* The parameter is used to set the variable name for data in the template scope
	*
	**/
	public function render( $itemName = 'item')
	{
		if(gettype($this->data) == 'array') {
			foreach ($this->data as $$itemName) {
				include( $this->template );
			}

		} else {
			$$itemName = $this->data;
			include( $this->template );
		}
		return $this;
	}
}


