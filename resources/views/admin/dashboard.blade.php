<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Admin — Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid md:grid-cols-4 gap-4">
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Jumlah User</div>
                    <div class="mt-2 text-2xl font-bold">{{ $stats['users'] }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Total Saldo</div>
                    <div class="mt-2 text-2xl font-bold">Rp {{ number_format($stats['total_wallet'], 2) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Order Pending</div>
                    <div class="mt-2 text-2xl font-bold">{{ $stats['orders_pending'] }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Total Order</div>
                    <div class="mt-2 text-2xl font-bold">{{ $stats['orders_total'] }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
