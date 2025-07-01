<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Inertia\Inertia;
use Inertia\Response;

trait HasCrudIndex
{
    /**
     * Display a listing of the resource.
     *
     * @param FormRequest $request
     * @return Response
     */
    public function index(FormRequest $request): Response
    {
        $filters = method_exists($request, 'filters') ? $request->filters() : [];
        $perPage = method_exists($request, 'perPage') ? $request->perPage($this->perPage ?? 15) : ($this->perPage ?? 15);
        
        // Si el request tiene método getSearchTerm, añadimos la búsqueda a los filtros
        if (method_exists($request, 'getSearchTerm') && $request->getSearchTerm()) {
            $filters['search'] = $request->getSearchTerm();
        }
        
        $items = $this->service->list($filters, $perPage);
        
        return Inertia::render("$this->viewPath/Index", [
            'items' => $items,
            'filters' => method_exists($request, 'filters') ? $request->filters() : [],
            'search' => method_exists($request, 'getSearchTerm') ? $request->getSearchTerm() : null,
        ] + $this->getAdditionalData());
    }
    
    /**
     * Get additional data for the view.
     *
     * @return array
     */
    protected function getAdditionalData(): array
    {
        return [];
    }
}
