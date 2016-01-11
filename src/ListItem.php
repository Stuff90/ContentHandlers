<?php

/**
* List any number of items
*/
class ListItems extends ContentRender
{

  private $index = 0;

    /**
    *
    * The render method will loop into an array of data and include them given template with a customizable scope
    * The $itemName parameter will be the variable name available in the template scope
    *
    **/
	public function render( $itemName = 'item')
	{
    $stacktrace = @debug_backtrace(false);
    $this->parentTemplateName = str_replace('.php', '', basename($stacktrace[0]['file']));

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
      $this->index++;

    }

    if(!isset($_POST['queryParameters'])){
      echo '<input style="display:none;" id="generated-content-manager-parameters" type="hidden" value="' . htmlspecialchars(json_encode($this->contentManagerParams)) . '">';
    }


		return $this;
	}

  /**
  *
  * Get the index of the current element
  *
  */
  public function get_index() {
    return $this->index;
  }
}

