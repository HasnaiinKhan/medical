@extends('admin.layouts.admin')
@section('title', 'Add Medicine')
@section('page-title', 'Add Medicine')
@section('page-subtitle', 'Create a new medicine in the catalogue')

@section('content')
<form method="post" action="{{ route('admin.medicines.store') }}" enctype="multipart/form-data" style="position:relative; z-index:10; overflow:visible;">
    @csrf
    @include('admin.medicines._form')
</form>
@endsection
