@extends('layouts.master')
@section('title', ' Product Details')
@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="main-card card mb-3">
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ $msg }}
                    </div>
                    <div class="tab-content" id="ex1-content">
                      <div class="tab-pane fade show active" id="tab-product-details" role="tabpanel" aria-labelledby="ex1-tab-1">
                        {!! Form::open(['route' => 'price.store']) !!}

                            <div class="mb-3">
                                {{ Form::label('title', 'Title', ['class'=>'form-label']) }}
                                {{ Form::text('name', null, array('class' => 'form-control')) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('probability', 'Probability', ['class'=>'form-label']) }}
                                {{ Form::number('probability_percentage', null, array('class' => 'form-control','min' => '0','max' => '100', 'placeholder' => '0 - 100','step' => '0.01')) }}
                                @error('probability_percentage')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                            </div>


                            {{ Form::submit('Save', array('class' => 'btn btn-success')) }}

                        {{ Form::close() }}             
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script>
    
</script>
@endpush