<?php

class Login {

    public static function register($username, $password, $yubikey) {
        if (Login::userExists($username) == false) {
            $hash = password_hash($password, PASSWORD_BCRYPT, Login::generateHashCost());
            include __DIR__ . '/config.php';
            $con = mysqli_connect($conf['mysql-url'], $conf['mysql-user'], $conf['mysql-password'], $conf['login-db']) or die("Connection problem.");
            $query = $con->prepare("INSERT INTO `" . $conf['login-table'] . "` (`username`, `password`, `yubikey`) VALUES (?, ?, ?);");
            $query->bind_param("sss", $username, $hash, $yubikey);
            $query->execute();
            return 1;
        }
        return 0;
    }

    public static function getPassword($username, $password) {
        include __DIR__ . '/config.php';
        $con = mysqli_connect($conf['mysql-url'], $conf['mysql-user'], $conf['mysql-password'], $conf['login-db']) or die("Connection problem.");
        $query = $con->prepare("SELECT * FROM `" . $conf['login-table'] . "` WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $query->bind_result($dbuser, $dbpassword, $dbyubikey);
        if ($query->fetch()) {
            if (password_verify($password, $dbpassword)) {
                return true;
            }
        }
        return false;
    }

    public static function verifyYubikey($username, $otp) {
        include __DIR__ . '/config.php';
        $con = mysqli_connect($conf['mysql-url'], $conf['mysql-user'], $conf['mysql-password'], $conf['login-db']) or die("Connection problem.");
        $query = $con->prepare("SELECT `yubikey` FROM `" . $conf['login-table'] . "` WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $query->bind_result($dbyubikey);
        if ($query->fetch()) {
            if (substr($otp, 0, 12) == $dbyubikey) {
                return true;
            }
        }
        return false;
    }

    public static function doLogin($username, $password) {
        if (Login::userExists($username)) {
            if (Login::getPassword($username, $password)) {
                return true;
            }
        }
        return false;
    }

    public static function userExists($username) {
        include __DIR__ . '/config.php';
        $con = mysqli_connect($conf['mysql-url'], $conf['mysql-user'], $conf['mysql-password'], $conf['login-db']) or die("Connection problem.");
        $query = $con->prepare("SELECT COUNT(*) AS num FROM `" . $conf['login-table'] . "` WHERE `username` = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            foreach ($row as $r) {
                if ($r > 0) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function generateHashCost() {
        $timeTarget = 0.05;
        $cost = 8;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);
        return $cost;
    }

    public static function updatePassword($username, $oldpassword, $password) {
        if (Login::getPassword($username, $oldpassword)) {
            $hash = password_hash($password, PASSWORD_BCRYPT, Login::generateHashCost($password));
            include __DIR__ . '/config.php';
            $con = mysqli_connect($conf['mysql-url'], $conf['mysql-user'], $conf['mysql-password'], $conf['login-db']) or die("Connection problem.");
            $query = $con->prepare("UPDATE `" . $conf['login-table'] . "` SET `password`=? WHERE `username`=?;");
            $query->bind_param("ss", $hash, $username);
            $query->execute();
            return true;
        }
        return false;
    }

}

class Radius_db {

    public static function getUser($username) {
        
    }

    public static function getAllUsers() {
        include __DIR__ . '/config.php';
        $con = mysqli_connect($conf['mysql-url'], $conf['mysql-user'], $conf['mysql-password'], $conf['radius-db']) or die("Connection problem.");
        $query = $con->prepare("SELECT `id`, `username` FROM `" . $conf['radius-table'] . "`;");
        $query->execute();
        $result = $query->get_result();
        $output = array();
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $output[] = $row;
        }
        return $output;
    }

    public static function createUser($username, $password, $time = NULL) {
        include __DIR__ . '/config.php';
        if ($time != NULL) {
            $time = date('Y-m-d H:i:s', $time);
        }
        $con = mysqli_connect($conf['mysql-url'], $conf['mysql-user'], $conf['mysql-password'], $conf['radius-db']) or die("Connection problem.");
        $query = $con->prepare("INSERT INTO `" . $conf['radius-table'] . "` (`username`, `attribute`, `op`, `value`, `expiry`) VALUES (?, 'Cleartext-Password', ':=', ?, ?);");
        if ($time != NULL) {
        $query->bind_param("sss", $username, $password, $time);
        }else{
          $query->bind_param("ssb", $username, $password, $time);  
        }
        $query->execute();
        return true;
    }

}

class load {

    public static function loadContent($filename) {
        $index_php = array("header.php", "session.php");
        if ($filename == "index.php") {
            foreach ($index_php as $file) {
                include __DIR__ . $file;
            }
        }
    }

}
