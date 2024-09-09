<?php
namespace Tests\Unit\Models;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_admin()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer'=>'OUI'
        ]);

        $adminData = [
            'user_id' => $user->id,
        ];

        $admin = Admin::create($adminData);

        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertEquals($user->id, $admin->user_id);
    }

    public function test_admin_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('admins', [
                'id','user_id'
            ]), 1);
    }

    public function test_get_Model_Type()
    {
        $admin = new Admin();
        $this->assertEquals("admin",$admin->getModelType());
    }

    public function test_user_relation()
    {
        $admin = new Admin();
        $bel = $admin->user();
        $this->assertInstanceOf(BelongsTo::class,$bel);

    }
}
