<?php


/**
* FieldRenderer
*/
class FieldRender extends ContentRender
{

/**
*	==============================================================
*
* 	DATA MEMBER
*
* 	==============================================================
*/

	/**
	* List of the default content section
	**/
	private $contentSections = array(
		'image' 	 => '/<img[^>]+./',
		'video' 	 => '/\[embed(.*)](.*)\[\/embed]/',
		'blockquote' => '/<blockquote.*>(.*)<\/blockquote>/'
	);


	/**
	* List of the custom content section
	**/
	private $customContentTemplate = array();



/**
*   ==============================================================
*
*   PUBLIC
*
*   ==============================================================
*/


	/**
	*
	* Helper for the getCleanImage() method
	* Used to get the images in the right order from the attachements
	*
	**/
	private function retrieveAttachementFromFullUrl( $imageFullUrl )
	{
		foreach ($this->attachedImages as $theAttachedImage) {
			$theAttachedImageFullUrl = $theAttachedImage['image'][0];

			if( $imageFullUrl == $theAttachedImageFullUrl )
			{
				return $theAttachedImage['attachement'];
			}
		}
	}



	/**
	*
	* Default templating for image content section
	*
	**/
	private function getCleanImage( $image )
	{
		$imageTag = $image[0];

        preg_match_all( '/<img(?:\s+(?:src=["\'](?P<href>[^"\'<>]+)["\']|title=["\'](?P<title>[^"\'<>]+)["\']|\w+=["\'][^"\'<>]+["\']))+/ix' , $imageTag , $imageTagAttributes );

		$imageTagAttributes = $imageTagAttributes[1];
        $imageSrcCropped 	= $imageTagAttributes[0];

        $imageSrcFull = preg_replace('/-[0-9]{3,}x[0-9]{3,}/', '', $imageSrcCropped );

        $theAttachImage = $this->retrieveAttachementFromFullUrl( $imageSrcFull );

		$output = 	'<figure class="fieldContent-image">';
		$output .=		'<img src="'. $imageSrcFull .'" />';
		$output .=	'</figure>';

		if(strlen($theAttachImage->post_content) > 0)
		{
			$output = 	'<figure class="fieldContent-image">';
			$output .=		'<img src="'. $imageSrcFull .'" />';
			$output .=		'<figcaption class="fieldContent-image--caption">' . $theAttachImage->post_content . '</figcaption>';
			$output .=	'</figure>';
		}
		return $output;
	}



	/**
	*
	* Default templating for text content section
	*
	**/
	private function getCleanText( $text )
	{
		$text = trim($text);

		if(strlen($text) > 0 && $text != '&nbsp;')
			return '<p class="fieldContent">'. $text .'</p>';
	}



	/**
	*
	* Default templating for video content section
	*
	**/
	private function getCleanVideo( $video )
	{
		return '<div class="fieldContent-video">'. wp_oembed_get($video[2], array('width'=> 800 )) . '</div>';
	}



	/**
	*
	* Default templating for blockquote content section
	*
	**/
	private function getCleanBlockquote( $blockquote )
	{
		return '<p class="fieldContent-blockquote">'. $blockquote[1] . '</p>';
	}



	/**
	*
	* Initialization method
	* Retrieve all attachements for the post and parse it to be usable through the class
	*
	**/
	private function parseAttachements ( $attachementImage )
	{
		$this->attachedImages = array();

		foreach ($attachementImage as $anAttachementImage ) {
			$this->attachedImages[$anAttachementImage->ID] = array(
				'attachement' => $anAttachementImage,
				'image' => wp_get_attachment_image_src( $anAttachementImage->ID , 'full')
			);
		}
	}



	/**
	*
	* Initialization method
	* Split all the content by br/nl to make them handlable sepzratly
	*
	**/
	private function explodeContent ()
	{
		$this->explodedContent = explode('<br />', nl2br($this->initialContent));
	}



	/**
	*
	* Test the content of a part of the content in order to return the proper template
	*
	**/
	private function testContent ( $contentPart )
	{
		foreach ($this->contentSections as $sectionName => $sectionRegexp )
		{
			$methodName = 'getClean' . ucfirst($sectionName);
			if(preg_match( $sectionRegexp , $contentPart , $extarctedContent))
			{
				return $this->$methodName( $extarctedContent );
			}
		}

		foreach ($this->customContentSections as $sectionName => $sectionRegexp )
		{
			if(preg_match( $sectionRegexp , $contentPart , $extarctedContent))
			{
				$this->setTemplate( $this->customContentTemplate[ $sectionName ]);
				$$sectionName = $extarctedContent;

				try {
					if(!file_exists($this->template))
					{
						throw new Exception("The template $this->template for $sectionName cannot be found");
					}
				} catch (Exception $e) {
					$this->raiseException($e);
					return;
				}


				ob_start();
				include( $this->template );
				$template = ob_get_contents();
				ob_end_clean();

				return $template;
			}
		}

		// TODO : Allow custom template fo text
		return $this->getCleanText( $contentPart );
	}


/**
*   ==============================================================
*
*   CONSTRUCTORE
*
*   ==============================================================
*/


	/**
	*
	* Instanciate the class with the $post native element from Wordpress
	*
	**/
	public function __construct( $post )
	{
		$this->post = $post;
		$this->parseAttachements(get_attached_media('image', $this->post->ID));
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
	* Overload a template by a custom one
	* Use the filepath from the 'template' directory
	* Custom templates will get the data directly from the RegExp output in a variable named after the $contentSectionName
	*
	**/
	public function setCustomTemplate( $contentSectionName , $templateName )
	{
		$this->customContentTemplate[  $contentSectionName ] = $templateName;
		return $this;
	}


	/**
	*
	* Add a new content rule to existing ones
	* This rule will apply after the default ones
	* You must add the path to the template to be used for this section
	* Custom templates will get the data directly from the RegExp output in a variable named after the $contentSectionName
	*
	**/
	public function setCustomContentSection( $contentSectionName , $regexp , $templateName)
	{
		$this->setCustomTemplate( $contentSectionName , $templateName );
		$this->customContentSections[  $contentSectionName ] = $regexp;
		return $this;
	}


	/**
	*
	* Return the compiled string from the content initially given
	*
	**/
	public function getCleanContent( $fieldName )
	{
		$this->initialContent = $this->post->$fieldName;
		try {
			if(strlen($this->initialContent) == 0)
			{
				throw new Exception("There is no data in the object for key $fieldName");
			}
		} catch (Exception $e) {
			$this->raiseException($e);
			return;
		}

		$this->explodeContent();
		$output = '';

		if(sizeof($this->explodedContent) > 0)
		{
			foreach ($this->explodedContent as $contentSection) {
				$output .= $this->testContent($contentSection);
			}
		}

		return $output;
	}
}
