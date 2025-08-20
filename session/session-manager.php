<?php
class SessionManager {
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($login_path, $user_login_id,  $password) {
        self::startSession();
        $_SESSION['client'] = $login_path;
        require("../login/login.php");


    }

    public static function logout() {
        self::startSession();
        if (isset($_SESSION['client'])) {
            $login_path = $_SESSION['client'];
            $login_path_vrsn = $_SESSION['client_login'];
            $path="";
             if(strtolower($login_path)!=strtolower("ISTL"))
            {
                $path=BASE_PATH.$login_path."/"; 
            }      
            session_unset();
            session_destroy();
            echo "<script>
            localStorage.removeItem('client_type');
            window.location.href = '$path'+'login.php';
            </script>";
            exit();
        }
        else
        {
            echo "<script>           
            window.location.href = 'login.php';
            </script>";
            exit();
        }
    }

    public static function SessionVariables() {
        self::startSession();

        return [
            'mobile_no' => $_SESSION['mobile_no'],
            'user_id' => $_SESSION['login_user_id'],
            'user_name' => $_SESSION['user_name'],
            'user_email' => $_SESSION['user_email'],
            'role' => $_SESSION['role'],
            'user_type' => $_SESSION['user_type'],
            'client' => $_SESSION['client'],
            'status' => $_SESSION['status'],
            'client_login' => $_SESSION['client_login'],
            'user_login_id' => $_SESSION['user_login_id']
        ];


    }
    public static function checkSession() {
        self::startSession();
        if (!isset($_SESSION['client'])) {
            echo "<script>
            var clientType = localStorage.getItem('client_type');
            if(clientType!='0')
            {
                if (clientType) 
                {
                    window.location.href = '" . BASE_PATH . "' + clientType + '/login.php';
                } 
                else 
                {
                    window.location.href = 'login.php';
                }
            }
            else
            {
                window.location.href = 'login.php';
            }
            </script>";
            exit();

        }
    }
}
?>
