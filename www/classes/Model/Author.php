<?php
namespace Model;

class Author {

    private $_db;

    public function __construct($db) {

        $this->_db = $db;
    }

    public function getAutor($id) {
        $id = (int) $id;
        if (!$id) {
            return false;
        }
        $query = $this->_db->prepare('SELECT * FROM author WHERE id = :id LIMIT 1');
        $query->execute(['id' => $id]);
        $author = $query->fetch();
        if (!empty($author['avatar'])) {
            $author['avatar'] = json_decode($author['avatar']);
        } else {
            $author['avatar'] = [];
        }

        return $author;
    }

    public function deleteAuthor($id) {
        $id = (int) $id;
        if (!$id) {
            return false;
        }
        $query = $this->_db->prepare('DELETE FROM author WHERE id = :id LIMIT 1');
        $query->execute(['id' => $id]);
        return $query->rowCount();
    }

    public function updateAuthor ($id, $data) {

        $id = (int) $id;
        if (!$id) {
            return false;
        }

        $author = $this->getAutor($id);

        if (empty($author)) {
            return false;
        }

        $updateData = [
            'name' => $data['name'] ?? $author['name'],
            'nameAblative' => $data['nameAblative'] ?? $author['nameAblative'],
            'avatar' => isset($data['avatar']) ? json_encode($data['avatar']) : $author['avatar']
        ];

        $query = $this->_db->prepare('
              UPDATE author SET
               name = :name,
               nameAblative = :nameAblative,
               avatar = :avatar
              WHERE id = :id
            ');
        $query->execute(array_merge($updateData, ['id' => $id]));

        return true;
    }

    public function addAuthor($data) {


        if (isset($data['avatar'])) {
            $data['avatar'] = json_encode($data['avatar']);
        } else {
            $data['avatar'] = json_encode([]);
        }

        $query = $this->_db->prepare("
              INSERT INTO author
                (name, nameAblative, avatar)
              VALUES ( :name, :nameAblative, :avatar)
            ");

        if ($query->execute($data)) {
            return $this->_db->lastInsertId();
        }
        return false;
    }
}