<?php
namespace Tests\Unit\Http\Requests;
use Tests\TestCase;
use App\Http\Requests\ModuleUpdateRequest;

class ModuleUpdateRequestTest extends TestCase
{
    public function testValidationRules()
    {
        $request = new ModuleUpdateRequest();

        // Utilisez la méthode `rules` pour obtenir les règles de validation
        $rules = $request->rules();

        // Vérifiez que la règle pour 'coefficient' est correcte
        $this->assertArrayHasKey('coefficient', $rules);
        $this->assertEquals(['int'], $rules['coefficient']);
    }
}
