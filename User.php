<?php
/**
 * Автор: Дмитрий Савчик
 *
 * Дата реализации: 28.09.2022 12:00
 *
 * Дата изменения: 29.09.2022 15:00
 *
 * Утилита для работы с базой данных
 */

/**
 * Class User
 * Класс для работы с пользователем
 *
 * Сохранение полей экземпляра класса в БД;
 * Удаление человека из БД в соответствии с id объекта;
 * static преобразование даты рождения в возраст (полных лет);
 * static преобразование пола из двоичной системы в текстовую (муж,
жен);
 * Конструктор класса либо создает человека в БД с заданной
информацией, либо берет информацию из БД по id (предусмотреть
валидацию данных);
 * Форматирование человека с преобразованием возраста и (или) пола
(п.3 и п.4) в зависимотси от параметров (возвращает новый экземпляр
StdClass со всеми полями изначального класса).
 */

class User
{
    protected $id;
    protected $firstname;
    protected $lastname;
    protected $birthdate;
    protected $gender;
    protected $city;

    public static $connect;

    public function __construct($id, $firstname, $lastname, $birthdate, $gender, $city)
    {
        try {
            static::$connect = new PDO("mysql:host=localhost", "root", "");
            static::$connect->exec('SET NAMES UTF8');
            static::$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $errors) {
            echo "Database error: " . $errors->getMessage();
        }
        if (isset($firstname, $lastname, $birthdate, $gender, $city)
            && preg_match('/^[a-zA-Z]+$/', $firstname)
            && preg_match('/^[a-zA-Z]+$/', $lastname)) {
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->birthdate = $birthdate;
            $this->gender = $gender;
            $this->city = $city;
        } else {
            $user = static::$connect->query("SELECT `id`, `firstname`, `lastname`, `birthdate`, `gender`, `city` 
FROM `UserTest` WHERE `id` = $id");
            $this->id = $user['id'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
            $this->birthdate = $user['birthdate'];
            $this->gender = $user['gender'];
            $this->city = $user['city'];
        }
    }


    public function setDataBase($id, $firstname, $lastname, $birthdate, $gender, $city)
    {
        $stmt = static::$connect->prepare(
            "INSERT INTO `UserTest` (`id`, `firstname`, `lastname`, `birthdate`, `gender`, `city`) 
VALUES (:id, :firstname, :lastname, :birthdate, :gender, :city)");
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":firstname", $firstname);
        $stmt->bindParam(":lastname", $lastname);
        $stmt->bindParam(":birthdate", $birthdate);
        $stmt->bindParam(":gender", $gender);
        $stmt->bindParam(":city", $city);
        $stmt->execute();
    }

    public function deleteUser($id)
    {
        static::$connect->query("DELETE FROM `users` WHERE `id`=$this->id");
    }

    static function getAge($birthdate)
    {
        $birthday_timestamp = strtotime($birthdate);
        $age = date('Y') - date('Y', $birthday_timestamp);
        if (date('md', $birthday_timestamp) > date('md')) {
            $age--;
        }
        return $age;
    }

    static function getGender($gender)
    {
        return $gender == 0 ? 'муж' : 'жен';
    }

    public function formatUser($id)
    {
        $stdClass = new stdClass();
        $stdClass->id = $this->id;
        $stdClass->firstname = $this->firstname;
        $stdClass->lastNnme = $this->lastname;
        $stdClass->birthdate = static::getAge($this->birthdate);
        $stdClass->gender = static::getAge($this->gender);
        $stdClass->city = $this->city;

        return $stdClass;
    }

}

?>