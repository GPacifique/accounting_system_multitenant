<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    

    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $q = $request->get('q');

        $query = Employee::query()->orderBy('created_at', 'desc');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('position', 'like', "%{$q}%")
                    ->orWhere('department', 'like', "%{$q}%");
            });
        }

        $employees = $query->paginate(15);

        return view('employees.index', compact('employees', 'q'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'      => ['required', 'string', 'max:255'],
            'last_name'       => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('employees', 'email')],
            'phone'           => ['nullable', 'string', 'max:40'],
            'position'        => ['nullable', 'string', 'max:255'],
            'salary'          => ['nullable', 'numeric', 'min:0'],
            'date_of_joining' => ['nullable', 'date'],
            'department'      => ['nullable', 'string', 'max:255'],
        ]);

        // Normalize salary to decimal
        if (isset($data['salary'])) {
            $data['salary'] = number_format((float) $data['salary'], 2, '.', '');
        }

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'first_name'      => ['required', 'string', 'max:255'],
            'last_name'       => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employee->id)],
            'phone'           => ['nullable', 'string', 'max:40'],
            'position'        => ['nullable', 'string', 'max:255'],
            'salary'          => ['nullable', 'numeric', 'min:0'],
            'date_of_joining' => ['nullable', 'date'],
            'department'      => ['nullable', 'string', 'max:255'],
        ]);

        if (isset($data['salary'])) {
            $data['salary'] = number_format((float) $data['salary'], 2, '.', '');
        }

        $employee->update($data);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted.');
    }
}
