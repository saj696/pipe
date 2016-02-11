<?php echo csrf_field(); ?>


<?php /*<div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">*/ ?>
    <?php /*<?php echo e(Form::label('name', 'Name', ['class'=>'col-md-3 control-label'])); ?>*/ ?>
    <?php /*<div class="col-md-7">*/ ?>
        <?php /*<?php echo e(Form::text('name', null,['class'=>'form-control'])); ?>*/ ?>
        <?php /*<?php if($errors->has('name')): ?>*/ ?>
            <?php /*<span class="help-block">*/ ?>
                <?php /*<strong><?php echo e($errors->first('name')); ?></strong>*/ ?>
            <?php /*</span>*/ ?>
        <?php /*<?php endif; ?>*/ ?>
    <?php /*</div>*/ ?>
<?php /*</div>*/ ?>

<?php /*<div class="form-group<?php echo e($errors->has('type') ? ' has-error' : ''); ?>">*/ ?>
    <?php /*<?php echo e(Form::label('type', 'Type', ['class'=>'col-md-3 control-label'])); ?>*/ ?>
    <?php /*<div class="col-md-7">*/ ?>
        <?php /*<?php echo e(Form::select('type', $types, null,['class'=>'form-control', 'id'=>'type', 'placeholder'=>'Select'])); ?>*/ ?>
        <?php /*<?php if($errors->has('type')): ?>*/ ?>
            <?php /*<span class="help-block">*/ ?>
                <?php /*<strong><?php echo e($errors->first('type')); ?></strong>*/ ?>
            <?php /*</span>*/ ?>
        <?php /*<?php endif; ?>*/ ?>
    <?php /*</div>*/ ?>
<?php /*</div>*/ ?>

<?php /*<div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">*/ ?>
    <?php /*<?php echo e(Form::label('status', 'Status', ['class'=>'col-md-3 control-label'])); ?>*/ ?>
    <?php /*<div class="col-md-7">*/ ?>
        <?php /*<?php echo e(Form::select('status', Config::get('common.status'), null,['class'=>'form-control', 'placeholder'=>'Select'])); ?>*/ ?>
        <?php /*<?php if($errors->has('status')): ?>*/ ?>
            <?php /*<span class="help-block">*/ ?>
                <?php /*<strong><?php echo e($errors->first('status')); ?></strong>*/ ?>
            <?php /*</span>*/ ?>
        <?php /*<?php endif; ?>*/ ?>
    <?php /*</div>*/ ?>
<?php /*</div>*/ ?>

<?php /*<div class="form-actions">*/ ?>
    <?php /*<div class="row">*/ ?>
        <?php /*<div class="col-md-offset-3 col-md-9">*/ ?>
        <?php /*<?php echo e(Form::submit($submitText, ['class'=>'btn green'])); ?>*/ ?>
        <?php /*</div>*/ ?>
    <?php /*</div>*/ ?>
<?php /*</div>*/ ?>

<table class="table table-bordered" id="adding_elements">
    <thead>
        <tr>
            <th>Date</th>
            <th>Material</th>
            <th>Usage</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <div>
                    <?php echo e(Form::text('date[]', null,['class'=>'form-control quantity'])); ?>

                </div>
            </td>

            <td>
                <div>
                    <?php echo e(Form::text('material_id[]', null,['class'=>'form-control quantity'])); ?>

                </div>
            </td>

            <td>
                <?php echo e(Form::text('usage[]', null,['class'=>'form-control quantity'])); ?>

            </td>
            <td style="min-width: 25px;">

            </td>
        </tr>
    </tbody>
</table>

<table class="table">
    <tr>
        <td class="pull-right" style="border: 0px;">
            <img style="width: 25px; height: 25px;" src="<?php echo e(URL::asset('public/image/plus.png')); ?>" onclick="RowIncrement()" />
        </td>
    </tr>
</table>

<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
        <?php echo e(Form::submit($submitText, ['class'=>'btn green'])); ?>

        </div>
    </div>
</div>

<script type="text/javascript">

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
        var img_url="<?php echo e(URL::asset('public/image/xmark.png')); ?>";
        var table = document.getElementById('adding_elements');
        var rowCount = table.rows.length;
        //alert(rowCount);
        var row = table.insertRow(rowCount);
        row.id = "T" + ExId;
        row.className = "tableHover";
        //alert(row.id);
        var cell1 = row.insertCell(0);
        cell1.innerHTML = "<input type='text' name='date[]' class='form-control quantity'/>";
        var cell1 = row.insertCell(1);
        cell1.innerHTML = "<input type='text' name='material_id[]' class='form-control quantity'/>";
        var cell1 = row.insertCell(2);
        cell1.innerHTML = "<input type='text' name='usage[]' class='form-control quantity'/>"+
                "<input type='hidden' id='elmIndex[]' name='elmIndex[]' value='" + ExId + "'/>";
        cell1.style.cursor = "default";
        cell1 = row.insertCell(3);
        cell1.innerHTML = "<img style='width: 25px; height: 25px;'  onclick=\"RowDecrement('adding_elements', 'T"+ExId+"')\" src='<?php echo e(URL::asset('public/image/xmark.png')); ?>' />";
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