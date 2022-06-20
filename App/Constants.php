<?php

namespace App;

class Constants {

	public static $passwordsDoNoMatch = "Your passwords don't match";
    public static $passwordLetter = "Password needs at least one letter";
    public static $passwordNumber = "Password needs at least one number";
	public static $passwordCharacters = "Please enter at least 6 characters for the password";
	public static $emailInvalid = "Email is invalid";
	public static $emailsDoNotMatch = "Your emails don't match";
	public static $lastNameCharacters = "Your last name must be between 2 and 25 characters";
	public static $firstNameCharacters = "Your first name must be between 2 and 25 characters";
	public static $usernameCharacters = "Your username must be between 5 and 25 characters";
	public static $usernameSpace = "Username should consist of 1 word";
	public static $emailExists = "This email is already taken";
	public static $usernameExists = "This username is already taken";
	public static $loginFailed = "Username or password are wrong";

}
?>