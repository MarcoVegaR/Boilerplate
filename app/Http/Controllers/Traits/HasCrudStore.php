<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;

trait HasCrudStore
{
    use HasFlashMessages;
    
    /**
     * Store a newly created resource in storage.
     *
     * @param FormRequest $request
     * @return RedirectResponse
     */
    public function store(FormRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());
        
        $this->createdSuccessfully();
        
        return redirect()->route("$this->routeName.index");
    }
}
