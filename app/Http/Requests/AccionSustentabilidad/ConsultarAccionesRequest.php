<?php

declare(strict_types=1);

namespace App\Http\Requests\AccionSustentabilidad;

use App\Enums\EstadoAccionEnum;
use App\Enums\FormaTrabajoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConsultarAccionesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ejes' => (array) $this->input('ejes', $this->input('idEjeTrabajo', [])),
        ]);
    }

    /** @return array<string, array<mixed>> */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:200'],
            'buscar' => ['nullable', 'string', 'max:200'],
            'ejes' => ['array'],
            'ejes.*' => ['integer', 'min:1'],
            'modalidad' => ['nullable', 'string', 'max:100'],
            'formaTrabajo' => ['nullable', Rule::enum(FormaTrabajoEnum::class)],
            'estado' => ['nullable', Rule::in(EstadoAccionEnum::valoresVisiblesEnCatalogo())],
            'orden' => ['sometimes', Rule::in(['recientes', 'proximas', 'az'])],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    /** @return array<string, mixed> */
    public function filtros(): array
    {
        $datos = $this->validated();

        return array_filter([
            'q' => trim($datos['q'] ?? $datos['buscar'] ?? ''),
            'idEjeTrabajo' => array_values(array_unique(array_map('intval', $datos['ejes']))),
            'modalidad' => $datos['modalidad'] ?? '',
            'formaTrabajo' => $datos['formaTrabajo'] ?? '',
            'estado' => $datos['estado'] ?? '',
            'orden' => $datos['orden'] ?? 'recientes',
        ], static fn (mixed $valor): bool => $valor !== '' && $valor !== []);
    }

    public function hayFiltros(): bool
    {
        return array_diff_key($this->filtros(), ['orden' => true]) !== [];
    }
}
