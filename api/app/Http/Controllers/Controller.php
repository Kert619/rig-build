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
    protected string $optionColumn;

    public function __construct()
    {
        $this->model = new $this->modelClass;
        $this->primarykey = $this->model->getKeyName();
    }

    protected abstract function columns(): array;

    protected abstract function rules(): array;

    protected abstract function updateRules($id): array;

    protected function messages(): array
    {
        return [];
    }

    protected function fieldNames(): array
    {
        return [];
    }

    protected function with(): array
    {
        return [];
    }

    protected function orderBy(): array
    {
        return [$this->primarykey => 'desc'];
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->model::select($this->columns())->with($this->with());

        foreach ($request->all() as $field => $value) {
            if (!empty($value)) {
                $query->where($field, $value);
            }
        }

        foreach ($this->orderBy() as $field => $order) {
            $query->orderBy($field, $order);
        }

        return response()->json($query->get());
    }

    public function options()
    {
        if (!$this->optionColumn) return [];

        $query = $this->model::select(["$this->primarykey AS value", "$this->optionColumn AS label"]);
        return $query->get();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules(), $this->messages(), $this->fieldNames());

        $row = $this->model::create($validated);

        $created = $this->model::select($this->columns())
            ->with($this->with())
            ->where($this->primarykey, $row[$this->primarykey])
            ->firstOrFail();

        return response()->json($created);
    }

    public function update($id, Request $request): JsonResponse
    {
        $validated = $request->validate($this->updateRules($id), $this->messages(), $this->fieldNames());

        $row = $this->model::findOrFail($id);

        $row->update($validated);

        $updated =  $this->model::select($this->columns())
            ->with($this->with())
            ->where($this->primarykey, $row[$this->primarykey])
            ->firstOrFail();

        return response()->json($updated);
    }

    public function show($id): JsonResponse
    {
        $row = $this->model::select($this->columns())
            ->with($this->with())
            ->where($this->primarykey, $id)
            ->firstOrFail();

        return response()->json($row);
    }

    public function destroy($id): JsonResponse
    {
        $row = $this->model::findOrFail($id);
        $row->delete();
        return response()->json(null, 204);
    }
}
