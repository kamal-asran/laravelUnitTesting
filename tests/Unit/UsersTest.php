<?php

namespace Tests\Unit;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class UsersTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_all_users_list()
    {
        $response=$this->withoutDeprecationHandling()->get('/users');
        $response->assertStatus(200);
        $response->dump();        
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 10)
            ->whereAllType([    //assert against types
                'data.0.name' => 'string',  
                'data.0.id' => 'integer'
            ])->where(            //assert against values
                'data.0.id',1
            )->hasAll('data','next_page_url')->missing('error')->etc()      //assert against Presence / Absence
        );

        
    }
    public function test_a_welcome_view_can_be_rendered()
    {
        $view = $this->view('welcome');
 
        $view->assertSee('laravel');
    }

    public function test_avatars_can_be_uploaded()
    {
        $stub = storage_path('app/public/ERD.pdf');
        $name = 'avatar.png';
        $path = storage_path('app/public/avatars').'/'.$name;
        copy($stub, $path);
        $file = UploadedFile::fake()->image($name);
        $response = $this->post('/avatars', [
            'avatar' => $file,
        ]);
        $response->assertStatus(200);
        Storage::disk('avatars')->assertExists($name);
    }
}
