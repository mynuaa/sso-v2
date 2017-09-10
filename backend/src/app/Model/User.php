<?php

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function byId($id) {
        return $this->getORM()
            ->select('id, username, stu_num, name, openid')
            ->where('id', $id)
            ->fetchOne();
    }
    public function byStuNum($stu_num) {
        return $this->getORM()
            ->select('id, username, stu_num, name, openid')
            ->where('stu_num', $stu_num)
            ->fetchOne();
    }
    public function listByStuNum($stu_num) {
        return $this->getORM()
            ->select('id, username, stu_num, name, openid')
            ->where('stu_num', $stu_num)
            ->fetchAll();
    }
    public function setRealname($stu_num, $name) {
        return $this->getORM()
            ->where('stu_num', $stu_num)
            ->update(['name' => $name]);
    }
    public function setStuNum($id, $stu_num) {
        return $this->getORM()
            ->where('id', $id)
            ->update(['stu_num' => $stu_num]);
    }
    public function addUser($user) {
        return $this->getORM()->insert($user);
    }
    public function bindUserCount($stu_num) {
        return $this->getORM()
            ->where('stu_num', $stu_num)
            ->count();
    }
}
