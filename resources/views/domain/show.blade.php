@extends('layouts.app')

@section("content")
@include('flash::message')
<div class="container-lg">
        <h1 class="mt-5 mb-3">Site: {{ $domain->name }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                                    <tbody><tr>
                        <td>id</td>
                        <td>{{ $domain->id }}</td>
                    </tr>
                                    <tr>
                        <td>name</td>
                        <td>{{ $domain->name }}</td>
                    </tr>
                                    <tr>
                        <td>created_at</td>
                        <td>{{ $domain->created_at }}</td>
                    </tr>
                                    <tr>
                        <td>updated_at</td>
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
                  <tr>
                    <td>{{ $check->id }}</td>
                    <td>{{ Illuminate\Support\Str::of($check->h1)->limit(30) }}</td>
                    <td>{{ $check->status_code }}</td>
                    <td>{{ $check->created_at }}</td>
                    <td>{{ Illuminate\Support\Str::of($check->keywords)->limit(30) }}</td>
                    <td>{{ Illuminate\Support\Str::of($check->description)->limit(30) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            </div>
</div>
@endsection