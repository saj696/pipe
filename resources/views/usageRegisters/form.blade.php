{!! csrf_field() !!}

<table class="table table-bordered">
    <tr>
        <td>
            <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                {{ Form::label('date', 'Date', ['class'=>'col-md-4 control-label']) }}
                <div class="col-md-4">
                    {{ Form::text('date', null,['class'=>'form-control col-md-2']) }}
                    @if ($errors->has('date'))
                        <span class="help-block">
                            <strong>{{ $errors->first('date') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </td>
    </tr>
</table>

<table class="table table-bordered" id="adding_elements">
    <thead>
        <tr>
            <th>Material</th>
            <th>Usage</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                {{ Form::select('material_id[]', $materials, null,['class'=>'form-control', 'id'=>'material_id', 'placeholder'=>'Select', 'required'=>'required']) }}
            </td>

            <td>
                {{ Form::text('usage[]', null,['class'=>'form-control quantity', 'required'=>'required']) }}
            </td>
            <td style="width: 25px; height: 34px;">

            </td>
        </tr>
    </tbody>
</table>

<table class="table">
    <tr>
        <td class="pull-right" style="border: 0px;">
            <img style="width: 25px; height: 25px;" src="{{ URL::asset('public/image/plus.png') }}" onclick="RowIncrement()" />
        </td>
    </tr>
</table>

<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
        {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
        </div>
    </div>
</div>

<script type="text/javascript">

    $(function() {
        $( "#date" ).datepicker();
    });

    jQuery(document).ready(function()
    {
        $(document).on("keyup", ".quantity", function()
        {
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });
    });

    var ExId = 0;
    function RowIncrement()
    {
        var img_url="{{ URL::asset('public/image/xmark.png') }}";
        var table = document.getElementById('adding_elements');
        var rowCount = table.rows.length;
        //alert(rowCount);
        var row = table.insertRow(rowCount);
        row.id = "T" + ExId;
        row.className = "tableHover";
        //alert(row.id);
        var cell1 = row.insertCell(0);

        cell1.innerHTML = "<select name='material_id[]' id='material_id" + ExId + "' class='form-control' required='required'>\n\
        <option value=''>Select</option>\n\
        <?php
        foreach ($materials as $id=>$material)
            echo "<option value='" . $id. "'>" . $material . "</option>";
        ?>";
        var cell1 = row.insertCell(1);
        cell1.innerHTML = "<input type='text' name='usage[]' class='form-control quantity' required='required' />"+
        "<input type='hidden' id='elmIndex[]' name='elmIndex[]' value='" + ExId + "'/>";
        cell1.style.cursor = "default";
        cell1 = row.insertCell(2);
        cell1.innerHTML = "<img style='width: 25px; height: 25px;'  onclick=\"RowDecrement('adding_elements', 'T"+ExId+"')\" src='{{ URL::asset('public/image/xmark.png') }}' />";
        cell1.style.cursor = "default";
        ExId = ExId + 1;
    }

    function RowDecrement(adding_elements, id)
    {
        try {
            var table = document.getElementById(adding_elements);
            for (var i = 1; i < table.rows.length; i++)
            {
                if (table.rows[i].id == id)
                {
                    table.deleteRow(i);
                }
            }
        }
        catch (e) {
            alert(e);
        }
    }
</script>