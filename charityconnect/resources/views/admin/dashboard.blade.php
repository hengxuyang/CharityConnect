<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Statistics Card -->
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">System Statistics</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Charities</p>
                                    <p class="text-2xl font-bold">{{ $totalCharities ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending Approval</p>
                                    <p class="text-2xl font-bold">{{ $pendingCharities ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Donations</p>
                                    <p class="text-2xl font-bold">{{ $totalDonations ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Users</p>
                                    <p class="text-2xl font-bold">{{ $totalUsers ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Charities Card -->
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Pending Charity Approvals</h3>
                            @if(isset($pendingCharitiesList) && count($pendingCharitiesList) > 0)
                                <ul class="space-y-2">
                                    @foreach($pendingCharitiesList as $charity)
                                        <li class="flex justify-between items-center">
                                            <span>{{ $charity->name }}</span>
                                            <a href="{{ route('admin.charities') }}" class="text-blue-500 hover:text-blue-700">Review</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No pending charity approvals.</p>
                            @endif
                            <div class="mt-4">
                                <a href="{{ route('admin.charities') }}" class="text-blue-500 hover:text-blue-700">View all charities</a>
                            </div>
                        </div>

                        <!-- Quick Actions Card -->
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('admin.charities') }}" class="block w-full py-2 px-4 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
                                    Manage Charities
                                </a>
                                <a href="{{ route('admin.users') }}" class="block w-full py-2 px-4 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
                                    Manage Users
                                </a>
                                <a href="{{ route('categories.index') }}" class="block w-full py-2 px-4 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
                                    Manage Categories
                                </a>
                                <a href="{{ route('items.index') }}" class="block w-full py-2 px-4 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
                                    Manage Items
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Activity</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                    @if(isset($recentActivities) && count($recentActivities) > 0)
                                        @foreach($recentActivities as $activity)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $activity->description }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $activity->user->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $activity->created_at->diffForHumans() }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No recent activity.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
