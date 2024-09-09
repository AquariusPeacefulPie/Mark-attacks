<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function addStudentToDB(
        $email,
        $birthdate,
        $lastName,
        $firstName,
        $active,
        $answer,
        $passwordHash = '$2y$10$QZbQjrEAiwNARn0adIBp..ssDG6Ks2R3BpJRcnTJTNKB4uHSdNODG',

        ) : Student {

        $userData = [
            'password' => $passwordHash,
            'email' => $email,
            'birthdate' => $birthdate,
            'lastname' => $lastName,
            'firstname' => $firstName,
            'security_answer' => $answer,
            'role' => 'student',
        ];

        $user = User::create($userData);

        $studentData = [
            'user_id' => $user->id,
            'active' => $active,
        ];

        $student = Student::create($studentData);

        return $student;
    }

    public function addTeacherToDB(
        $email,
        $birthdate,
        $lastName,
        $firstName,
        $active,
        $answer,
        $passwordHash = '$2y$10$QZbQjrEAiwNARn0adIBp..ssDG6Ks2R3BpJRcnTJTNKB4uHSdNODG' //hash of 123456789
    ) : Teacher {
        $userData = [
            'password' => $passwordHash,
            'email' => $email,
            'birthdate' => $birthdate,
            'lastname' => $lastName,
            'firstname' => $firstName,
            'security_answer' => $answer,
            'role' => 'student',
        ];

        $user = User::create($userData);

        $teacherData = [
            'user_id' => $user->id,
            'active' => $active,
        ];

        $teacher = Teacher::create($teacherData);

        return $teacher;
    }
}
