import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AdminLayout from '@/components/ui/admin-layout';
import { router } from '@inertiajs/react';

export default function Show({ item }) {
  return (
    <AdminLayout>
      <Head title="Tenant Details" />
      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <Card>
            <CardHeader className="flex flex-row justify-between items-center">
              <CardTitle className="text-2xl">Tenant Details</CardTitle>
              <div className="flex space-x-2">
                <Button
                  variant="outline"
                  onClick={() => router.visit(route('admin.tenants.edit', item.id))}
                >
                  Edit
                </Button>
                <Button
                  variant="destructive"
                  onClick={() => {
                    if (confirm('Are you sure you want to delete this item?')) {
                      router.delete(route('admin.tenants.destroy', item.id));
                    }
                  }}
                >
                  Delete
                </Button>
              </div>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <p className="text-sm font-medium text-muted-foreground">ID</p>
                  <p>{item.id}</p>
                </div>
                
                <div className="space-y-2">
                  <p className="text-sm font-medium text-muted-foreground">Name</p>
                  <p>{item.name}</p>
                </div>
                
                {/* Add more fields as needed */}
                
                <div className="space-y-2">
                  <p className="text-sm font-medium text-muted-foreground">Status</p>
                  <p className={`px-2.5 py-1 rounded-full text-xs font-medium inline-block ${item.is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}`}>
                    {item.is_active ? 'Active' : 'Inactive'}
                  </p>
                </div>
                
                <div className="space-y-2">
                  <p className="text-sm font-medium text-muted-foreground">Created At</p>
                  <p>{new Date(item.created_at).toLocaleString()}</p>
                </div>
                
                <div className="space-y-2">
                  <p className="text-sm font-medium text-muted-foreground">Updated At</p>
                  <p>{new Date(item.updated_at).toLocaleString()}</p>
                </div>
              </div>
              
              <div className="mt-6">
                <Button
                  variant="outline"
                  onClick={() => router.visit(route('admin.tenants.index'))}
                >
                  Back to List
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </AdminLayout>
  );
}
