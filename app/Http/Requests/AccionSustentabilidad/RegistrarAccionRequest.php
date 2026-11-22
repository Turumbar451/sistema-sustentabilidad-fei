<?php

declare(strict_types=1);

namespace App\Http\Requests\AccionSustentabilidad;

use App\Enums\FormaTrabajoEnum;
use App\Models\EjeTrabajo;
use App\Models\UsuarioInstitucional;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarAccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof UsuarioInstitucional;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:200'],
            'descripcion' => ['required', 'string', 'max:1200'],
            'idEjeTrabajo' => ['required', 'integer', Rule::exists(EjeTrabajo::class, 'idEjeTrabajo')],
            'modalidad' => ['required', 'string', Rule::in(['Presencial', 'Virtual', 'Híbrida'])],
            'formaTrabajo' => ['required', 'string', Rule::enum(FormaTrabajoEnum::class)],
            'fechaInicio' => ['required', 'date_format:Y-m-d'],
            'fechaTermino' => ['required', 'date_format:Y-m-d', 'after_or_equal:fechaInicio'],
            'requerimientos' => ['nullable', 'string', 'max:600'],
            'evidencias' => ['nullable', 'array'],
            'evidencias.*' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'nombre.required' => 'Escribe el nombre de la iniciativa.',
            'nombre.string' => 'El nombre debe ser un texto.',
            'nombre.max' => 'El nombre no puede superar los 200 caracteres.',
            'descripcion.required' => 'Describe la iniciativa.',
            'descripcion.string' => 'La descripción debe ser un texto.',
            'descripcion.max' => 'La descripción no puede superar los 1200 caracteres.',
            'idEjeTrabajo.required' => 'Selecciona un eje de trabajo.',
            'idEjeTrabajo.integer' => 'El eje de trabajo debe ser un identificador válido.',
            'idEjeTrabajo.exists' => 'El eje de trabajo seleccionado no existe.',
            'modalidad.required' => 'Selecciona la modalidad.',
            'modalidad.string' => 'La modalidad debe ser un texto.',
            'modalidad.in' => 'Selecciona Presencial, Virtual o Híbrida como modalidad.',
            'formaTrabajo.required' => 'Selecciona la forma de trabajo.',
            'formaTrabajo.string' => 'La forma de trabajo debe ser un texto.',
            'formaTrabajo.enum' => 'Selecciona Individual o Equipo conformado como forma de trabajo.',
            'fechaInicio.required' => 'Indica la fecha de inicio.',
            'fechaInicio.date_format' => 'La fecha de inicio debe tener el formato AAAA-MM-DD y ser válida.',
            'fechaTermino.required' => 'Indica la fecha de término.',
            'fechaTermino.date_format' => 'La fecha de término debe tener el formato AAAA-MM-DD y ser válida.',
            'fechaTermino.after_or_equal' => 'La fecha de término debe ser igual o posterior a la fecha de inicio.',
            'requerimientos.string' => 'Los requerimientos deben ser un texto.',
            'requerimientos.max' => 'Los requerimientos no pueden superar los 600 caracteres.',
            'evidencias.array' => 'Las evidencias deben enviarse como una lista de archivos.',
            'evidencias.*.required' => 'Selecciona un archivo de evidencia válido.',
            'evidencias.*.file' => 'Cada evidencia debe ser un archivo válido.',
            'evidencias.*.mimes' => 'Las evidencias solo pueden ser archivos PDF, JPG o PNG.',
            'evidencias.*.max' => 'Cada evidencia puede pesar como máximo 5 MB.',
        ];
    }
}
