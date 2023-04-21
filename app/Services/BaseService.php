<?php

namespace App\Services;

use Error;
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use PDOException;

class BaseService
{
    protected $model;
    protected $tableName;
    protected $defaultSortKey;
    protected $searchableColumns;

    public $modelResource;
    public $requestValidator;

    public function __construct(Model $model)
    {
        // pass the model
        $this->model = $model;

        // get the fillable columns to use for search function
        $this->searchableColumns = $this->model->getFillable();
        
        // set primary key as default sort key
        $this->defaultSortKey = $this->model->primaryKey;
        
        // set table name from model
        $this->tableName = $this->model->tableName;
    }

    /**
     * get record from the table using
     * the incremental id 
     * @return Model $model instance
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * return all the record from the table 
     * no pagination
     * @return Array json
     */
    public function all()
    {
        $result = $this->model->all();

        if ($this->modelResource) {
            return $this->modelResource::collection($result);
        }

        return $result;
    }

    /**
     * return records with pagination
     * @param int pageLength 
     * @param string $lookup
     * @param string $orderBy
     * @return Array Json
     */
    public function allWithPagination($initialQuery = null, $pageLength = 5, $lookUp = "", $orderBy = [])
    {
        $orderResult = $initialQuery ?? $this->model;

        /**
         * with search 
         */
        if ($lookUp != "") {
            foreach ($this->searchableColumns as $column) {
                if (!isset($queryResult)) {
                    $queryResult = $orderResult->where($this->tableName[0] . '.' . $column, 'LIKE', '%' . $lookUp . '%');
                } else {
                    $queryResult = $queryResult->orWhere($this->tableName[0] . '.' . $column, 'LIKE', '%' . $lookUp . '%');
                }
            }

            // sort
            if (count($orderBy) > 0) {
                $orderBy[0] = ((in_array($orderBy[0], $this->searchableColumns))) ? $orderBy[0] : $this->defaultSortKey[0];
                $orderBy[1] = ((in_array($orderBy[1], ['asc', 'desc']))) ? $orderBy[1] : 'asc';
                $queryResult = $queryResult->orderBy($orderBy[0], $orderBy[1]);
            }

            // pagination
            if ($pageLength) {
                $queryResult = $queryResult->paginate($pageLength);
            } else {
                $queryResult = $queryResult->get();
            }

            // resource
            if ($this->modelResource) {
                return $this->modelResource::collection($queryResult);
            }

            return $queryResult;
        } else {
            $queryResult = $orderResult;
        }

        /**
         * sort by single column
         */
        if (count($orderBy) > 0) {
            $orderBy[0] = ((in_array($orderBy[0], $this->searchableColumns))) ? $orderBy[0] : $this->defaultSortKey[0];
            $orderBy[1] = ((in_array($orderBy[1], ['asc', 'desc']))) ? $orderBy[1] : 'asc';
            $orderResult = $this->model->orderBy($orderBy[0], $orderBy[1]);
        } else {
            $orderResult = $queryResult ?? $orderResult;
        }

        /**
         * check if request 
         * with pagination
         */
        if ($pageLength) {
            $result = $orderResult->paginate($pageLength);
        } else {
            $result = $orderResult->get();
        }

        /**
         * return data
         */
        if ($this->modelResource) {
            return $this->modelResource::collection($result);
        }

        return $result;
    }


    /**
     * store data to table
     * @param Request $payload
     * @param int $id
     * @return json
     */
    public function store($request, $id = null)
    {
        try {
            $this->model = new $this->model;

            if ($id !== null) {
                $this->model = $this->find($id);
            }
    
            $columns = $this->model->getFillable();
    
            foreach ($columns as $column) {
                $this->model[$column] = $request[$column] ?? $this->model[$column];
            }
    
            $this->model->save();
        } catch (Error $er) {
            return [
                'success' => false,
                'message' => $er
            ];
        } catch (ErrorException $ex) {
            return [
                'success' => false,
                'message' => $ex
            ];
        } catch (QueryException $qex) {
            return [
                'success' => false,
                'message' => $qex
            ];
        } catch (PDOException $pex) {
            return [
                'success' => false,
                'message' => $pex
            ];
        }

        return [
            'success' => true,
            'data' => $this->model
        ];
    }

    /**
     * remove single record
     */
    public function remove(int $id)
    {
        try {
            $record = $this->model->find($id);
            $record->delete();
        } catch (Error $er) {
            return [
                'success' => false,
                'message' => $er
            ];
        } catch (ErrorException $ex) {
            return [
                'success' => false,
                'message' => $ex
            ];
        } catch (QueryException $qex) {
            return [
                'success' => false,
                'message' => $qex
            ];
        } catch (PDOException $pex) {
            return [
                'success' => false,
                'message' => $pex
            ];
        }

        return [
            'success' => true,
            'message' => null
        ];
    }
}
