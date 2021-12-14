<?php


namespace App\Models\Repositories;


use App\Models\Application;

class ApplicationRepository
{

    public function insert(array $params)
    {
        $applicationObj = Application::query()->firstOrNew(
            ['app_id' => $params['app_id']],
            ['name' => $params['name'], 'description' => $params['description']]
        );
        if (!empty($applicationObj->getAttribute('id'))) {
            $applicationObj->setAttribute('status', 'updated');
        } else {
            $applicationObj->save();
            $applicationObj->setAttribute('status', 'inserted');
        }
        return $applicationObj;
    }

    public function getByAppId($app_id)
    {
        return Application::query()->where('app_id', $app_id)->first();
    }
}
