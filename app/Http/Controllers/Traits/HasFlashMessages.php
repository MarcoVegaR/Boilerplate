<?php

namespace App\Http\Controllers\Traits;

trait HasFlashMessages
{
    /**
     * Flash a success message.
     */
    protected function success(string $key, array $replace = []): void
    {
        session()->flash('success', __($key, $replace));
    }

    /**
     * Flash an error message.
     */
    protected function error(string $key, array $replace = []): void
    {
        session()->flash('error', __($key, $replace));
    }

    /**
     * Flash a "created successfully" message.
     */
    protected function createdSuccessfully(): void
    {
        $this->success('messages.created_successfully');
    }

    /**
     * Flash an "updated successfully" message.
     */
    protected function updatedSuccessfully(): void
    {
        $this->success('messages.updated_successfully');
    }

    /**
     * Flash a "deleted successfully" message.
     */
    protected function deletedSuccessfully(): void
    {
        $this->success('messages.deleted_successfully');
    }

    /**
     * Flash a "restored successfully" message.
     */
    protected function restoredSuccessfully(): void
    {
        $this->success('messages.restored_successfully');
    }
}
