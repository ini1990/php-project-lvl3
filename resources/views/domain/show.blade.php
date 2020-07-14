@extends('layouts.app')

@section("content")
<div class="container-lg">
  <h1 class="mt-5 mb-3">Site: {{ $domain->name }}</h1>
  <div class="table-responsive">
    <table class="table table-bordered table-hover text-nowrap">
      <tbody>
        <tr>
          <th>created_at</th>
          <td>{{ $domain->created_at }}</td>
        </tr>
        <tr>
          <th>updated_at</th>
          <td>{{ $domain->updated_at }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <h2 class="mt-5 mb-3">Checks</h2>
  <form method="post" action="{{route('domains.checks.store', $domain->id)}}">
    @csrf
    <input type="submit" class="btn btn-primary" value="Run check">
  </form>
  <div class="table-responsive">
    <table class="table table-bordered">
      <tbody>
        @foreach ($domainChecks as $check)
        @if ($loop->first)
        <tr>
          <th style="width: 5%"> ID </th>
          <th style="width: 19%"> Header 1 </th>
          <th style="width: 6%"> Status </th>
          <th style="width: 16%">Created At</th>
          <th style="width: 28%">Keywords</th>
          <th style="width: 27%">Description</th>
        </tr>
        @endif
        <tr>
          <td>{{ $check->id }}</td>
          <td class="text-truncate">{{ $check->h1 }}</td>
          <td>{{ $check->status_code }}</td>
          <td>{{ $check->created_at }}</td>
          <td class="text-truncate">{{ $check->keywords }}</td>
          <td class="text-truncate">{{ $check->description  }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection