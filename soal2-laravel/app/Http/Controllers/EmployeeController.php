<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Resources\EmployeeResource;



class EmployeeController extends Controller
{
    public function index()
    {
        return EmployeeResource::collection(Employee::all());
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
        
            $data['photo_upload_path'] = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $path;

        }
        

        $data['created_on'] = now();
        $employee = Employee::create($data);

        Redis::set("emp_{$employee->nomor}", json_encode($employee));

        return new EmployeeResource($employee);
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return new EmployeeResource($employee);
    }

    public function update(StoreEmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $data['photo_upload_path'] = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $path;
        }

        $data['updated_on'] = now();
        $employee->update($data);

        Redis::set("emp_{$employee->nomor}", json_encode($employee));

        return new EmployeeResource($employee);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        Redis::del("emp_{$employee->nomor}");
        $employee->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

