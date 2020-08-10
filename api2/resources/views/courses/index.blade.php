@extends('layouts.app')


@section('content')

<div class="row">

    <div class="col-lg-12 margin-tb">

        <div class="pull-left">

            <h2>Courses</h2>

        </div>

        <div class="pull-right">

            @can('course-create')

            <a class="btn btn-success" href="{{ route('courses.create') }}"> Create New Course</a>

            @endcan

        </div>

    </div>

</div>


@if ($message = Session::get('success'))

<div class="alert alert-success">

    <p>{{ $message }}</p>

</div>

@endif


<table class="table table-bordered">

    <tr>

        <th>No</th>

        <th>Name</th>

        <th>Description</th>

        <th width="280px">Action</th>

    </tr>

    @foreach ($courses as $course)

    <tr>

        <td>{{ ++$i }}</td>

        <td>{{ $course->name }}</td>

        <td>{{ $course->description }}</td>

        <td>

            <form action="{{ route('courses.destroy',$course->id) }}" method="POST">

                <a class="btn btn-info" href="{{ route('courses.show',$course->id) }}">Show</a>

                @can('course-edit')

                <a class="btn btn-primary" href="{{ route('courses.edit',$course->id) }}">Edit</a>

                @endcan


                @csrf

                @method('DELETE')

                @can('course-delete')

                <button type="submit" class="btn btn-danger">Delete</button>

                @endcan

            </form>

        </td>

    </tr>

    @endforeach

</table>


{!! $courses->links() !!}

@endsection