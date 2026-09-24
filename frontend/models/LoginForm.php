<?php

namespace frontend\models;

use common\models\LoginForm as BaseLoginForm;

/**
 * Login form del frontend, con captcha ademas de usuario/contrasena.
 */
class LoginForm extends BaseLoginForm
{
    public $verifyCode;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['verifyCode', 'captcha'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Código de verificación',
        ];
    }
}
