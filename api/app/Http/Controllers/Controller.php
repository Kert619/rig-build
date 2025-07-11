<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class Controller
{

    protected string $modelClass;
    protected Model $model;
    protected readonly string $primarykey;

    public function __construct()
    {
        $this->model = new $this->modelClass;
        $this->primarykey = $this->model->getKeyName();
    }

    protected abstract function columns(): array;

    protected function with(): array
    {
        return [];
    }

    protected function where(): array
    {
        return [];
    }

    protected function rules(): array
    {
        return [];
    }

    protected function updateRules(): array
    {
        return $this->rules();
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->model::select($this->columns())->with($this->with());

        foreach ($this->where() as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        return $query->get();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());

        $row = $this->model::create($validated);

        return $this->model::select($this->columns())
            ->with($this->with())
            ->where($this->primarykey, $row[$this->primarykey])
            ->firstOrFail();
    }

    public function update($id, Request $request): JsonResponse
    {
        $validated = $request->validate($this->updateRules());

        $row = $this->model::findOrFail($id);

        $row->update($validated);

        return $this->model::select($this->columns())
            ->with($this->with())
            ->where($this->primarykey, $row[$this->primarykey])
            ->firstOrFail();
    }

    public function show($id): JsonResponse
    {
        return $this->model::select($this->columns())
            ->with($this->with())
            ->where($this->primarykey, $id)
            ->firstOrFail();
    }

    public function destroy($id): JsonResponse
    {
        $row = $this->model::findOrFail($id);
        $row->delete();
        return response()->json(null, 204);
    }
}
