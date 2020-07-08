@extends('layouts.app')

@section("content")
@include('flash::message')
<div class="container-lg">
        <h1 class="mt-5 mb-3">Site: {{ $domain->name }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                                    <tbody><tr>
                        <th>id</th>
                        <td>{{ $domain->id }}</td>
                    </tr>
                                    <tr>
                        <th>name</th>
                        <td>{{ $domain->name }}</td>
                    </tr>
                                    <tr>
                        <th>created_at</th>
                        <td>{{ $domain->created_at }}</td>
                    </tr>
                                    <tr>
                        <th>updated_at</th>
                        <td>{{ $domain->updated_at }}</td>
                    </tr>
                            </tbody></table>
        </div>
        <h2 class="mt-5 mb-3">Checks</h2>
        <form method="post" action="{{route('domains.checks.store', $domain->id)}}">
        @csrf
          <input type="submit" class="btn btn-primary" value="Run check">
        </form>
                    <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
            <tbody>
                @foreach ($domainChecks as $check)
                    @if ($loop->first)
                        <tr>
                          <th> ID </th>
                          <th> Header 1 </th>
                          <th>Status Code</th>
                          <th>Created At</th>
                          <th>Keywords</th>
                          <th>Description</th>
                        </tr>
                    @endif
                  <tr>
                    <td>{{ $check->id }}</td>
                    <td>{{ $check->h1 }}</td>
                    <td>{{ $check->status_code }}</td>
                    <td>{{ $check->created_at }}</td>
                    <td>{{ $check->keywords }}</td>
                    <td>{{ $check->description  }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            </div>
</div>
@endsection