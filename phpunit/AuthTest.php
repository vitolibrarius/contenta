<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:34.
 */



use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:34. */
use \http\Session as Session;
use \http\Cookies as Cookies;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;
use \Model as Model;
use \Localized as Localized;
use \Config as Config;
use \Logger as Logger;
use \model\user\Users as Users;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-31 10:53:16. */
use \model\user\UsersDBO as UsersDBO;
/* {useStatements} */

class AuthTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Users" ) );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
		Session::init( new \http\GlobalMemoryAdapter() );
		Cookies::init( new \http\GlobalMemoryAdapter() );
		HttpGet::init( new \http\GlobalMemoryAdapter() );
		HttpPost::init( new \http\GlobalMemoryAdapter() );
    }

    protected function tearDown()
    {
    }

/*  Test functions */

	/**
	 * @covers	handleLogin
	 * 			T_FUNCTION T_STATIC T_PUBLIC handleLogin ( )
     * @runInSeparateProcess
     * @preserveGlobalState disabled
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 10:53:16.
	 */
	public function testHandleLogin()
	{
		$this->assertFalse( Auth::handleLogin(), "Login should have failed" );

		Session::init( new \http\GlobalMemoryAdapter() );
		Cookies::set('rememberme', "junkvalue" );
		$this->assertFalse( Auth::handleLogin(), "Login should have failed" );

		$userDBO = Model::Named("Users")->objectForEmail('test@home.com');
		$cookie_string_first_part = $userDBO->id . ':' . $userDBO->rememberme_token();
		$cookie_string_hash = hash('sha256', $cookie_string_first_part);
		$cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;
		Session::init( new \http\GlobalMemoryAdapter() );
		Cookies::set('rememberme', $cookie_string );
		$this->assertTRUE( Auth::handleLogin(), "Login should have succeeded" );
	}

	/**
	 * @covers	handleLoginWithAPI
	 * 			T_FUNCTION T_STATIC T_PUBLIC handleLoginWithAPI ( $userHash = '')
     * @runInSeparateProcess
     * @preserveGlobalState disabled
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 10:53:16.
	 */
	public function testHandleLoginWithAPI()
	{
		// no hash
		Session::init( new \http\GlobalMemoryAdapter() );
		$this->assertFalse( Auth::handleLoginWithAPI(), "handleLoginWithAPI should have failed" );

		Session::init( new \http\GlobalMemoryAdapter() );
		$this->assertFalse( Auth::handleLoginWithAPI(''), "handleLoginWithAPI should have failed" );

		// random hash
		Session::init( new \http\GlobalMemoryAdapter() );
		$this->assertFalse( Auth::handleLoginWithAPI(uuidShort()), "handleLoginWithAPI should have failed" );

		$userDBO = Model::Named("Users")->objectForEmail('test@home.com');
		Session::init( new \http\GlobalMemoryAdapter() );
		$this->assertTrue( Auth::handleLoginWithAPI($userDBO->api_hash()), "handleLoginWithAPI should have worked" );
		$userDBO = Model::Named("Users")->refreshObject( $userDBO );

		$this->assertEquals( true, Session::get('user_logged_in'), "Session var not set user_logged_in" );
		$this->assertEquals( $userDBO->id, Session::get('user_id'), "Session var not set user_id" );
		$this->assertEquals( $userDBO->name, Session::get('user_name'), "Session var not set user_name" );
		$this->assertEquals( $userDBO->email, Session::get('user_email'), "Session var not set user_email" );
		$this->assertEquals( $userDBO->account_type, Session::get('user_account_type'), "Session var not set user_account_type" );
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect updated failed login count" );
		$this->assertTrue( $userDBO->last_login_timestamp() > (time()-30), "Incorrect updated last_login_timestamp "
			. $userDBO->formattedDateTime_last_login_timestamp() . " | " . time() );
	}

	/**
	 * @covers	requireRole
	 * 			T_FUNCTION T_STATIC T_PUBLIC requireRole ( $role = null)
     * @runInSeparateProcess
     * @preserveGlobalState disabled
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 10:53:16.
	 */
	public function testRequireRole()
	{
		$this->assertTrue( Auth::requireRole(), "No role" );

		Session::init( new \http\GlobalMemoryAdapter() );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_name" )), "test" );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_password" )), "abc123_!890xyz" );
		$this->assertTrue( Auth::login(), "Login should have succeeded" );
		$this->assertTrue( Auth::requireRole(Users::StandardRole), "Standard Role" );

		$threwException = false;
		try {
			Session::init( new \http\GlobalMemoryAdapter() );
			HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_name" )), "test" );
			HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_password" )), "abc123_!890xyz" );
			$this->assertTrue( Auth::login(), "Login should have succeeded" );
			// should throw
			Auth::requireRole(Users::AdministratorRole);
		}
		catch (Exception $e) {
			$threwException = true;
		}
		$this->assertTrue( $threwException, "Administrator Role" );

		Session::init( new \http\GlobalMemoryAdapter() );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_name" )), "vito" );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_password" )), "abc123_!890xyz" );
		$this->assertTrue( Auth::login(), "Login should have succeeded" );
		$this->assertTrue( Auth::requireRole(Users::StandardRole), "Standard Role" );
		$this->assertTrue( Auth::requireRole(Users::AdministratorRole), "Administrator Role" );
	}

	/**
	 * @covers	login
	 * 			T_FUNCTION T_STATIC T_PUBLIC login ( )
     * @runInSeparateProcess
     * @preserveGlobalState disabled
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 10:53:16.
	 */
	public function testLogin()
	{
		// no values
		$this->assertFalse( Auth::login(), "Login should have failed" );

		// missing password
		Session::init( new \http\GlobalMemoryAdapter() );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_name" )), "test" );
		$this->assertFalse( Auth::login(), "Login should have failed" );

		// wrong username
		Session::init( new \http\GlobalMemoryAdapter() );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_name" )), "testFail" );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_password" )), "abc123_!890xyz" );
		$this->assertFalse( Auth::login(), "Login should have failed" );

		// more than 3 fails in the last 3 seconds
		$userDBO = Model::Named("Users")->objectForEmail('login_test@home.com');
		$userDBO->clearFailedLogin();
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect updated failed login count" );
		$userDBO->increaseFailedLogin();
		$userDBO->increaseFailedLogin();
		$userDBO->increaseFailedLogin();
		Session::init( new \http\GlobalMemoryAdapter() );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_name" )), "login_test" );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_password" )), "abc123_!890xyz" );
		$this->assertFalse( Auth::login(), "Login should have failed" );

		// password wrong
		$userDBO = Model::Named("Users")->objectForEmail('test@home.com');
		$userDBO->clearFailedLogin();
		$userDBO = Model::Named("Users")->refreshObject($userDBO);
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect initial failed login count" );

		Session::init( new \http\GlobalMemoryAdapter() );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_name" )), "test" );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_password" )), "wrong" );
		$this->assertFalse( Auth::login(), "Login should have failed" );
		$userDBO = Model::Named("Users")->refreshObject($userDBO);
		$this->assertEquals( 1, $userDBO->failed_logins(), "Incorrect updated failed login count" );

		// success
		Session::init( new \http\GlobalMemoryAdapter() );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_name" )), "test" );
		HttpPost::set( implode(	\Model::HTML_ATTR_SEPARATOR, array( "users", "user_password" )), "abc123_!890xyz" );
		$this->assertTrue( Auth::login(), "Login should have succeeded" );
		$userDBO = Model::Named("Users")->refreshObject($userDBO);
		$this->assertEquals( true, Session::get('user_logged_in'), "Session var not set user_logged_in" );
		$this->assertEquals( $userDBO->id, Session::get('user_id'), "Session var not set user_id" );
		$this->assertEquals( $userDBO->name, Session::get('user_name'), "Session var not set user_name" );
		$this->assertEquals( $userDBO->email, Session::get('user_email'), "Session var not set user_email" );
		$this->assertEquals( $userDBO->account_type, Session::get('user_account_type'), "Session var not set user_account_type" );
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect updated failed login count" );
		$this->assertTrue( $userDBO->last_login_timestamp() > (time()-30), "Incorrect updated last_login_timestamp " . $userDBO->last_login_timestamp() . " | " . time() );
	}

	/**
	 * @covers	loginWithCookie
	 * 			T_FUNCTION T_STATIC T_PUBLIC loginWithCookie ( )
     * @runInSeparateProcess
     * @preserveGlobalState disabled
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 10:53:16.
	 */
	public function testLoginWithCookie()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Cookies::set('rememberme', "junkvalue" );
		$this->assertFalse( Auth::loginWithCookie(), "Login should have failed" );
		$this->assertEmpty( Cookies::get("rememberme", null), "Failed to get value after setting it" );

		$userDBO = Model::Named("Users")->objectForEmail('test@home.com');
		$cookie_string_first_part = $userDBO->id . ':' . $userDBO->rememberme_token();
		$cookie_string_hash = hash('sha256', $cookie_string_first_part);
		$cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;
		Session::init( new \http\GlobalMemoryAdapter() );
		Cookies::set('rememberme', $cookie_string );

		$this->assertTrue( Auth::loginWithCookie(), "Login should have succeeded" );
		$userDBO = Model::Named("Users")->refreshObject( $userDBO );

		$this->assertEquals( true, Session::get('user_logged_in'), "Session var not set user_logged_in" );
		$this->assertEquals( $userDBO->id, Session::get('user_id'), "Session var not set user_id" );
		$this->assertEquals( $userDBO->name, Session::get('user_name'), "Session var not set user_name" );
		$this->assertEquals( $userDBO->email, Session::get('user_email'), "Session var not set user_email" );
		$this->assertEquals( $userDBO->account_type, Session::get('user_account_type'), "Session var not set user_account_type" );
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect updated failed login count" );
		$this->assertTrue( $userDBO->last_login_timestamp() > (time()-30), "Incorrect updated last_login_timestamp" );
	}

	/**
	 * @covers	httpAuthenticate
	 * 			T_FUNCTION T_STATIC T_PUBLIC httpAuthenticate ( $auth_type = 'Basic', $auth_user, $auth_pw)
     * @runInSeparateProcess
     * @preserveGlobalState disabled
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 10:53:16.
	 */
	public function testHttpAuthenticate()
	{
		// no values
		$this->assertFalse( Auth::httpAuthenticate( null, null, null), "httpAuthenticate should have failed" );

		// missing password
		Session::init( new \http\GlobalMemoryAdapter() );
		$this->assertFalse( Auth::httpAuthenticate(null, "nobody", null), "httpAuthenticate should have failed" );

		// wrong username
		Session::init( new \http\GlobalMemoryAdapter() );
		$this->assertFalse( Auth::httpAuthenticate(null, "nobody", "garbage"), "httpAuthenticate should have failed" );

		// more than 3 fails in the last 3 seconds
		$userDBO = Model::Named("Users")->objectForEmail('login_test@home.com');
		$userDBO->clearFailedLogin();
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect updated failed login count" );
		$userDBO->increaseFailedLogin();
		$userDBO->increaseFailedLogin();
		$userDBO->increaseFailedLogin();
		Session::init( new \http\GlobalMemoryAdapter() );
		$this->assertFalse( Auth::httpAuthenticate(null, "login_test", "abc123_!890xyz"), "httpAuthenticate should have failed (3 strikes)" );

		// password wrong
		$userDBO = Model::Named("Users")->objectForEmail('test@home.com');
		$userDBO->clearFailedLogin();
		$userDBO = Model::Named("Users")->refreshObject($userDBO);
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect initial failed login count" );

		Session::init( new \http\GlobalMemoryAdapter() );
		$this->assertFalse( Auth::httpAuthenticate(null, "test", "wrong"), "httpAuthenticate should have failed (password)" );
		$userDBO = Model::Named("Users")->refreshObject($userDBO);
		$this->assertEquals( 1, $userDBO->failed_logins(), "Incorrect updated failed login count" );

		// success
		Session::init( new \http\GlobalMemoryAdapter() );
		$this->assertTrue( Auth::httpAuthenticate(null, "test", "abc123_!890xyz"), "httpAuthenticate should have failed" );
		$this->assertEquals( true, Session::get('user_logged_in'), "Session var not set user_logged_in" );
		$this->assertEquals( $userDBO->id, Session::get('user_id'), "Session var not set user_id" );
		$this->assertEquals( $userDBO->name, Session::get('user_name'), "Session var not set user_name" );
		$this->assertEquals( $userDBO->email, Session::get('user_email'), "Session var not set user_email" );
		$this->assertEquals( $userDBO->account_type, Session::get('user_account_type'), "Session var not set user_account_type" );
	}


/* {functions} */
}
