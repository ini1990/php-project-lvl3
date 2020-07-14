@extends('layouts.app')
@section('content')
<div class="container-lg">
  <h1 class="mt-5 mb-3">Domains</h1>
  <div class="table-responsive">
    <table class="table table-bordered table-hover text-nowrap">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Created</th>
          <th>Last Check</th>
          <th>Status Code</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($domains as $domain)
        <tr>
          <th> {{ $domain->id }} </th>
          <td><a href="{{ route('domains.show', $domain->id)}}">{{ $domain->name }}</a></td>
          <td>{{ $domain->created_at }}</td>
          <td>{{ $lastChecks[$domain->id]->created_at ?? "" }}</td>
          <td>{{ $lastChecks[$domain->id]->status_code ?? "" }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection