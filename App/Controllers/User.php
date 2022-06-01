<?php

namespace App\Controllers;

use App\Config;
use App\Model\UserRegister;
use App\Models\Articles;
use App\Utility\Hash;
use App\Utility\Session;
use \Core\View;
use Exception;
use http\Env\Request;
use http\Exception\InvalidArgumentException;

/**
 * User controller
 */
class User extends \Core\Controller
{

    /**
     * Affiche la page de login
     */
    public function loginAction()
    {
        if(isset($_POST['submit'])){
            $f = $_POST;

            // TODO: Validation

            $this->loginExec($f);

            // Si login OK, redirige vers le compte
            header('Location: /account');
        }

        View::renderTemplate('User/login.html');
    }

    /**
     * Page de création de compte
     */
    public function registerAction()
    {
        if(isset($_POST['submit'])){
            $f = $_POST;

            if($f['password'] !== $f['password-check']){
                // TODO: Gestion d'erreur côté utilisateur
            }

            // validation

            $this->registerExec($f);

            $this->loginExec($f);
            header('Location: /');
        }

        View::renderTemplate('User/register.html');
    }

    /**
     * Affiche la page du compte
     */
    public function accountAction()
    {
        $articles = Articles::getByUser($_SESSION['user']['id']);

        View::renderTemplate('User/account.html', [
            'articles' => $articles
        ]);
    }

    /*
     * Fonction privée pour enregister un utilisateur
     */
    private function registerExec($data)
    {
        try {
            // Generate a salt, which will be applied to the during the password
            // hashing process.
            if(preg_match('^[a-zA-Z0-9_.-]*$', $data['email'])){
                if(preg_match('^[a-zA-Z0-9_.-]*$', $data['email'])){
                    if(preg_match('^[a-zA-Z0-9_.-]*$', $data['email'])){
                        $salt = Hash::generateSalt(32);
                        $userID = \App\Models\User::createUser([
                            "email" => $data['email'],
                            "username" => $data['username'],
                            "password" => Hash::generate($data['password'], $salt),
                            "salt" => $salt
                        ]);
                        return $userID;
                    }else{
                        throw new Exception('Reg dont match');
                    }
                }else{
                    throw new Exception('Reg dont match');
                }
            }else{
                throw new Exception('Reg dont match');
            }
        } catch (Exception $ex) {
            // TODO : Set flash if error : utiliser la fonction en dessous
            /* Utility\Flash::danger($ex->getMessage());*/
        }
    }

    
    private function loginExec($data){
        try {
            if (preg_match('^[a-zA-Z0-9_.-]*$',$data['email'])){
                if(preg_match('^[a-zA-Z0-9_.-]*$',$data['password'])){
                    if(!isset($data['email'])){
                        throw new Exception('TODO');
                    }
        
                    $user = \App\Models\User::getByLogin($data['email']);
        
                    if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                        return false;
                    }
        
                    // TODO: Create a remember me cookie if the user has selected the option
                    // to remained logged in on the login form.
                    // https://github.com/andrewdyer/php-mvc-register-login/blob/development/www/app/Model/UserLogin.php#L86
        
                    $_SESSION['user'] = array(
                        'id' => $user['id'],
                        'username' => $user['username'],
                    );
        
                    return true;
                }else{
                    throw new Exception('Reg dont match');
                }
            }else{
                throw new Exception('Reg dont match');
            }
            

        } catch (Exception $ex) {
            // TODO : Set flash if error
            /* Utility\Flash::danger($ex->getMessage());*/
        }
    }


    /**
     * Logout: Delete cookie and session. Returns true if everything is okay,
     * otherwise turns false.
     * @access public
     * @return boolean
     * @since 1.0.2
     */
    public function logoutAction() {

        /*
        if (isset($_COOKIE[$cookie])){
            // TODO: Delete the users remember me cookie if one has been stored.
            // https://github.com/andrewdyer/php-mvc-register-login/blob/development/www/app/Model/UserLogin.php#L148
        }*/
        // Destroy all data registered to the session.

        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header ("Location: /");

        return true;
    }

}
