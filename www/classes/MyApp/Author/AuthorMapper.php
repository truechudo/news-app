<?php
namespace MyApp\Author;

/**
 * Маппер для объекта Author
 *
 * @package Author
 */
class AuthorMapper
{
    /**
     * Подключение к базе mysql
     *
     * @var PDO
     */
    protected $db;

    /**
     * Создание маппера для автора
     *
     * @param PDO $db подключение в БД
     */
    public function __construct($db)
    {
        $this->_db = $db;
    }

    /**
     * Поиск автора по ID
     *
     * @param int $id ID автора
     * @return Author объект автора
     */
    public function getAuthor($id)
    {
        $id = (int) $id;
        if (!$id) {
            return null;
        }
        $query = $this->_db->prepare('SELECT * FROM author WHERE id = :id LIMIT 1');
        $query->execute(['id' => $id]);
        $author = $query->fetch();
        if (!empty($author['avatar'])) {
            $author['avatar'] = json_decode($author['avatar'], true);
        } else {
            $author['avatar'] = [];
        }
        return Author::createFromArray($author);
    }

    /**
     * Удаление автора по ID
     *
     * @param int $id ID автора
     * @return bool true - автор удален, false - автора не существовало
     */
    public function deleteAuthor($id)
    {
        $id = (int) $id;
        if (!$id) {
            return false;
        }
        $query = $this->_db->prepare('DELETE FROM author WHERE id = :id LIMIT 1');
        $query->execute(['id' => $id]);
        return $query->rowCount() ? true : false;
    }

    /**
     * Обновление/добавление автора
     *
     * @param Author $author объект автора
     * @return bool|int id автора, если данные сохранились, false - не сохранились
     */
    public function saveAuthor (Author $author)
    {
        if ($author->id) {

            $query = $this->_db->prepare('
                  UPDATE author SET
                   name = :name,
                   nameAblative = :nameAblative,
                   avatar = :avatar
                  WHERE id = :id
            ');
            $query->bindParam("id", $author->id);
            $query->bindParam("name", $author->name);
            $query->bindParam("nameAblative", $author->nameAblative);
            $query->bindParam("avatar", json_encode($author->nameAblative));
            $query->execute();
            return $author->id;
        }

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

        $query->bindParam("name", $author->name);
        $query->bindParam("nameAblative", $author->nameAblative);
        $query->bindParam("avatar", json_encode($author->avatar));

        if ($query->execute()) {
            return $this->_db->lastInsertId();
        }
        return false;
    }
}