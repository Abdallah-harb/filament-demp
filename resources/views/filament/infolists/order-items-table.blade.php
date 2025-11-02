<div class="overflow-x-auto">
    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-800">
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Product</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Price</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Quantity</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Discount</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($getRecord()->orderDetails as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                        {{ $item->product->name }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                        EGP {{ number_format($item->price, 2) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                        {{ $item->quantity }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                        EGP {{ number_format($item->discount, 2) }}
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        EGP {{ number_format($item->total, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

