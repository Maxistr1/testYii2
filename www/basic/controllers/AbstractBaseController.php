<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;


abstract class AbstractBaseController extends Controller
{
    const OPEN_ACL = 'open';
    const CLOSE_ACL = 'close';
    const ADMIN_ROLE = 'admin';

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        // Определяем группу пользователя
        $userGroup = $this->getUserGroup();
        Yii::$app->view->params['userGroup'] = $userGroup;
        $permision = $this->checkPermision($userGroup, $action);

        if ($permision) {
            return parent::beforeAction($action);
        } else {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getUserGroup()
    {
        $identity = Yii::$app->getUser()->getIdentity();

        if ($identity) {
            $userGroup = Yii::$app->getUser()->getIdentity()->getUserGroup();
            $allRoles = Yii::$app->params['roles'];

            if (isset($allRoles[$userGroup])) {
                $userGroup = $allRoles[$userGroup];
            } else {
                throw new \Exception('Undefined user_group for user');
            }

        } else {
            $userGroup = "guest";
        }

        return $userGroup;
    }

    private function checkPermision($userGroup, $action)
    {
        $allAcl = Yii::$app->params['acl'];

        if (!isset($allAcl[$userGroup])) {
            throw new \Exception("Don`t isset acl data for {$userGroup}!");
        }

        $result = true;

        $userAcl = $allAcl[$userGroup];

        if (!isset($userAcl['groupAclType']) || !isset($userAcl['resources']) || !is_array($userAcl['resources'])) {
            throw new \Exception("Wrong acl format for {$userGroup}!");
        }

        // Все записи приводим в строчный вид
        $userAcl = $this->getPreparedResources($userAcl);

        $method = strtolower($action->actionMethod);
        $controller = strtolower($action->controller->id . "Controller");
        $issetController = isset($userAcl['resources'][$controller]);
        if ($issetController) {
            $issetMethod = array_search($method, $userAcl['resources'][$controller]) !== false;
        }

        if ($userAcl['groupAclType'] == self::OPEN_ACL && $issetController && $issetMethod) {
            $result = false;
        }

        if ($userAcl['groupAclType'] == self::CLOSE_ACL && !($issetController && $issetMethod)) {
            $result = false;
        }

        return $result;
    }

    private function getPreparedResources($userAcl)
    {
        $preparedUserAcl = [];

        foreach ($userAcl['resources'] as $key => $value) {
            $tempValue = [];

            foreach ($value as $val) {
                $tempValue[] = strtolower($val);
            }

            $preparedUserAcl[strtolower($key)] = $tempValue;
        }

        $userAcl['resources'] = $preparedUserAcl;

        return $userAcl;
    }

}