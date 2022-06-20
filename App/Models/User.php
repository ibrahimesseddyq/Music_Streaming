<?php

namespace App\Models;

use PDO;
use \App\Constants;
use Core\View;
use Core\DB;
use \App\Query;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{
    public $username;
    public $firstName;
    public $lastName;
    public $email;
    public $email2;
    public $password;
    public $password2;

    public $errors = [];

  /**
   * Class constructor
   *
   * @param array $data  Initial property values
   *
   * @return void
   */
    public function __construct($data = [])
    {
        parent::__construct();
        foreach((array)$data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function save()
    {
        $query = new Query;
        $this->validate();

        if (empty($this->errors)) {

            $profilePic = "public/img/profile-pics/head-emerald.png";
            
            $this->db->query('INSERT INTO users (username, firstName, lastName, email, password, signUpDate, profilePic)
                 VALUES (:username, :firstName, :lastName, :email, :password, :signUpDate, :profilePic)');

            $this->db->bind(':username', $this->username);
            $this->db->bind(':firstName', $this->firstName);
            $this->db->bind(':lastName', $this->lastName);
            $this->db->bind(':email', ucfirst(strtolower($this->email)));
            $this->db->bind(':password', md5($this->password));
            $this->db->bind(':signUpDate', date("Y-m-d"));
            $this->db->bind(':profilePic', $profilePic);

            if($this->db->execute()){
                $this->db->query('INSERT INTO artists (name,user_fk) VALUES (:name,:userfk)');
                $this->db->bind(':name', $this->username);
                $id = $query->getLastId();
                $this->db->bind(':userfk', $id);
                $this->db->execute();
                return $this->username;
            }
            return false;
        }

        return false;
    }

    public function validate()
    {
       // Username
        if (strlen($this->username) > 25 || strlen($this->username) < 5) {
            $this->errors['username'] = Constants::$usernameCharacters;
        }
        if ($this->username != str_replace(" ", "", $this->username)) {
            $this->errors['username'] = Constants::$usernameSpace;
        }
        if ($this->itemExists(['username' => $this->username])) {
            $this->errors['usernameExists'] = Constants::$usernameExists;
        }

        // FirstName
        if (strlen($this->firstName) > 25 || strlen($this->firstName) < 5) {
            $this->errors['firstName'] = Constants::$firstNameCharacters;
        }

        // LastName
        if (strlen($this->lastName) > 25 || strlen($this->lastName) < 5) {
            $this->errors['lastName'] = Constants::$lastNameCharacters;
        }

       // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors['emailValidation'] = Constants::$emailInvalid;
        }

        if ($this->email != $this->email2) {
            $this->errors['emailMatch'] = Constants::$emailsDoNotMatch;
        }
        if ($this->itemExists(['email' => $this->email])) {
            $this->errors['emailExists'] = Constants::$emailExists;
        }
        
       // Password
        if (strlen($this->password) < 6) {
            $this->errors['passwordLength'] = Constants::$passwordCharacters;
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors['passwordLetter'] = Constants::$passwordLetter;
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors['passwordNumber'] = Constants::$passwordNumber;
        }

        if ($this->password != $this->password2) {
            $this->errors['passwordMatch'] = Constants::$passwordsDoNoMatch;
        }
    }

    public function authenticate($username, $password)
    {
        $user = $this->findByField(['username' => $username]);

        if ($user) {
            if (md5($password) == $user->password) {
                return $user;
            }
        }

        return false;
    }
}
