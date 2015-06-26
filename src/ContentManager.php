<?php

/**
 * Class Handling contents retrievement
 */

class ContentManager
{
    use errorHandlerTrait;


/**
*   ==============================================================
*
*   DATA MEMBER
*
*   ==============================================================
*/



    private $contentType = array();
    private $postPerPage = -1;
    private $order = 'DESC';
    private $orderby = 'date';
    private $postId;
    private $taxonomy;
    private $queryParameters;

    private $contents = array();


/**
*   ==============================================================
*
*   CONSTRUCTOR
*
*   ==============================================================
*/



    public function __construct( $postId = null)
    {
        if(!is_null($postId))
        {
            $this->setPostId($postId);
            return $this;
        }
        return $this;
    }


/**
*   ==============================================================
*
*   PRIVATE
*
*   ==============================================================
*/




    /**
    *
    * This method will browse and validate the given parameters and then return an array of this paramerters
    *
    **/
    private function retrieveParameters()
    {
        $theParameters = $this->queryParameters;
        $theParameters['posts_per_page'] = $this->postPerPage;
        $theParameters['order'] = $this->order;
        $theParameters['orderby'] = $this->orderby;
        $theParameters['post_type'] = 'post';

        if(!is_null($this->postId))
        {
            $theParameters['p'] = $this->postId;
        }

        if(!is_null($this->taxonomy))
        {
            $theParameters['tax_query'] = $this->taxonomy;
        }

        if(!is_null($this->contentType))
        {
            $theParameters['post_type'] = $this->contentType;
        }
        return $theParameters;
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
    * Method called to perform the proper request
    * Before calling this method, not a single SQL call will be performed
    *
    **/
    public function fetch()
    {
        $theContents = new WP_Query( $this->retrieveParameters() );

        $counter = 0;

        while ( $theContents->have_posts() ) : $theContents->the_post();

            $aContent = new Content($theContents->post);

            $aContent->nb       = $counter;

            $this->contents[]   = $aContent;
            $this->count        = $theContents->found_posts;
            $counter++;

        endwhile;
        wp_reset_query();
        return $this;
    }




    /**
    *
    * Return an array of alk data retrieved for given parameters
    *
    **/
    public function all()
    {
        return $this->contents;
    }




    /**
    *
    * Return the first element of the batch of data the request received
    *
    **/
    public function first()
    {
        return $this->contents[0];
    }




    /**
    *
    * Return the list of parameters already set
    *
    **/
    public function getParams()
    {
        return $this->retrieveParameters();
    }




    /**
    *
    * Set a custom parameter freely
    *
    * You can pass a classic key/value parameter setup or directly an array of parameters
    *
    **/
    public function setQueryParameters( $queryParametersKey , $queryParametersValue = null )
    {
        if(gettype($queryParametersKey) == "array")
        {
            $this->queryParameters = array_merge( $this->queryParameters , $queryParametersKey );
        }
        else
        {
            $this->queryParameters[$queryParametersKey] = $queryParametersValue;
        }
        return $this;
    }





    /**
    *
    * Set a specific post Id
    *
    **/
    public function setPostId( $postId )
    {
        $this->postId = $postId;
        return $this;
    }




    /**
    *
    * Set the content type of the content you want to receive
    *
    **/
    public function setContentType( $contentType )
    {
        $this->contentType = $contentType;
        return $this;
    }




    /**
    *
    * Set the data field you want the order to apply on
    *
    **/
    public function setOrder( $order )
    {
        $this->order = $order;
        return $this;
    }




    /**
    *
    * Set the taxonomy the data must belong to
    *
    **/
    public function setTaxonomy( $taxonomy )
    {
        $this->taxonomy = $taxonomy;
        return $this;
    }




    /**
    *
    * Set the order you want the data to come (ASC or DESC)
    *
    **/
    public function setOrderBy( $orderby )
    {
        $this->orderby = $orderby;
        return $this;
    }




    /**
    *
    * Set the number of result per page you want to receive
    *
    **/
    public function setPostPerPage( $postPerPage )
    {
        $this->postPerPage = $postPerPage;
        return $this;
    }
}


