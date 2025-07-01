<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;

trait HasCrudUpdate
{
    use HasFlashMessages;
    
    /**
     * Update the specified resource in storage.
     *
     * @param FormRequest $request
     * @param string|int $id
     * @return RedirectResponse
     */
    public function update(FormRequest $request, string|int $id): RedirectResponse
    {
        $this->service->update($id, $request->validated());
        
        $this->updatedSuccessfully();
        
        return redirect()->route("$this->routeName.index");
    }
}
