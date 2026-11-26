<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use App\Models\UsuarioInstitucional;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class App extends Component
{
    public ?UsuarioInstitucional $usuarioAutenticado;

    public function __construct(
        public string $title = 'FEI Sustentabilidad',
        public string $metaDescription = 'Consulta las iniciativas de sustentabilidad de la comunidad universitaria de la FEI.',
    ) {
        $this->usuarioAutenticado = Auth::guard('web')->user();
        $this->usuarioAutenticado?->loadMissing('rolesVigentes');
    }

    public function render(): View
    {
        return view('components.layouts.app');
    }
}
