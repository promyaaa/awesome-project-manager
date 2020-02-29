<template>
    <div>
        <div class="subtask-list" v-for="(task, index) in subtasks" style="display:flex;">
            <div class="custom-checkbox check-small">
                <input type="checkbox"
                        :id="'subtask'+index" 
                        @click="toggleCheckbox(task)"
                        v-model="task.is_complete"
                        v-bind:true-value="1"
                        v-bind:false-value="0">
                    <label class="label-small" :for="'subtask'+index"></label>
            </div>
            <div style="width:90%">
                <div style="line-height: 20px" 
                    :class="{ completed: +task.is_complete }">
                    {{task.subtask}}
                </div> 
            </div>
            <div style="width:5%;text-align: right;cursor:pointer;color:#ddd" @click="deleteTask(task, index)">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
        </div>
        <div>
            <input type="text"
                class="subtask-form-control" 
                v-model.trim="subtask" 
                :placeholder="i18n.add_subtask_placeholder"
                @keyup.enter="addSubTask">
                <span class="small-note"><i>{{i18n.subtask_small_label}}</i></span>
        </div>
    </div>
</template>

<script>
    export default{
        props: ['todo', 'i18n', 'subtasks'],
        components: {

        },
        data() {
            return {
                subtask:'',
                // subtasks: [],
                is_complete: ''
            }
        },
        methods: {
            fetchSubtasks() {
                var vm = this,
                    data,
                    task,
                    i;

                data = {
                    action: 'fpm-get-subtasks',
                    nonce: fpm.nonce,
                    todo_id: vm.todo.ID
                }
                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        for( i=0; i < resp.data.length; i++ ) {
                            task = resp.data[i];
                            task.is_complete = +task.is_complete;
                            vm.subtasks.push(resp.data[i]);
                        }
                    }
                });

            },
            addSubTask() {
                var vm = this,
                    task = {},
                    data;
                data = {
                    action: 'fpm-create-subtask',
                    todo_id: vm.todo.ID,
                    subtask: vm.subtask,
                    list_id: vm.todo.listID,
                    project_id: vm.todo.projectID,
                    nonce: fpm.nonce
                };

                if(!vm.subtask) return;

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        task.ID = resp.data.ID;
                        task.subtask = vm.subtask;
                        vm.subtasks.push(task);
                        vm.subtask = '';
                    }
                });
            },

            toggleCheckbox( stask ) {
                console.log(this.$route.params.listid);
                var vm = this,
                    data = {
                        action : 'fpm-complete-subtask',
                        nonce : fpm.nonce,
                        todo_id: stask.todoID,
                        todo: vm.todo.todo,
                        subtask: stask.subtask,
                        subtask_id: stask.ID,
                        is_complete: stask.is_complete,
                        project_id: vm.$route.params.projectid,
                        list_id: vm.$route.params.listid,
                    };

                if ( stask.is_complete ) {
                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            // vm.stask.is_complete = stask.is_complete;
                        } else {
                            vm.message = resp.data;
                        }
                    });
                } else {
                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            // vm.stask.is_complete = stask.is_complete;
                        } else {
                            vm.message = resp.data;
                        }
                    });
                }
            },

            deleteTask(task, index) {
                if(confirm("Are you sure ??")) {
                    var vm = this,
                    data;
                    data = {
                        action: 'fpm-delete-subtask',
                        subtask_id: task.ID,
                        nonce: fpm.nonce
                    };

                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            vm.subtasks.splice(index, 1);
                        }
                    });
                }
            }
        },
    }
</script>

<style>
    .subtask-form-control {
        box-shadow: none !important;
        -webkit-box-shadow: none !important;
        border-radius: 3px;
        width: 100%;
        height: 28px;
    }
    .subtask-form-control:focus {
        box-shadow: none !important;
        -webkit-box-shadow: none !important;
        border-color: #ddd !important;
    }
    .subtask-list {
        padding-top: 8px;
        padding-bottom: 10px;
    }
    .small-note {
        font-size: 11px;
        color: #9a9a9a;
    }
</style>

