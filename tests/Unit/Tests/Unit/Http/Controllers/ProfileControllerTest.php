<?php

namespace Tests\Unit\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\View\View;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer'=>'OUI'
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
    }


    public function test_update()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer'=>'OUI'
        ]);

        $data = [
            'firstname' => 'newName',
            'lastname' => 'newLastName',
            'email' => 'nouveau@email.com',
        ];

        $response = $this->actingAs($user)->patch(route('profile.update'), $data);

        $response->assertRedirect(route('profile.edit'));
    }

    public function testDestroy()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer'=>'OUI'
        ]);


        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'passzword',
        ]);


        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
