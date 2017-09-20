<?php
namespace MyApp\Author;

/**
 * Объект автора
 *
 * @package Author
 */
class Author
{
    /**
     * ID автора
     * @var int
     */
    public $id;

    /**
     * Имя автора
     * @var string
     */
    public $name;

    /**
     * Имя автора в творительном падеже
     * @var string
     */
    public $nameAblative;

    /**
     * Массив с данными о картинке автора в формате
     * [
     *  'fileName' => 'путь до картинки',
     *  'width' => 20,
     *  'рушпре' => 20
     * ]
     * @var array
     */
    public $avatar;

    /**
     * Создание объекта автора. При передаче неверных данных выкидывается исключение.
     *
     * @param int $id ID автора
     * @param string $name имя автора
     * @param string $nameAblative имя автора в творительном падеже
     * @param array $avatar массив с информацией о картинке автора в формате
     * [
     *  'fileName' => 'путь до картинки',
     *  'width' => 20,
     *  'рушпре' => 20
     * ]
     * @throws Exception    Переданные данные не валидны
     */
    public function __construct(int $id, string $name, string $nameAblative, array $avatar = [])
    {
        if (empty($name)) {
            throw new Exception('Имя автора не должно быть пустым');
        }

        if (empty($nameAblative)) {
            throw new Exception('Имя автора в творительном падеже не должно быть пустым');
        }

        if (!empty($avatar)) {
            if (empty($avatar['fileName'])) {
                throw new Exception('Путь до картинки автора не может быть пустым');
            }

            if (empty($avatar['width'])) {
                throw new Exception('Ширина картинки не задана');
            } elseif (!filter_var($avatar['width'], FILTER_VALIDATE_INT)) {
                throw new Exception('Ширина картинки должна быть целым числом');
            }

            if (empty($avatar['height'])) {
                throw new Exception('Высота картинки не задана');
            } elseif (!filter_var($avatar['height'], FILTER_VALIDATE_INT)) {
                throw new Exception('Высота картинки должна быть целым числом');
            }
        }

        $this->id = $id;
        $this->name = $name;
        $this->nameAblative = $nameAblative;
        $this->avatar = $avatar;
    }

    /**
     * Создает объект автора из переданного массива данных.
     * При передаче неверных данных выкидывается исключение.
     *
     * @param $data
     * @return Author
     * @throws Exception    Переданные данные не валидны
     */
    public function createFromArray($data) : Author
    {
        $author['id'] = $data['id'] ?? 0;
        $author['name'] = $data['name'] ?? '';
        $author['nameAblative'] = $data['nameAblative'] ?? '';
        $author['avatar'] = [];
        if (isset($data['avatar'])) {
            $author['avatar']['fileName'] = $data['avatar']['fileName'] ?? '';
            $author['avatar']['width'] = $data['avatar']['width'] ?? 0;
            $author['avatar']['height'] = $data['avatar']['height'] ?? 0;
        }
        return new self($author['id'], $author['name'], $author['nameAblative'], $author['avatar']);
    }

    /**
     * Возвращает массив с данными автора
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}