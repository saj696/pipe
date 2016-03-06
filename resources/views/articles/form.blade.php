{!! csrf_field() !!}

<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
    {{ Form::label('title', 'Title', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('title', null,['class'=>'form-control']) }}
        @if ($errors->has('title'))
            <span class="help-block">
                <strong>{{ $errors->first('title') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('body') ? ' has-error' : '' }}">
    {{ Form::label('body', 'Body', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::textarea('body', null,['class'=>'form-control', 'rows'=>'3']) }}
        @if ($errors->has('body'))
            <span class="help-block">
                <strong>{{ $errors->first('body') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('published_at') ? ' has-error' : '' }}">
    {{ Form::label('published_at', 'Published On', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::input('date', 'published_at', date('Y-m-d'),['class'=>'form-control']) }}
        @if ($errors->has('published_at'))
            <span class="help-block">
                <strong>{{ $errors->first('published_at') }}</strong>
            </span>
        @endif
    </div>
</div>


<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            {{ Form::submit($submitText, ['class'=>'btn green']) }}
        </div>
    </div>
</div>