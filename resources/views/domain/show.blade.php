@extends('layouts.app')

@section("content")

<h2>Site: {{ $domain->name }}</h2>

<table>
  <thead>
    <tr>
      <th>id</th>
      <th>Name</th>
      <th>Create</th>
    </tr>
  </thead>
  <tbody>
      <tr>
        <th> {{ $domain->id }} </th>
        <td>{{ $domain->name }}</td>
        <td>{{ $domain->created_at }}</td>
      </tr>
  </tbody>
</table>
@endsection