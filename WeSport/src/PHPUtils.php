<?php
class PHPUtils{
    public static function isPostSetNotEmpty(...$testValues)
    {
        foreach ($testValues as $testValue) {
            if(!isset($_POST[$testValue]) || empty($_POST[$testValue]))
                return false;
        }
        return true;
    }
}