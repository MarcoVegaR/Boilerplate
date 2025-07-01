<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasCrudIndex;
use App\Http\Controllers\Traits\HasCrudShow;
use App\Http\Controllers\Traits\HasCrudCreate;
use App\Http\Controllers\Traits\HasCrudEdit;
use App\Http\Controllers\Traits\HasCrudStore;
use App\Http\Controllers\Traits\HasCrudUpdate;
use App\Http\Controllers\Traits\HasCrudDestroy;
use App\Services\Contracts\TenantServiceInterface;
use App\Http\Requests\Admin\StoreTenantRequest;
use App\Http\Requests\Admin\UpdateTenantRequest;

class TenantController extends Controller
{
    use HasCrudIndex;
    use HasCrudShow;
    use HasCrudCreate;
    use HasCrudEdit;
    use HasCrudStore;
    use HasCrudUpdate;
    use HasCrudDestroy;

    /**
     * The service instance.
     *
     * @var TenantServiceInterface
     */
    protected $service;
    
    /**
     * The base view path.
     *
     * @var string
     */
    protected string $viewPath;
    
    /**
     * The base route name.
     *
     * @var string
     */
    protected string $routeName;
    
    /**
     * Columns to search in.
     *
     * @var array
     */
    protected array $searchColumns = ['name', 'domain', 'database'];
    
    /**
     * Relationships to eager load.
     *
     * @var array
     */
    protected array $relationships = ['plan'];
    
    /**
     * Create a new controller instance.
     *
     * @param TenantServiceInterface $service
     */
    public function __construct(TenantServiceInterface $service)
    {
        $this->service = $service;
        $this->viewPath = 'Admin/Tenant';
        $this->routeName = 'admin.tenants';
    }

    /**
     * Provide specific validation rules for store action.
     *
     * @param StoreTenantRequest $request
     * @return array
     */
    public function store(StoreTenantRequest $request)
    {
        return $this->storeItem($request);
    }
    
    /**
     * Provide specific validation rules for update action.
     *
     * @param UpdateTenantRequest $request
     * @param string|int $id
     * @return array
     */
    public function update(UpdateTenantRequest $request, $id)
    {
        return $this->updateItem($request, $id);
    }
    
    /**
     * Get additional data for views.
     *
     * @return array
     */
    protected function getAdditionalData(): array
    {
        return [
            'plans' => \App\Models\Plan::select('id', 'name', 'description', 'price')->get(),
            'statusOptions' => [
                ['value' => true, 'label' => 'Active'],
                ['value' => false, 'label' => 'Inactive']
            ]
        ];
    }
    
    /**
     * Extend tenant trial period
     *
     * @param Request $request
     * @param string|int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function extendTrial(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);
        
        $days = $request->input('days', 30);        
        $tenant = $this->service->processBusinessLogic($id, [
            'action' => 'extend_trial',
            'days' => $days
        ]);
        
        session()->flash('success', 'Trial extended successfully for ' . $tenant->name);
        
        return redirect()->route("$this->routeName.show", $tenant->id);
    }
    
    /**
     * Change tenant plan
     *
     * @param Request $request
     * @param string|int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePlan(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'plan_id' => 'required|exists:landlord.plans,id'
        ]);
        
        $tenant = $this->service->processBusinessLogic($id, [
            'action' => 'change_plan',
            'plan_id' => $request->input('plan_id')
        ]);
        
        session()->flash('success', 'Plan changed successfully for ' . $tenant->name);
        
        return redirect()->route("$this->routeName.show", $tenant->id);
    }
    
    /**
     * Toggle tenant activation status
     *
     * @param Request $request
     * @param string|int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(\Illuminate\Http\Request $request, $id)
    {
        $tenant = $this->service->show($id);
        $action = $tenant->is_active ? 'deactivate' : 'activate';
        
        $tenant = $this->service->processBusinessLogic($id, [
            'action' => $action
        ]);
        
        $statusText = $tenant->is_active ? 'activated' : 'deactivated';
        
        session()->flash('success', "Tenant {$statusText} successfully");
        
        return redirect()->route("$this->routeName.show", $tenant->id);
    }
    
    /**
     * Force activate or deactivate a tenant (using explicit state)
     *
     * @param Request $request
     * @param string|int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setStatus(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'status' => 'required|boolean'
        ]);
        
        $status = (bool) $request->input('status');
        
        $tenant = $this->service->processBusinessLogic($id, [
            'action' => $status ? 'activate' : 'deactivate'
        ]);
        
        $statusText = $tenant->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Tenant {$statusText} successfully");
        
        return redirect()->route("$this->routeName.show", $tenant->id);
    }
}
