<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Charity Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Statistics Card -->
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Donation Statistics</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Active Requests</p>
                                    <p class="text-2xl font-bold">{{ $activeRequests ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending Donations</p>
                                    <p class="text-2xl font-bold">{{ $pendingDonations ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Fulfilled Requests</p>
                                    <p class="text-2xl font-bold">{{ $fulfilledRequests ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Donations</p>
                                    <p class="text-2xl font-bold">{{ $totalDonations ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Status Card -->
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Inventory Status</h3>
                            @if(isset($lowStockItems) && count($lowStockItems) > 0)
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-red-600 dark:text-red-400 mb-2">Low Stock Items</h4>
                                    <ul class="space-y-2">
                                        @foreach($lowStockItems as $item)
                                            <li class="flex justify-between items-center">
                                                <span>{{ $item->name }}</span>
                                                <span class="text-red-600 dark:text-red-400">{{ $item->quantity }} {{ $item->unit }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 mb-4">No low stock items.</p>
                            @endif
                            <div class="mt-4">
                                <a href="{{ route('inventory.index') }}" class="text-blue-500 hover:text-blue-700">View full inventory</a>
                            </div>
                        </div>

                        <!-- Quick Actions Card -->
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('requests.create') }}" class="block w-full py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Create Donation Request
                                </a>
                                <a href="{{ route('inventory.index') }}" class="block w-full py-2 px-4 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
                                    Manage Inventory
                                </a>
                                <a href="{{ route('requests.index') }}" class="block w-full py-2 px-4 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
                                    View Donation Requests
                                </a>
                                <a href="{{ route('donations.index') }}" class="block w-full py-2 px-4 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
                                    View Donations
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Donations -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Recent Donations</h3>
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Donor</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                    @if(isset($recentDonations) && count($recentDonations) > 0)
                                        @foreach($recentDonations as $donation)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $donation->request->item->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $donation->quantity }} {{ $donation->request->item->unit }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $donation->donor->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @if($donation->status == 'Pending')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                            Pending
                                                        </span>
                                                    @elseif($donation->status == 'Delivered')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                            Delivered
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                            Cancelled
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $donation->created_at->format('M d, Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                    <a href="{{ route('donations.show', $donation) }}" class="text-blue-500 hover:text-blue-700">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No recent donations.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{ route('donations.index') }}" class="text-blue-500 hover:text-blue-700">View all donations</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
