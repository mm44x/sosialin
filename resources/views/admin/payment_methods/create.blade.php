<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Tambah Metode Pembayaran</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('admin.payment-methods.store') }}" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf
                    @include('admin.payment_methods.partials.form', ['row' => null])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
