@extends('layouts.app')
@section('content')
    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> Edit: User Group Role
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-warning pull-right" href="{{ url('/roles' )}}">Back</a>
            </div>
        </div>
        <?php
        $modules=[];
        foreach($tasks as $task)
        {
            $modules[$task->module_id]['component_id']=$task->component_id;
            if(Auth::user()->user_group_id==1)
            {
                $modules[$task->module_id]['component_name']=$task->component->name_en;
                $modules[$task->module_id]['module_name']=$task->module->name_en;
            }
            else
            {
                $modules[$task->module_id]['component_name']=$task->component_name;
                $modules[$task->module_id]['module_name']=$task->module_name;
            }

            $modules[$task->module_id]['module_id']=$task->module_id;
            $modules[$task->module_id]['tasks'][]=$task;
        }

        $components=[];
        foreach($modules as $module)
        {
            $components[$module['component_id']]['component_id']=$module['component_id'];
            $components[$module['component_id']]['component_name']=$module['component_name'];
            $components[$module['component_id']]['modules'][]=$module;
        }
//        echo '<pre>';
//        print_r($components);
//        echo '</pre>';
        ?>

        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['method'=>'PATCH','action'=>['User\RolesController@update', $id]]) }}
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo 'Component'; ?></th>
                                <th><?php echo 'Module'; ?></th>
                                <th><?php echo 'Task'; ?></th>
                                <th><?php echo 'List'; ?></th>
                                <th><?php echo 'View'; ?></th>
                                <th><?php echo 'Add'; ?></th>
                                <th><?php echo 'Edit'; ?></th>
                                <th><?php echo 'Delete'; ?></th>
                                <th><?php echo 'Print'; ?></th>
                                <th><?php echo 'Report'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(sizeof($components)>0)
                        {
                            foreach($components as $component)
                            {
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" data-id='<?php echo $component['component_id']; ?>' class="component_name"><?php echo $component['component_name'];?>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                                foreach($component['modules'] as $module)
                                {
                                ?>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" data-id='<?php echo $module['module_id']; ?>' class="module_name component_action_<?php echo $component['component_id'];?>"><?php echo $module['module_name'];?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <?php
                                foreach($module['tasks'] as $task)
                                {
                                ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][ugr_id]" value="<?php echo isset($roles->ugr_id[isset($task->id)?$task->id:$task->task_id])?$roles->ugr_id[isset($task->id)?$task->id:$task->task_id]:0;?>">
                                        <input type="hidden" name="tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][task_id]" value="<?php echo isset($task->id)?$task->id:$task->task_id;?>">
                                        <input type="hidden" name="tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][module_id]" value="<?php echo $task->module_id;?>">
                                        <input type="hidden" name="tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][component_id]" value="<?php echo $task->component_id;?>">
                                    </td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" data-id='<?php echo isset($task->id)?$task->id:$task->task_id;?>' class="task_name module_action_<?php echo $module['module_id'];?> component_action_<?php echo $component['component_id'];?>"><?php echo isset($task->name_en)?$task->name_en:$task->task_name;?>
                                    </td>
                                    <td>
                                        <?php
                                        if($task->list)
                                        {
                                        ?>
                                        <input title="list" class="task_action_<?php echo isset($task->id)?$task->id:$task->task_id;?> module_action_<?php echo $module['module_id'];?> component_action_<?php echo $component['component_id'];?>" type="checkbox" <?php if(in_array(isset($task->id)?$task->id:$task->task_id,$roles->list)){echo 'checked';}?> value="1" name='tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][list]'>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($task->view)
                                        {
                                        ?>
                                        <input title="view" class="task_action_<?php echo isset($task->id)?$task->id:$task->task_id;?> module_action_<?php echo $module['module_id'];?> component_action_<?php echo $component['component_id'];?>" type="checkbox" <?php if(in_array(isset($task->id)?$task->id:$task->task_id,$roles->view)){echo 'checked';}?> value="1" name='tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][view]'>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($task->add)
                                        {
                                        ?>
                                        <input title="add" class="task_action_<?php echo isset($task->id)?$task->id:$task->task_id;?> module_action_<?php echo $module['module_id'];?> component_action_<?php echo $component['component_id'];?>" type="checkbox" <?php if(in_array(isset($task->id)?$task->id:$task->task_id,$roles->add)){echo 'checked';}?> value="1" name='tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][add]'>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($task->edit)
                                        {
                                        ?>
                                        <input title="edit" class="task_action_<?php echo isset($task->id)?$task->id:$task->task_id;?> module_action_<?php echo $module['module_id'];?> component_action_<?php echo $component['component_id'];?>" type="checkbox" <?php if(in_array(isset($task->id)?$task->id:$task->task_id,$roles->edit)){echo 'checked';}?> value="1" name='tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][edit]'>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($task->delete)
                                        {
                                        ?>
                                        <input title="delete" class="task_action_<?php echo isset($task->id)?$task->id:$task->task_id;?> module_action_<?php echo $module['module_id'];?> component_action_<?php echo $component['component_id'];?>" type="checkbox" <?php if(in_array(isset($task->id)?$task->id:$task->task_id,$roles->delete)){echo 'checked';}?> value="1" name='tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][delete]'>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($task->report)
                                        {
                                        ?>
                                        <input title="report" class="task_action_<?php echo isset($task->id)?$task->id:$task->task_id;?> module_action_<?php echo $module['module_id'];?> component_action_<?php echo $component['component_id'];?>" type="checkbox" <?php if(in_array(isset($task->id)?$task->id:$task->task_id,$roles->report)){echo 'checked';}?> value="1" name='tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][report]'>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($task->print)
                                        {
                                        ?>
                                        <input title="print" class="task_action_<?php echo isset($task->id)?$task->id:$task->task_id;?> module_action_<?php echo $module['module_id'];?> component_action_<?php echo $component['component_id'];?>" type="checkbox" <?php if(in_array(isset($task->id)?$task->id:$task->task_id,$roles->print)){echo 'checked';}?> value="1" name='tasks[<?php echo isset($task->id)?$task->id:$task->task_id;?>][print]'>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                }
                            }
                            ?>
                            <?php
                            }
                            ?>
                            <tr>
                                <td colspan="20" class="text-center">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-6">
                                            {{ Form::submit('Save', ['class'=>'btn green']) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td colspan="20" class="text-center alert-danger">
                                <?php echo "No Data Found!"; ?>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function ()
        {
            $(document).on("click",'.task_name',function()
            {
                if($(this).is(':checked'))
                {
                    $('.task_action_'+$(this).attr('data-id')).prop('checked', true);
                }
                else
                {
                    $('.task_action_'+$(this).attr('data-id')).prop('checked', false);
                }
            });
            $(document).on("click",'.module_name',function()
            {
                if($(this).is(':checked'))
                {
                    $('.module_action_'+$(this).attr('data-id')).prop('checked', true);

                }
                else
                {
                    $('.module_action_'+$(this).attr('data-id')).prop('checked', false);
                }
            });
            $(document).on("click",'.component_name',function()
            {
                if($(this).is(':checked'))
                {
                    $('.component_action_'+$(this).attr('data-id')).prop('checked', true);

                }
                else
                {
                    $('.component_action_'+$(this).attr('data-id')).prop('checked', false);
                }
            });
        });
    </script>
@stop

