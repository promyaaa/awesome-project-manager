<template>

    <li style="margin-bottom: 0px; cursor: pointer;">
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
                        {{todo.todo}}
                        <span v-if="todo.formatted_due_date">
                            | <i class="fa fa-calendar" aria-hidden="true"></i> {{todo.formatted_due_date}}
                        </span>
                        <span v-if="todo.assignee_name">
                            | <i class="fa fa-user" aria-hidden="true"></i> {{todo.assignee_name}}
                        </span>
                        <span v-if="fileCount > 0">
                            | <i class="fa fa-file" aria-hidden="true"></i>
                        </span>
                    </router-link>
                </div>
            </div>
            <div class="col-2">
                <div class="actions text-center" v-if="isShowEdit">
                    <span @click="showEditForm( todo, tindex )" v-tooltip :title="i18n.edit">
                        <a style="cursor: pointer"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    </span>
                    <span class="trash" @click="deleteTodo(todo, tindex)" v-tooltip :title="i18n.delete">|
                        <a style="color: #D54E21;cursor: pointer;"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </span>
                </div>
            </div>
        </div>

        <div class="row" v-if="editindex === tindex">
            <div class="col-1"></div>
            <div class="col-10">
                <div class="add_form_style">

                    <div class="todo_name inline">
                        <input type="text"
                            name="todo_text"
                            v-model="todoName"
                            class="form-control"
                            :placeholder="i18n.add_todo_placeholder"
                            v-focus
                            required
                            @keyup.esc="hideTodoForm">
                    </div>
                    <div>
                        <select v-model="selected" class="form-control">
                            <option disabled value="">{{ i18n.select_user }}</option>
                            <option v-for="option in users" v-bind:value="{ID : option.ID, assignee : option.display_name}">
                                {{ option.display_name }}
                            </option>
                        </select>
                    </div>
                    <date-picker id="update-duedate" v-model="updateDueDate"></date-picker>

                    <div class="inline">
                        <input style="vertical-align: middle;" type="submit" @click.prevent="updateTodo(todo)" name="add_todo" class="button button-primary" :value="i18n.update">
                        <input style="vertical-align: middle;" type="submit" @click.prevent="cancelEdit" class="button button-default" :value="i18n.cancel">
                    </div>

                </div>
            </div>
        </div>
    </li>
</template>

<style>
    .completed {
        text-decoration: line-through;
        font-style: italic;
        color: #a2a2a2;
    }
</style>

<script>
    import DatePicker from './DatePickerComponent.vue';
    import store from '../../store';
    export default {
        props: ['todo', 'tindex', 'list', 'i18n' ],
        components: {
            DatePicker
        },
        data() {
            return {
                is_complete: +this.todo.is_complete, // '+' for string to integer conversion
                editindex : -1,
                todoName: '',
                currentUser: '',
                fileCount: 0,
                selected: '',
                updateDueDate: ''
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
                this.updateDueDate = todoObj.due_date;
                this.selected = {
                    ID: todoObj.assigneeID, assignee: todoObj.assignee_name
                }
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
                        todo_id: todoObject.ID,
                        list_id: vm.list.ID,
                        project_id: vm.$route.params.projectid,
                        user_id: vm.currentUser.data.ID,
                        user_name: vm.currentUser.data.display_name,
                        assignee_id: vm.selected.ID,
                        assignee_name: vm.selected.assignee,
                        attachments: todoObject.attachmentIDs,
                        due_date: vm.updateDueDate ? vm.updateDueDate : ''
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    console.log(resp);
                    if ( resp.success ) {
                        todoObject.todo = vm.todoName;
                        todoObject.formatted_due_date = resp.data.todo.formatted_duedate;
                        todoObject.assigneeID = vm.selected.ID;
                        todoObject.assignee_name = vm.selected.assignee;

                        vm.todoName = '';
                        vm.editindex = -1;
                    } else {
                        vm.message = resp.data;
                    }
                });
            },

            toggleCheckbox: function( todo, tindex ) {
                var self = this,
                    data = {
                        action : 'fpm-complete-todo',
                        nonce : fpm.nonce,
                        todo: todo.todo,
                        todo_id: todo.ID,
                        is_complete: todo.is_complete,
                        list_id: todo.listID,
                        project_id: todo.projectID,
                        user_id: todo.userID,
                        user_name: todo.user_name
                    };
                if ( todo.is_complete ) {

                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            self.todo.is_complete = todo.is_complete;
                            self.is_complete = todo.is_complete;
                        } else {
                            self.message = resp.data;
                        }
                    });
                } else {

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
                            todo_id: todo.ID,
                            todo: todo.todo,
                            project_id: todo.projectID
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

        },

        created() {
            var vm = this,
                projectid,
                key;
            vm.currentUser = fpm.currentUserInfo;
            if ( vm.todo.file_ids ) {
                vm.fileCount = +vm.todo.file_ids.charAt(2);
            }

            projectid = vm.$route.params.projectid;

            key = projectid + '-users';
            vm.users = JSON.parse(localStorage.getItem(key));

            if (!vm.users) {

                localStorage.setItem('pid', projectid);

                store.fetchUsers( projectid ).then(function(resp){
                    vm.users = resp.data;
                    localStorage.setItem(key, JSON.stringify(vm.users));
                });
            }
        }
    }
</script>