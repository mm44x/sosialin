@extends('errors.minimal', [
    'code' => 429,
    'title' => 'Terlalu banyak permintaan',
    'message' => 'Anda melakukan aksi terlalu cepat. Coba lagi sebentar.',
])
