<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'didi',
            'password' => 'didi123',
            'name' => 'Tengku Didi Ferdillah'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'didi',
                    'name' => 'Tengku Didi Ferdillah'
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'didi',
            'password' => 'didi123',
            'name' => 'Tengku Didi Ferdillah'
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test'
                ]
            ]);

        $user = User::where('username', 'test')->first();
        assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'ngasal'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorized' => '123'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test')->first();

        $this->patch('/api/users/current', 
        [
            'name' => 'baru'
        ],
        [
            'Authorization' => 'test'
        ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "baru"
                ]
            ]);

        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test')->first();

        $this->patch('/api/users/current', 
        [
            'password' => 'baru'
        ],
        [
            'Authorization' => 'test'
        ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);

        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current', 
        [
            'name' => 'sjjjdnhttgggdtffstfasjjjdnhttgggdtffstfasjjjdnhttgggdtffstfasjjjdnhttgggdtffstfasjjjdnhttgggdtffstfas'
        ],
        [
            'Authorization' => 'test'
        ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        'The name must not be greater than 100 characters.'
                    ]
                ]
            ]);

    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
        
        $user = User::where('username', 'test')->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: [
            "Authorization" => "testsalah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

}
