<?php namespace Phalconvn\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * Eduapps\Models\Users
 *
 * All the users registered in the application
 */
class Users extends Model
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
    public $username;
     
    /**
     *
     * @var string
     */
    public $email;
     
    /**
     *
     * @var string
     */
    public $password;
     
    /**
     *
     * @var string
     */
    public $mustChangePassword;
     
    /**
     *
     * @var integer
     */
    public $profilesId;
     
   
     
    /**
     *
     * @var string
     */
    public $banned;
     
    /**
     *
     * @var string
     */
    public $suspended;
     
    /**
     *
     * @var string
     */
    public $active;
     
    /**
	 * Before create the user assign a password
	 */
	public function beforeValidationOnCreate()
	{
		if (empty($this->password)) {

			//Generate a plain temporary password
			$tempPassword = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(12)));

			//The user must change its password in first login
			$this->mustChangePassword = 'Y';

			//Use this password as default
			$this->password = $this->getDI()->getSecurity()->hash($tempPassword);

		} else {
			//The user must not change its password in first login
			$this->mustChangePassword = 'N';

		}

		//The account must be confirmed via e-mail
		$this->active = 'Y';

		//The account is not suspended by default
		$this->suspended = 'N';

		//The account is not banned by default
		$this->banned = 'N';
      
	}

	/**
	 * Send a confirmation e-mail to the user if the account is not active
	 */
	public function afterSave()
	{
		if ($this->active == 'N') {

			$emailConfirmation = new EmailConfirmations();

			$emailConfirmation->usersId = $this->id;

			if ($emailConfirmation->save()) {
				$this->getDI()->getFlash()->notice(
					'A confirmation mail has been sent to ' . $this->email
				);
			}
		}
	}

	/**
	 * Validate that emails are unique across users
	 */
	public function validation()
	{

		$this->validate(new Uniqueness(
			array(
				"field"   => "email",
				"message" => "E-mail đã tồn tại!"
			)
		));
		if ($this->validationHasFailed() == true) {
            return false;
        }
		//return $this->validationHasFailed() != true;
	}

	public function initialize()
	{

		$this->belongsTo('profilesId', 'Phalconvn\Models\Profiles', 'id', array(
			'alias' => 'profile',
			'reusable' => true
		));
		$this->hasMany('id', 'Phalconvn\Models\Posts', 'userId', array(
			'alias' => 'posts',
			'foreignKey' => array(
				'message' => 'User cannot be deleted because article has not for '
			)
		));
		$this->hasMany('id', 'Phalconvn\Models\SuccessLogins', 'usersId', array(
			'alias' => 'successLogins',
			'foreignKey' => array(
				'message' => 'User acannot be deleted because he/she has activity in the system'
			)
		));


		$this->hasMany('id', 'Phalconvn\Models\PasswordChanges', 'usersId', array(
			'alias' => 'passwordChanges',
			'foreignKey' => array(
				'message' => 'User cannot be deleted because he/she has activity in the system'
			)
		));

		$this->hasMany('id', 'Phalconvn\Models\ResetPasswords', 'usersId', array(
			'alias' => 'resetPasswords',
			'foreignKey' => array(
				'message' => 'User cannot be deleted because he/she has activity in the system'
			)
		));
	}
   

}
