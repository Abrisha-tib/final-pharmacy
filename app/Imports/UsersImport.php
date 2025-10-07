<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    public function model(array $row)
    {
        // Generate password if not provided
        $password = $row['password'] ?? Str::random(12);
        
        // Get role IDs from role names
        $roleIds = [];
        if (!empty($row['roles'])) {
            $roleNames = explode(',', $row['roles']);
            $roles = Role::whereIn('name', array_map('trim', $roleNames))->get();
            $roleIds = $roles->pluck('id')->toArray();
        }

        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($password),
            'phone' => $row['phone'] ?? null,
            'department' => $row['department'] ?? null,
            'notes' => $row['notes'] ?? null,
            'status' => $row['status'] ?? 'active',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.email' => 'required|email|unique:users,email',
            '*.phone' => 'nullable|string|max:20',
            '*.department' => 'nullable|string|max:100',
            '*.notes' => 'nullable|string|max:1000',
            '*.status' => 'nullable|in:active,inactive,suspended',
            '*.roles' => 'nullable|string',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function customValidationMessages()
    {
        return [
            '*.name.required' => 'Name is required for all users.',
            '*.email.required' => 'Email is required for all users.',
            '*.email.email' => 'Email must be a valid email address.',
            '*.email.unique' => 'Email must be unique.',
            '*.status.in' => 'Status must be one of: active, inactive, suspended.',
        ];
    }
}
