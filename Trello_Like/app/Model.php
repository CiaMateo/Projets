<?php
declare(strict_types=1);
abstract class Model implements \JsonSerializable{

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}