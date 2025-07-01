import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import { AdminPageProps } from '@/types/admin';

interface AdminStats {
  total_tenants: number;
  active_tenants: number;
  total_plans: number;
}

export default function AdminDashboard({ auth, stats, status }: AdminPageProps & { stats: AdminStats; status?: string }) {
  return (
    <AdminLayout
      header={
        <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Admin Dashboard
        </h2>
      }
    >
      <Head title="Admin Dashboard" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          {status && (
            <div className="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg">
              {status}
            </div>
          )}
          
          <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-6 text-gray-900 dark:text-gray-100">
              <h3 className="text-lg font-medium mb-4">SaaS Platform Statistics</h3>
              
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                  <div className="text-sm text-blue-600 dark:text-blue-300">Total Tenants</div>
                  <div className="text-2xl font-bold">{stats.total_tenants}</div>
                </div>
                
                <div className="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg">
                  <div className="text-sm text-green-600 dark:text-green-300">Active Tenants</div>
                  <div className="text-2xl font-bold">{stats.active_tenants}</div>
                </div>
                
                <div className="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg">
                  <div className="text-sm text-purple-600 dark:text-purple-300">Available Plans</div>
                  <div className="text-2xl font-bold">{stats.total_plans}</div>
                </div>
              </div>
              
              <div className="mt-6">
                <h4 className="font-medium mb-2">Quick Actions</h4>
                <div className="flex space-x-2">
                  <a 
                    href={route('admin.tenants.index')}
                    className="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                  >
                    Manage Tenants
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AdminLayout>
  );
}
