<?php

namespace app\adapters;


class FileAdapter
{
    const AUTH_FILE = '/application/www/basic/files/users.txt';

    /**
     * Возвращает true если файл существует
     * или false в иных случаях
     * @return bool
     */
    public static function getStatusFile()
    {
        return file_exists(self::AUTH_FILE);
    }

    /**
     * Очищает все данные о пользователях
     * @param null $condition
     * @param array $params
     * @return mixed
     */
    public static function clearFile()
    {
        $result = false;

        if (self::getStatusFile()) {
            fopen(self::AUTH_FILE, 'w');
            $result = true;
        }

        return $result;
    }

    /**
     * Создает файл, если файл существует то перезатирает его
     * @return bool
     */
    public static function createFile()
    {
        $response = false;
        $s = fopen(self::AUTH_FILE, 'w');
        if ($s) {
            $response = true;
        }
        return $response;
    }

    /**
     * Ищет заданную подстроку в файле и возвращает все записи с
     * данной подстрокой
     * @param string|null $condition
     * @return array
     * @throws \Exception
     */
    public static function find(string $condition = null)
    {
        $response = [];
        $file = file(self::AUTH_FILE);

        if (!is_array($file)) {
            throw new \Exception('Error write to file');
        }

        foreach ($file as $row) {
            if (strpos($row, $condition) || strpos($row, $condition) === 0) {
                $response[] = $row;
            }
        }
        return $response;
    }

    /**
     * Находит первую подходящую строку по условию и выдает ее
     * @param string|null $condition
     * @return array
     * @throws \Exception
     */
    public static function findRow(string $condition = null)
    {
        $response = [];
        $file = file(self::AUTH_FILE);

        if (!is_array($file)) {
            throw new \Exception('Error open file');
        }

        foreach ($file as $key => $row) {
            if (strpos($row, $condition) || strpos($row, $condition) === 0) {
                $response[$key] = $row;
                break;
            }
        }
        return $response;
    }

    /**
     * Удаление строки из файла по условию или номеру строки
     * @param string|null $condition
     * @param null $rowNumber
     * @return bool
     */
    public static function deleteRow(string $condition = null, $rowNumber = null)
    {
        $response = false;

        $file = file(self::AUTH_FILE);

        if (!is_array($file)) {
            throw new \Exception('Error open file');
        }

        if (!empty($condition)) {
            $row = self::findRow($condition);
            if (!empty($row)) {
                $deletedKey = key($row);
                unset($file[$deletedKey]);
                $response = true;
            }
        }

        if (!empty($rowNumber) && is_int($rowNumber) && count($file) >= $rowNumber) {
            unset($file[$rowNumber]);
            $response = true;
        }

        if ($response) {
            file_put_contents(self::AUTH_FILE, implode($file));
        }

        return $response;
    }

    /**
     * Обновление записанных данных в строке
     * @param string|null $data
     * @param null $condition
     * @param null $rowNumber
     * @return bool
     */
    public static function updateRow(string $data = null, $condition = null, $rowNumber = null)
    {
        $response = false;

        $file = file(self::AUTH_FILE);

        if (!is_array($file)) {
            throw new \Exception('Error open file');
        }

        if (!empty($data) && !empty($condition)) {
            $row = self::findRow($condition);
            if (!empty($row)) {
                $updateKey = key($row);

                $file[$updateKey] = $data . "\r\n";
                $response = true;
            }
        }

        if (!empty($data) && !empty($rowNumber) && is_int($rowNumber) && count($file) >= $rowNumber) {
            $file[$rowNumber] = $data . "\r\n";
            $response = true;
        }

        if ($response) {
            file_put_contents(self::AUTH_FILE, implode($file));
        }

        return $response;
    }

    /**
     * Создание новой записи с новой строки
     * @param string $data
     * @return bool|int
     */
    public static function createRow(string $data)
    {
        $result = file_put_contents(self::AUTH_FILE, $data . "\r\n", FILE_APPEND);
        if ($result) {
            $result = true;
        }

        return $result;
    }
}