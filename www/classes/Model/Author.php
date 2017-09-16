<?php
namespace Model;

class Author {

    private $_db;

    public function __construct($db) {

        $this->_db = $db;
    }

    public function getAutor($id) {
        $id = (int) $id;
        $query = $this->_db->prepare("SELECT * FROM author WHERE id = :id LIMIT 1");
        $query->execute(['id' => $id]);
        return $query->fetch();
    }

    public function deleteAuthor($id) {
        $id = (int) $id;
        $query = $this->_db->prepare("DELETE FROM author WHERE id = :id LIMIT 1");
        $query->execute(['id' => $id]);
        return $query->rowCount();
    }

    public function updateAuthor ($id, $data) {

        $query = $this->_db->prepare("
              UPDATE author SET
               name = :name,
               nameAblative = :nameAblative,
               avatar = :avatar,
               width = :width,
               height = :height
              WHERE id = :id
            ");
        $query->execute(array_merge($data, ['id' => $id]));

        return $query->rowCount();
    }

    public function addAuthor($data) {

        $query = $this->_db->prepare("
              INSERT INTO author
                (name, nameAblative, avatar, width, height)
              VALUES ( :name, :nameAblative, :avatar, :width, :height)
            ");

        if ($query->execute($data)) {
            return $this->_db->lastInsertId();
        }
        return false;
    }
}