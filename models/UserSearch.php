<?php
/**
 * Project: yii2-mongodb-user
 * File: UserSearch.php
 * User: andrew
 * Date: 30.12.16
 * Time: 15:23
 */

namespace andrew72ru\user\models;


use andrew72ru\user\models\User;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{
    public function rules()
    {
        return [
            [['username', 'email'], 'string']
        ];
    }

    public function search($params = [])
    {
        $query = User::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $dataProvider;
    }
}
