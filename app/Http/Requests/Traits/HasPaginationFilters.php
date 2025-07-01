<?php

namespace App\Http\Requests\Traits;

trait HasPaginationFilters
{
    /**
     * Get filters from request.
     */
    public function filters(): array
    {
        $allowed = $this->allowedFilters();
        return array_filter(
            $this->only($allowed),
            fn($value) => $value !== null && $value !== ''
        );
    }
    
    /**
     * Get the search term from request.
     */
    public function getSearchTerm(): ?string
    {
        return $this->input('search');
    }
    
    /**
     * Get the per page value from request.
     */
    public function perPage(?int $default = 15): int
    {
        return $this->input('per_page', $default);
    }
    
    /**
     * Get the sort column from request.
     */
    public function getSort(?string $default = 'created_at'): string
    {
        return $this->input('sort', $default);
    }
    
    /**
     * Get the sort direction from request.
     */
    public function getDirection(?string $default = 'desc'): string
    {
        return $this->input('direction', $default);
    }
    
    /**
     * Get the allowed filters for the request.
     * Must be implemented by the class using this trait.
     */
    abstract protected function allowedFilters(): array;
}
