import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AdminLayout from '@/components/ui/admin-layout';

export default function Edit({ item }) {
  const { data, setData, put, processing, errors } = useForm({
    // Add your form fields here
    name: item.name || '',
    is_active: item.is_active || false,
    // Add more fields as needed
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    put(route('admin.tenants.update', item.id));
  };

  return (
    <AdminLayout>
      <Head title="Edit Tenant" />
      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <Card>
            <CardHeader>
              <CardTitle className="text-2xl">Edit Tenant</CardTitle>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmit}>
                <div className="grid grid-cols-1 gap-6 mt-4">
                  <div>
                    <FormLabel htmlFor="name">Name</FormLabel>
                    <Input
                      id="name"
                      type="text"
                      className="mt-1 block w-full"
                      value={data.name}
                      onChange={(e) => setData('name', e.target.value)}
                      required
                    />
                    {errors.name && <FormMessage>{errors.name}</FormMessage>}
                  </div>

                  {/* Add more form fields as needed */}
                  
                  <div className="flex items-center gap-2 mt-2">
                    <Checkbox
                      id="is_active"
                      checked={data.is_active}
                      onCheckedChange={(checked) => setData('is_active', !!checked)}
                    />
                    <FormLabel htmlFor="is_active" className="cursor-pointer">
                      Active
                    </FormLabel>
                  </div>

                  <div className="flex items-center justify-end mt-4">
                    <Button
                      type="button"
                      variant="outline"
                      className="mr-2"
                      onClick={() => window.history.back()}
                    >
                      Cancel
                    </Button>
                    <Button type="submit" disabled={processing}>
                      Update
                    </Button>
                  </div>
                </div>
              </form>
            </CardContent>
          </Card>
        </div>
      </div>
    </AdminLayout>
  );
}
