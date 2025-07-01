<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasCrudDestroy
{
    use HasFlashMessages;
    
    /**
     * Remove the specified resource from storage.
     *
     * @param string|int $id
     * @return RedirectResponse
     */
    public function destroy(string|int $id): RedirectResponse
    {
        $forceDelete = request()->has('force');
        
        $this->service->destroy($id, $forceDelete);
        
        if ($forceDelete) {
            $this->success('messages.permanently_deleted_successfully');
        } else {
            $this->deletedSuccessfully();
        }
        
        return redirect()->route("$this->routeName.index");
    }
    
    /**
     * Restore the specified soft-deleted resource.
     *
     * @param string|int $id
     * @return RedirectResponse
     */
    public function restore(string|int $id): RedirectResponse
    {
        $this->service->restore($id);
        
        $this->restoredSuccessfully();
        
        return redirect()->route("$this->routeName.index");
    }
}
