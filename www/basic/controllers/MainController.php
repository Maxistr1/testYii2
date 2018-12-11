<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\adapters\FileAdapter;

class MainController extends AbstractBaseController
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
//        var_dump('s' . FileAdapter::getStatusFile());
//        var_dump('s' . FileAdapter::clearFile());
//        FileAdapter::createFile();
//        FileAdapter::createRow("string4");
//        var_dump(FileAdapter::findRow(3));
//        var_dump(FileAdapter::deleteRow('tring'));
//        var_dump(FileAdapter::updateRow('string1','2'));
//        var_dump(get_class_vars(UserDTO::class));
        return $this->render('index.php');
    }

    // Очищает файл пользователей и устанавливает дефолтных
    public function actionSetdata()
    {
        FileAdapter::clearFile();
        FileAdapter::createRow(json_encode([
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
            'userGroup' => 1,
        ]));
        FileAdapter::createRow(json_encode([
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
            'userGroup' => 2,
        ]));
        FileAdapter::createRow(json_encode([
            'id' => '102',
            'username' => 'test',
            'password' => 'test',
            'authKey' => 'test102key',
            'accessToken' => '102-token',
            'userGroup' => 2,
        ]));
        return $this->render('setdata');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
