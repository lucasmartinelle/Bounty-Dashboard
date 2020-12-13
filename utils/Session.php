<?php
    namespace Utils;

    require_once("models/userHandler.php");
    use Models\UserHandler;

    class Session {
        private $_userHandler;

        // Check if user is authenticate or not
        public function isAuth(){
            $this->_userHandler = new UserHandler;
            $auth = false;
            $id;
            $username;
            $email;
            $password;
            if (isset($_SESSION['id']) AND !empty($_SESSION['id']) AND isset($_SESSION['username']) AND !empty($_SESSION['username']) AND isset($_SESSION['email']) AND !empty($_SESSION['email']) AND isset($_SESSION['password']) AND !empty($_SESSION['password'])) {
                $id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                $username = htmlspecialchars($_SESSION['username'], ENT_QUOTES);
                $email = htmlspecialchars($_SESSION['email'], ENT_QUOTES);
                $password = htmlspecialchars($_SESSION['password'], ENT_QUOTES);
                $auth = true;
            }
        
            if (isset($_COOKIE['id']) AND !empty($_COOKIE['id']) AND isset($_COOKIE['username']) AND !empty($_COOKIE['username']) AND isset($_COOKIE['email']) AND !empty($_COOKIE['email']) AND isset($_COOKIE['password']) AND !empty($_COOKIE['password'])) {
                $id = htmlspecialchars($_COOKIE['id'], ENT_QUOTES);
                $username = htmlspecialchars($_COOKIE['username'], ENT_QUOTES);
                $email = htmlspecialchars($_COOKIE['email'], ENT_QUOTES);
                $password = htmlspecialchars($_COOKIE['password'], ENT_QUOTES);
                $auth = true;
            }
            if($auth == true){
                $users = $this->_userHandler->getUsers(array("username" => $username, "email" => $email, "password" => $password));
                foreach($users as $user){
                    if($user->username() == $username && $user->email() == $email && $user->password() == $password){
                        $_SESSION['role'] = $user->role();
                        $token = bin2hex(openssl_random_pseudo_bytes(16));
                        $_SESSION['token'] = $token;
                        $this->_userHandler->updateUser(array("token" => $token), array("email" => $email));
                        return true;
                    }
                }
            }
            return false;
        }

        // Authenticate user if he is in users table
        public function Auth($email, $rem){
            $this->_userHandler = new UserHandler;
            $users = $this->_userHandler->getUsers(array("email" => $email));
            foreach($users as $user){
                if($rem == 'on'){
                    setcookie('id', $user->id(), time() + (86400 * 30), "/");
                    setcookie('username', $user->username(), time() + (86400 * 30), "/");
                    setcookie('email', $user->email(), time() + (86400 * 30), "/");
                    setcookie('password', $user->password(), time() + (86400 * 30), "/");
                } else {
                    $_SESSION['id'] = $user->id();
                    $_SESSION['username'] = $user->username();
                    $_SESSION['email'] = $user->email();
                    $_SESSION['password'] = $user->password();
                }
                $this->isAuth();
                return true;
            }
            return false;
        }

        public function disconnect(){
            if (isset($_SESSION['id']) AND !empty($_SESSION['id']) AND isset($_SESSION['username']) AND !empty($_SESSION['username']) AND isset($_SESSION['email']) AND !empty($_SESSION['email']) AND isset($_SESSION['password']) AND !empty($_SESSION['password'])) {
                $_SESSION['id'] = '';
                $_SESSION['username'] = '';
                $_SESSION['email'] = '';
                $_SESSION['password'] = '';
                if(isset($_SESSION['role']) && !empty($_SESSION['role'])){
                    $_SESSION['role'] = '';
                }
                session_regenerate_id();
                return true;
            }
        
            if (isset($_COOKIE['id']) AND !empty($_COOKIE['id']) AND isset($_COOKIE['username']) AND !empty($_COOKIE['username']) AND isset($_COOKIE['email']) AND !empty($_COOKIE['email']) AND isset($_COOKIE['password']) AND !empty($_COOKIE['password'])) {
                setcookie('id', '', time() - (86400 * 30), "/");
                setcookie('username', '', time() - (86400 * 30), "/");
                setcookie('email', '', time() - (86400 * 30), "/");
                setcookie('password', '', time() - (86400 * 30), "/");
                if(isset($_SESSION['role']) && !empty($_SESSION['role'])){
                    $_SESSION['role'] = '';
                }
                return true;
            }
            return false;
        }

        public function updateToken(){
            $this->_userHandler = new UserHandler;
            if($this->isAuth()){
                $token = $_SESSION['token'];
                $email = htmlspecialchars($_SESSION['email'], ENT_QUOTES);
                $this->_userHandler->updateUser(array("token" => $token), array("email" => $email));
                return $token;
            } else {
                $token = bin2hex(openssl_random_pseudo_bytes(16));
                $_SESSION['token'] = $token;
                return $token;
            }
            return $token;
        }

        public function getToken(){
            return htmlspecialchars($_SESSION['token'], ENT_QUOTES);
        }

        public function isAdmin(){
            if(isset($_SESSION['role']) && !empty($_SESSION['role'])){
                if($_SESSION['role'] == 'admin'){
                    return true;
                }
            }
            return false;
        }
    }
?>