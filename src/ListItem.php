<?php

/**
* List any number of items
*/
class ListItems extends ContentRender
{


    /**
    *
    * The render method will loop into an array of data and include them given template with a customizable scope
    * The $itemName parameter will be the variable name available in the template scope
    *
    **/
	public function render( $itemName = 'item')
	{
		try {
			if(gettype($this->data) != 'array') {
				throw new Exception("You must pass an array as parameter in constructor of ListItems class");
			}
		} catch (Exception $e) {
			$this->raiseException($e);
			return;
		}
		foreach ($this->data as $$itemName) {
			include( $this->template );
		}
		return $this;
	}
}

