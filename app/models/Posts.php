<?php namespace Phalconvn\Models;

use Phalcon\Mvc\Model;
class Posts extends Model
{

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var string
     */
    public $title;
     
    /**
     *
     * @var string
     */
    public $content;
     
    /**
     *
     * @var string
     */
    public $categoriesId;
     
    /**
     *
     * @var string
     */
    public $slug;
     
    /**
     *
     * @var string
     */
    public $tags;
     
    /**
     *
     * @var string
     */
    public $created;
     
    /**
     *
     * @var integer
     */
    public $numberViews;
     
    /**
     *
     * @var integer
     */
    public $userId;
     
    

    public function beforeValidationOnCreate()
    {
        $this->numberViews = '0';
        $this->created = time();

    }
    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        $this->created = time();
    }

    /**
     * Create a posts-views logging the ipaddress where the post was created
     * This avoids that the same session counts as post view
     */
    public function beforeCreate()
    {
        $postView = new PostsViews();
        $postView->ipaddress = $this->getDI()->getRequest()->getClientAddress();
        $this->views = $postView;
        $this->created = time();
    }
    public function afterCreate()
    {
        /**
         * Register a new activity
         */
        if ($this->id > 0) {
            /**
             * Update the total of posts related to a category
             */
            $this->category->numberPosts++;
            $this->category->save();
        }
    }
    public function beforeDelete()
    {
        //$id = $this->getDI()->getSession()->get('auth-identity');
        $id = $_SESSION['auth-identity'];
        if ($this->userId != $id['id']) {
            echo "The Post is not owner, it can't be deleted";
            return false;
        }
        return true;
    }

   /**
     * Validate that emails are unique across users
     */
    public function validation()
    {
        if ($this->validationHasFailed() == true) {
            return false;
        }
        return $this->validationHasFailed() != true;
    }
    public function initialize()
    {
        $this->hasMany('id', 'Phalconvn\Models\PostsViews', 'postsId', array(
            'alias' => 'views'
        ));
        $this->belongsTo('userId','Phalconvn\Models\Users','id',array(
            'alias' => 'userPost'
        ));

        $this->belongsTo('categoriesId', 'Phalconvn\Models\Categories', 'id', array(
            'alias' => 'category',
            'reusable' => true,
            'foreignKey' => array(
                'message' => 'The category is not valid'
            )
        ));
    }
   

}
