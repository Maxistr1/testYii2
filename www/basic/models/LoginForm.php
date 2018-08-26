<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property UserIdentity|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    const PERIOD_TIME = 5*60;
    const COUNT_ATTEMPT = 3;

    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        $session = Yii::$app->session;
        $time = $session->get('time');

        $period = (time() - $time);
        if ($period > self::PERIOD_TIME)
        {
            $session->destroy();
        }

        $period = self::PERIOD_TIME - $period;
        $count = $session->get('count');

        if (empty($count) && $count!== 0) {
            $session->set('count', self::COUNT_ATTEMPT - 1);
            $session->set('time', time());
        }

        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                if (!empty($count)) {
                    $session->set('count', $count - 1);
                }
                if($count !== 0)
                {
                    $this->addError($attribute, 'Incorrect username or password.');
                }
            }
        }

        if($count === 0)
        {
            $this->addError($attribute, 'Incorrect username or password. Try after '
                . $period . ' sec');
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return UserIdentity|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = UserIdentity::findByUsername($this->username);
        }

        return $this->_user;
    }
}
