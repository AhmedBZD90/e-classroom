@extends('layouts.app')


@section('content')

<div class="row">

    <div class="col-lg-12 margin-tb">

        <div class="pull-left">

            <h2> Show Courses</h2>

        </div>

        <div class="pull-right">

            <a class="btn btn-primary" href="{{ route('courses.index') }}"> Back</a>

        </div>

    </div>

</div>


<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-12">

        <div class="form-group">

            <strong>Name:</strong>

            {{ $course->name }}

        </div>

    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">

        <div class="form-group">

            <strong>Description:</strong>

            {{ $course->description }}

        </div>

    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Teacher:</strong>
            {{ utf8_encode($teacher_name) }}
        </div>
    </div>

    <strong>Qr Code:</strong>
    <div class="visible-print text-center">
        {!! QrCode::size(300)->generate($qr_code); !!}
        <p>Scan me to mark your presence.</p>
    </div>

</div>

@endsection
