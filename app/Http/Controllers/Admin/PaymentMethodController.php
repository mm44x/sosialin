<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $rows = PaymentMethod::orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.payment_methods.index', compact('rows'));
    }

    public function create()
    {
        return view('admin.payment_methods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'            => ['required', 'in:bank,qris,ewallet,other'],
            'name'            => ['required', 'string', 'max:150'],
            'bank_name'       => ['nullable', 'string', 'max:80'],
            'account_name'    => ['nullable', 'string', 'max:120'],
            'account_number'  => ['nullable', 'string', 'max:80'],
            'instructions'    => ['nullable', 'string'],
            'sort_order'      => ['nullable', 'integer', 'between:-1000,1000'],
            'is_active'       => ['nullable', 'boolean'],
            'media'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->store('payment_methods', 'public');
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.payment-methods.index')->with('status', 'Metode pembayaran dibuat.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment_methods.edit', ['row' => $paymentMethod]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'type'            => ['required', 'in:bank,qris,ewallet,other'],
            'name'            => ['required', 'string', 'max:150'],
            'bank_name'       => ['nullable', 'string', 'max:80'],
            'account_name'    => ['nullable', 'string', 'max:120'],
            'account_number'  => ['nullable', 'string', 'max:80'],
            'instructions'    => ['nullable', 'string'],
            'sort_order'      => ['nullable', 'integer', 'between:-1000,1000'],
            'is_active'       => ['nullable', 'boolean'],
            'media'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('media')) {
            if ($paymentMethod->media_path) {
                Storage::disk('public')->delete($paymentMethod->media_path);
            }
            $data['media_path'] = $request->file('media')->store('payment_methods', 'public');
        }

        $paymentMethod->update($data);

        return redirect()->route('admin.payment-methods.index')->with('status', 'Metode pembayaran diperbarui.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->media_path) {
            Storage::disk('public')->delete($paymentMethod->media_path);
        }
        $paymentMethod->delete();

        return back()->with('status', 'Metode pembayaran dihapus.');
    }
}
