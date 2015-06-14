<?php
class DB{
    private $_db = null;
    public function __construct(){
        $dsn = 'mysql:dbname=q2a;host=127.0.0.1';
        $user = 'root';
        $password = '()!@#!**';
        try {
            $this->_db = new PDO($dsn, $user, $password);
            echo 'yes';
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public function exec($sql){
        $this->_db->exec($sql);
    }

    public function query($sql){
        $res = $this->_db->query($sql, PDO::FETCH_ASSOC);
        $data = array();
        foreach ($res as $value) {
            $data[] = $value;
        }
        return $data;
    }
}

