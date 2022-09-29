<?php
/**
 * Автор: Дмитрий Савчик
 *
 * Дата реализации: 28.09.2022 12:00
 *
 * Дата изменения: 29.09.2022 15:00
 *
 * Утилита для работы со списками базы данных
 */
/**
 * Class User
 * Класс для работы со списками пользователей
 *
 * Конструктор ведет поиск id людей по всем полям БД (поддержка
выражений больше, меньше, не равно);
 * Получение массива экземпляров класса 1 из массива с id людей
полученного в конструкторе;
 * Удаление людей из БД с помощью экземпляров класса 1 в
соответствии с массивом, полученным в конструкторе.
 */
include_once 'User.php';
if (!class_exists('User')) {
    echo ('Didn\'t find User class!');
}
class UserList extends User {
    public $usersList = [];

    public function __construct($usersList)
    {
        if (class_exists('User')) {
            $db = static::$connect;

            foreach ($usersList as $user) {
                $userId = $this->findUser($user, $db);

                if ($userId) {
                    $this->usersList[] = $userId['id'];
                }
            }
        }
    }

     public function getUsers()
    {
        $usersList = [];

        foreach ($this->usersList as $user) {
            $usersId[] = new User($user);
        }

        return $usersList;
    }

    public function deleteUsers()
    {
        foreach ($this->usersList as $user) {
            $user = new User($user['id']);

            $user->deleteUser();
        }
    }

    private function findUser($user, $db)
    {
        $args = [];
        $keys = [];

        $sql = 'SELECT `id` FROM `UserTest` WHERE ';

        foreach ($user as $key => $value) {
            $key_arr = explode(' ', $value);
            switch ($key_arr[0]) {
                case '=':
                    $args[] = "`$key` = :$key";
                    break;
                case '>':
                    $args[] = "`$key` > :$key";
                    break;
                case '<':
                    $args[] = "`$key` < :$key";
                    break;
                case '!=':
                    $args[] = "`$key` != :$key";
                    break;
            }

            $keys[$key] = $key_arr[1];
        }

        $sql .= implode(' AND ', $args) . ' LIMIT 1';

        $stmt = $db->prepare($sql);
        $stmt->execute($keys);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>