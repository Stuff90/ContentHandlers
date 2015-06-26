<?php


/**
 * Class Content
 */

class Content
{
    use errorHandlerTrait;


/**
*   ==============================================================
*
*   DAT MEMBER
*
*   ==============================================================
*/


    private $data;
    private $compositeData = array();


/**
*   ==============================================================
*
*   CONSSTRUCTOR
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
    * Magic setter used to easily reference data to key in Content object
    *
    **/
    public function __set( $name , $value )
    {
        $this->compositeData[$name] = $value;
        return $this;
    }


    /**
    *
    * Magic getter is used to reach data from the ARRAY retrieved by the WP_Query
    *
    **/
    public function __get( $name )
    {
        if (!isset($this->data->$name)) {
            return $this->compositeData[$name];
        }
        return $this->data->$name;
    }



    /**
    *
    * Extract method will transform an int or an JSON string of ints to Content formatted data
    * The $name parameter must be a valid field name and the $type can be any referenced type in Wordpress
    *
    **/
    public function extract( $name , $type = 'post' )
    {
        $dataSrc = json_decode($this->data->$name);
        try {
            if(is_null($dataSrc)) {
                throw new Exception("The data contained in object for key \"$name\" cannot be parsed");
            }
        } catch (Exception $e) {
            $this->raiseException($e);
            return;
        }

        if (gettype($dataSrc) == 'array')
        {
            if ( $type != 'attachement' )
            {
                $extractedContent = new ContentManager();
                return $extractedContent
                    ->setContentType( $type )
                    ->setQueryParameters('post__in', $dataSrc)
                    ->fetch()
                    ->all();
            }
            $attachements = array();
            foreach ($dataSrc as $anAttachementId) {
                $attachements[] = new Content(get_post( $anAttachementId ));
            }
            return $attachements;
        } elseif (gettype($dataSrc) == 'integer') {
            return new Content(get_post($dataSrc));
        }
        return;
    }






    /**
    *
    * Return the full path to templates root directory
    *
    **/
    public function getImage( $name , $size = "thumbnail" , $icon = false )
    {
        if (!isset($this->data->$name)) {
            return $this->compositeData[$name];
        }

        $attachementID = json_decode($this->data->$name);

        if(gettype($attachementID) == 'array'){
            $results = array();

            foreach ($attachementID as $theAttachementID) {
                $results[] = wp_get_attachment_image_src( $theAttachementID , $size, $icon );
            }
            return $results;
        }
        return wp_get_attachment_image_src( $this->data->$name , $size, $icon );
    }



    /**
    *
    * Return the full path to templates root directory
    *
    **/
    public function getImageUrl( $name , $size = "thumbnail" , $icon = false )
    {
        $theImageArray = $this->getImage( $name , $size , $icon );
        if(gettype($attachementID) == 'array'){
            return $theImageArray[0];
        }
        return $theImageArray[0];
    }
}






