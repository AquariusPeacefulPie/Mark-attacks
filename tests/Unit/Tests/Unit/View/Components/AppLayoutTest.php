<?php
namespace Tests\Unit\View\Components;
use Tests\TestCase;
use App\View\Components\AppLayout;
use Illuminate\View\View;

class AppLayoutTest extends TestCase
{
    public function testRenderMethodReturnsView()
    {
        // Crée une instance du composant
        $component = new AppLayout();

        // Appelle la méthode render pour obtenir la vue
        $view = $component->render();

        // Vérifie que la méthode render retourne une instance de Illuminate\View\View
        $this->assertInstanceOf(View::class, $view);
    }


}
