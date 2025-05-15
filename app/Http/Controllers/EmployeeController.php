<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesExport;
use App\Http\Requests\ExcelRequest;
use App\Http\Requests\UserRequest;
use App\Imports\EmployeesImport;
use App\Models\Employee;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function import(ExcelRequest $request)
    {
        Excel::import(new EmployeesImport, $request->file('file'));

        return redirect()->route('employees.index')->with('success', 'Imported successfully.');
    }

    public function export()
    {
        $count = Employee::count();
        if ($count == 0) {
            return redirect()->route('employees.index')->with('error', 'No records found to export.');
        } else {
            return Excel::download(new EmployeesExport, 'employees.xlsx');
        }
    }

    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    public function store(UserRequest $request)
    {
        $employee = Employee::create($request->validated());
        if ($request->ajax()) {
            return response()->json(['message' => 'Employee created successfully.']);
        }
        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return response()->json($employee);
    }

    public function update(UserRequest $request, Employee $employee)
    {
        $employee->update($request->validated());
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
