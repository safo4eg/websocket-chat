<?php

class Hasher {
    private static $serverKey = 'xZzNG6a78fGnDOc0mJECweoVJYbedvSN';
    private static $algo = 'sha256';

    public static function checkUserHash($dataBaseHash, $userHash) {
        $serverHash = self::hashForServer();
        $verifiableHash = hash(self::$algo, $serverHash.$userHash); // verifiable(проверяемый)
        if($verifiableHash === $dataBaseHash) return true;
        else return false;
    }

    public static function outputHashes() {
        $userHash = self::hashForUser();
        $serverHash = self::hashForServer();
        $dataBaseHash = hash(self::$algo, $serverHash.$userHash);
        return ['userHash' => $userHash, 'dataBaseHash' => $dataBaseHash];
    }

    private static function hashForServer() {
        return hash(self::$algo, self::$serverKey);
    }

    private static function hashForUser() {
        $randomStr = self::randomString();
        return hash(self::$algo, $randomStr);
    }

    private static function randomString($strLength = 32) {
        $permittingChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; // разрешенные символы в строке
        $permittingCharsLength = strlen($permittingChars);
        $randomString = '';
        for($i = 0; $i < $strLength; $i++) {
            $randomChar = $permittingChars[mt_rand(0, $permittingCharsLength - 1)];
            $randomString .= $randomChar;
        }
        return $randomString;
    }

}