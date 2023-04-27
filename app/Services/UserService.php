<?php

namespace App\Services;

use App\Models\User;
use App\Services\BaseService;

class UserService extends BaseService
{
    public function __construct(User $model)
    {
        // pass the model to the parent constructor
        parent::__construct($model);

        // column use for sorting
        $this->defaultSortKey = ["id"];

        // column use for sorting
        $this->tableName = ["users"];

        // model resource for response formatting
        $this->modelResource = "App\Http\Resources\UserResource";
    }
}
