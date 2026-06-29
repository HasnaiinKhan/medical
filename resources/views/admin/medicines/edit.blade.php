@extends('admin.layouts.admin')
@section('title', 'Edit Medicine')
@section('page-title', 'Edit Medicine')
@section('page-subtitle', $medicine->name)

@section('content')
<form method="post" action="{{ route('admin.medicines.update', $medicine) }}" enctype="multipart/form-data" style="position:relative; z-index:10; overflow:visible;">
    @csrf
    @method('PUT')
    @include('admin.medicines._form')
</form>
@endsection
