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
    private $postIds;
    private $taxonomy;
    private $meta_query;
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

        if(!is_null($this->postIds))
        {
            $theParameters['post__in'] = $this->postIds;
        }

        if(!is_null($this->taxonomy))
        {
            $theParameters['tax_query'] = $this->taxonomy;
        }

        if(!is_null($this->meta_query))
        {
            $theParameters['meta_query'] = $this->meta_query;
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
    * Return an instance of ListItems calss to allow iteration through the content
    *
    **/
    public function iterate()
    {
        try {
            if(sizeof($this->contents) > 0 ) {
                $thelistItem = new ListItems($this->contents);
                foreach ($this->retrieveParameters() as $key => $value) {
                    $thelistItem->setParam($key , $value);
                }
                return $thelistItem->setParam('contentManagerParams' , $this->retrieveParameters());
            } else {
                throw new Exception("No post found ! Did you fetch() data before iterate them ?");
            }
        } catch (Exception $e) {
            $this->raiseException($e);
            return;
        }

        return $this;
    }




    /**
    *
    * Return an array of alk data retrieved for given parameters
    *
    **/
    public function paginate( $postPerPage = null )
    {
        if(isset($_POST['queryParameters'])){
            $queryParameters = json_decode(str_replace('\\', '', $_POST['queryParameters']));
            foreach ( $queryParameters as $parameterName => $parameterValue ) {
                if($parameterName == 'filter') {
                    $this->setFilterTaxonomy($parameterValue);
                } else if($parameterName == 'posts_per_page') {
                    $this->setPostPerPage($parameterValue);
                } else {
                    $this->setQueryParameters($parameterName , $parameterValue);
                }
            }
        }

        if($this->postPerPage < 0) {
            $this->setPostPerPage($postPerPage);
        }
        $offset = isset($_POST['step']) ? $_POST['step'] * $this->postPerPage : 0;
        $this->setQueryParameters('offset', $offset);

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
        if(!isset($this->contents[0])){
            return new Content();
        }
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

        switch($queryParametersKey) {
            case 'filter':
                $this->setFilterTaxonomy($queryParametersValue);
                break;
            case 'contributor':
                $this->setContributorMetaQuery($queryParametersValue);
                break;
            default:
                break;
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
    * Set an array of post id to search in
    *
    **/
    public function setPostIds( $postIds )
    {
        $this->postIds = $postIds;
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
    * Shortcut for setting a filtering
    *
    **/
    private function setFilterTaxonomy($tags) {
        $tags = gettype($tags) === 'object' ? get_object_vars($tags) : $tags;
        $tags = array_values($tags);

        if(sizeof($tags)) {
            $this->setTaxonomy(array(
                array (
                    'taxonomy' => 'filter',
                    'field'    => 'slug',
                    'terms'    => $tags
                )
            ));
        }
    }

    /**
    *
    * Set the meta query
    *
    **/
    public function setMetaQuery( $meta_query )
    {
        $this->meta_query = $meta_query;
        return $this;
    }

    /**
    *
    * Set a meta query to get post from a contributor
    *
    **/
    private function setContributorMetaQuery($contributor_id) {
        if($contributor_id) {
            $this->setMetaQuery(array(
                array(
                    'key' => 'mastercredits',
                    'value' => $contributor_id,
                    'compare' => 'LIKE'
                )
            ));
        }
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

    public function getPostsPerPage() {
        return $this->postPerPage;
    }

    /**
    *
    * Returns the number of contents
    *
    **/
    public function contentsCount() {
        return sizeof($this->contents);
    }

    /**
    *
    * Returns true if the content manager has at least one content
    *
    **/
    public function hasContent() {
        return $this->contentsCount() > 0;
    }
}


