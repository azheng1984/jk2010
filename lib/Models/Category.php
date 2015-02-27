<?php

class Category extends DbActiveRecord {
    public function getName() {
        return $this->getColumn('name');
    }

    public function setName($value) {
        if (DbClient::isConnected()) {
            $conn = DbClient::getConnection();//c2
        } else {
            $conn = null;
        }
        $shouldConnect = false;
        $conn = DbClient::getConnection($shouldConnect);//c2
        //DbClient::connect('c2');
        try {
            DbClient::find('xx');
        } finally {
            DbClient::setConnection($conn);
        }

        DbClient::setConnection();

        DbConnectionManager::connect('c2');

        {
            DbConnectionManager::connect('c2');
            DbConnectionManager::closeConnection();
            DbClient::find();
        }

        DbConnectionManager::closeConnection();

        DbClient::closeConnection('c2');
        return $this->setColumn('name', $value);
    }
}
