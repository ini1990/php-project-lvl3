@extends('layouts.app')

@section('content')
<h2>Page Analizer</h2>

<form action="{{ route('domains.store') }}" method="POST">
    {{ csrf_field() }}
  <input type="text" placeholder="http://example.com" name="name" >
  <button type="submit">Check</button>
</form>
@endsection