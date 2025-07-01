<?php

namespace App\Http\Controllers\Traits;

use Inertia\Inertia;
use Inertia\Response;

trait HasCrudEdit
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param string|int $id
     * @return Response
     */
    public function edit(string|int $id): Response
    {
        $item = $this->service->show($id, $this->relationships ?? []);
        $additionalData = $this->getAdditionalData() ?? [];
        
        return Inertia::render("$this->viewPath/Edit", array_merge([
            'item' => $item,
        ], $additionalData));
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
