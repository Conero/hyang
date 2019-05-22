<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/9/17 0017 23:02
 * Email: brximl@163.com
 * Name:
 */

use hyang\Rand;

class RandTest extends \PHPUnit\Framework\TestCase
{

    public function testGetId()
    {
        echo Rand::getId();
    }
}
