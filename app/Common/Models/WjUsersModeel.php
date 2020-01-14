<?php
namespace App\Common\Models;
use mix\facades\RDB;

class WjUsersModeel
{
    const TABLE = 'wj_users';

    /***
     * 通过id获取某一条数据
     * @param $id
     * @return mixed
     */
    public function getRowById($id)

    {
        $sql = "SELECT * FROM `" . self::TABLE . "` WHERE id = :id";
        $row = RDB::createCommand($sql)->bindParams([
            'id' => $id,
        ])->queryOne();
        return $row;
    }
}
