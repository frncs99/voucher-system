<?php

namespace App\Services;

use App\Models\Group;
use App\Services\BaseService;

class GroupService extends BaseService
{
    public function __construct(Group $model)
    {
        // pass the model to the parent constructor
        parent::__construct($model);

        // column use for sorting
        $this->defaultSortKey = ["group_id"] ;

        // column use for sorting
        $this->tableName = ["groups"] ;

        // validator class for the controller
        // $this->requestValidator = new GroupStoreRequest();
        // $this->requestValidator = new GroupUpdateRequest();

        // model resource for response formatting
        // $this->modelResource = "App\Http\Resources\GroupResource";
    }
}
