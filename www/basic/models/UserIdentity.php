<?php

namespace app\models;

use app\adapters\FileAdapter;


class UserIdentity extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }


    public $accessToken;

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $data = FileAdapter::findRow('"id":"' . $id);
        $data = array_pop($data);
        if (!empty($data)) {
            $data = json_decode($data,true);
        }
        return is_array($data) ? new static($data) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $data = FileAdapter::findRow('"accessToken":"' . $token);
        $data = array_pop($data);
        if (!empty($data)) {
            $data = json_decode($data,true);
        }
        return is_array($data) ? new static($data) : null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $data = FileAdapter::findRow('"username":"' . $username);
        $data = array_pop($data);
        if (!empty($data)) {
            $data = json_decode($data,true);
        }
        return is_array($data) ? new static($data) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
}