<?php

namespace App\Http\Controllers\Traits;

use Inertia\Inertia;
use Inertia\Response;

trait HasCrudCreate
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        $additionalData = $this->getAdditionalData() ?? [];
        
        return Inertia::render("$this->viewPath/Create", $additionalData);
    }
    
    /**
     * Get additional data for views.
     *
     * @return array
     */
    protected function getAdditionalData(): array
    {
        return [];
    }
}
