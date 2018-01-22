<template>

    <li style="margin-bottom: 0px;">
        <div class="row" v-if="editindex !== tindex">
            <div class="col-1 text-right">
                <div style="margin-bottom: 6px;">
                    <input type="checkbox" 
                        @click="toggleCheckbox(todo, tindex)" 
                        v-model="todo.is_complete"
                        v-bind:true-value="1"
                        v-bind:false-value="0">
                </div>
            </div>
            <div class="col-9">
                <div class="todo-item">
                    <router-link :to="'/projects/' + $route.params.projectid + '/todolists/' + list.ID + '/todos/' + todo.ID" class="link-style" tag="span" :class="{ completed: is_complete }">
                        {{todo.todo}}, due date: dateobj, assigned To {{todo.assignee_name}},
                        <span v-if="fileCount > 0">fileIcon</span>
                    </router-link>
                </div>
            </div>
            <div class="col-2">
                <div class="actions text-center" v-if="isShowEdit">
                    <span @click="showEditForm( todo, tindex )">
                        <a style="cursor: pointer">Edit</a>
                    </span>
                    <span class="trash" @click="deleteTodo(todo, tindex)">| 
                        <a style="color: #D54E21;cursor: pointer;">Delete</a>
                    </span>
                </div> 
            </div>
        </div>

        <div class="row" v-if="editindex === tindex">
            <div class="col-1"></div>
            <div class="col-10">
                <div class="add_form_style">
                    <form>
                        <div class="todo_name inline">
                            <input type="text" 
                                name="todo_text" 
                                v-model="todoName" 
                                class="form-control" 
                                placeholder="add todo . . ." 
                                v-focus
                                @keyup.esc="hideTodoForm">
                            <span class="form-note"><i>*required field</i></span>
                        </div>
                        
                        <div class="inline">
                            <input style="vertical-align: middle;" type="submit" @click.prevent="updateTodo(todo)" name="add_todo" class="button button-primary" value="Update">
                            <input style="vertical-align: middle;" type="submit" @click.prevent="cancelEdit" class="button button-default" value="Cancel">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </li>
</template>

<style>
    .todo-item {
        /*margin-bottom: 8px;*/
        /*line-height: 124%;*/
    }
    .completed {
        text-decoration: line-through;
        font-style: italic;
        color: #a2a2a2;
    }
</style>

<script>
    export default {
        props: ['todo', 'tindex', 'list'],

        data() {
            return {
                is_complete: +this.todo.is_complete, // '+' for string to integer conversion
                editindex : -1,
                todoName: '',
                currentUser: '',
                fileCount: 0,
            }
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },
        computed: {
            isShowEdit: function() {
                var vm = this;
                return (vm.currentUser.roles[0] === 'administrator' && !vm.is_complete) || 
                        (!vm.is_complete && (vm.currentUser.data.ID === vm.todo.userID));
            }
        },
        methods: {
            showEditForm: function( todoObj, index ) {
                this.todoName = todoObj.todo;
                this.editindex = index;
            },

            cancelEdit: function() {
                this.editindex = -1;
            },

            updateTodo: function( todoObject ) {

                var vm = this,
                    todo,
                    data = {
                        action : 'fpm-insert-todo',
                        nonce : fpm.nonce,
                        todo: vm.todoName,
                        user_id: vm.currentUser.data.ID,
                        user_name: vm.currentUser.data.display_name,
                        list_id: vm.list.ID,
                        project_id: vm.$route.params.projectid,
                        todo_id: todoObject.ID
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        todoObject.todo = vm.todoName;
                        // todo = resp.data.todo;
                        // todo.todo = vm.todoName;
                        // todo.is_complete = '0';
                        vm.todoName = '';
                        vm.editindex = -1;
                    } else {
                        vm.message = resp.data;
                    }
                });
            },
            
            toggleCheckbox: function( todo, tindex ) {
                var self = this,
                    data;
                if ( todo.is_complete ) {
                    data = {
                        action : 'fpm-complete-todo',
                        nonce : fpm.nonce,
                        todo_id: todo.ID,
                        is_complete: todo.is_complete
                    };

                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            self.todo.is_complete = todo.is_complete;
                            self.is_complete = todo.is_complete;
                        } else {
                            self.message = resp.data;
                        }
                    });
                } else {
                    data = {
                        action : 'fpm-complete-todo',
                        nonce : fpm.nonce,
                        todo_id: todo.ID,
                        is_complete: todo.is_complete
                    };

                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            self.todo.is_complete = todo.is_complete;
                            self.is_complete = todo.is_complete;
                        } else {
                            self.message = resp.data;
                        }
                    });
                }
            },

            deleteTodo: function(todo, tindex) {

                if (confirm("Are you sure ??")) {
                    var self = this,
                        todoInfo,
                        data = {
                            action : 'fpm-delete-todo',
                            nonce : fpm.nonce,
                            todo_id: todo.ID
                        };
                        
                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            
                            self.list.todos.splice( tindex, 1 );

                        } else {
                            
                        }
                    });
                }
            }
        },

        mounted() {
            // this.is_complete = +this.todo.is_complete;
            console.log('Component mounted');
        },

        created() {
            this.currentUser = fpm.currentUserInfo;
            if (this.todo.file_ids) {
                this.fileCount = +this.todo.file_ids.charAt(2);
            }
        }
    }
</script>