@extends('layouts.app')

@section('content')
<h2>Domains</h2>

<table>
  <thead>
    <tr>
      <th>id</th>
      <th>Name</th>
      <th>Create</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($domains as $domain)
      <tr>
        <th> {{ $domain->id }} </th>
        <td><a href="{{ route('domains.show', $domain->id)}}">{{ $domain->name }}</a></td>
        <td>{{ $domain->created_at }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
@endsection