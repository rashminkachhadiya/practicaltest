@extends('layouts.master')
@section('title', 'Price')
@section('content')
    <div class="app-page-title">
        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="d-inline-block ml-2">
                        <a href="{{ route('price.create') }}" class="btn btn-success"><i
                                class="glyphicon glyphicon-plus"></i>
                            Add Price
                        </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="manage_all"
                               class="align-middle mb-0 table table-borderless table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Title</th>
                                <th>Probability</th>
                                <th>Awarded</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h3 style="color: black;">Simulate</h3>
                    {!! Form::open(['route' => 'price.simulate']) !!}

                            <div class="mb-3">
                                {{ Form::label('title', 'Number of Prize', ['class'=>'form-label']) }}
                                {{ Form::number('num_entries', null, array('class' => 'form-control')) }}
                            </div>
                            {{ Form::submit('Simulate', array('class' => 'btn btn-success')) }}
                    {{ Form::close() }}  
                    <a href="{{ route('price.reset') }}" class="btn btn-info mt-1">     Reset
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container  mb-4">
        <div class="row">
            <div class="col-md-6">
                <h2 style="color: black;">Probability Settings</h2>
                <canvas id="probabilityChart"></canvas>
            </div>
            <div class="col-md-6">
                <h2 style="color: black;">Actual Rewards</h2>
                <canvas id="awardedChart"></canvas>
            </div>
        </div>
    </div>

    <style>
        
    </style>
    <script>
        $(function () {

            table = $('#manage_all').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": '{!! route('allPrice') !!}',
                    "type": "GET",
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                    },
                    "dataType": 'json'
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'probability_percentage', name: 'probability_percentage'},
                    {data: 'count_awarded', name: 'count_awarded'},
                    {data: 'action', name: 'action'}
                ],
                "autoWidth": false,
            });
            $('.dataTables_filter input[type="search"]').attr('placeholder', 'Type here to search...').css({
                'width': '220px',
                'height': '30px'
            });

        });

        $("#manage_all").on("click", ".delete", function () {
                var id = $(this).attr('id');
                $.ajax({
            url: 'price' + '/' + id,
            type: 'DELETE',
            headers: {
                "X-CSRF-TOKEN": CSRF_TOKEN,
            },
            "dataType": 'json',
            success: function (data) {
                if (data.type === 'success') {
                    table.ajax.reload(null, false);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log("Error deleting!");
            }
        });
            });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('probabilityChart').getContext('2d');
        var myChart = new Chart(ctx, {
             type: 'doughnut',
            data: {
                labels: @json($data['labels']),
                datasets: [{
                    data: @json($data['probability']),
                    borderWidth: 1
                }]
            },
        });
    </script>

    <script>
        var ctx = document.getElementById('awardedChart').getContext('2d');
        var myChart = new Chart(ctx, {
             type: 'doughnut',
            data: {
                labels: @json($data['labels']),
                datasets: [{
                    data: @json($data['actual']),
                    borderWidth: 1
                }]
            },
        });
    </script>
@stop
