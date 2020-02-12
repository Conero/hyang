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
    // 生成唯一id键
    // @todo 出现生成id重复的问题，不可用于生成环境
    public function testGetId()
    {
        print_r([
            Rand::getId(),
            Rand::getId(),
            Rand::getId(),
            Rand::getId()
        ]);
    }

    public function testCnPhone(){
        // Rand::cnPhone
        print_r([
            Rand::cnPhone(),
            Rand::cnPhone(),
            Rand::cnPhone(),
            Rand::cnPhone()
        ]);

        // Rand::numberStr
        print_r([
            Rand::numberStr(),
            Rand::numberStr(4),
            Rand::numberStr(8),
            Rand::numberStr(10),
            Rand::numberStr(20)
        ]);

        print_r([
            Rand::numberStr(),
            Rand::numberStr(4),
            Rand::numberStr(8),
            Rand::numberStr(10),
            Rand::numberStr(20)
        ]);


    }

}
