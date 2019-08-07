<template>
    <div>
        <div class="container">
            <project-nav>
                <span><i class="fa fa-angle-right"></i></span>
                <router-link :to="'/projects/' + $route.params.projectid + '/todolists'" class="link-style t-d-none">
                    {{ i18n.todos }}
                </router-link>
                <span><i class="fa fa-angle-right"></i></span>
                <router-link :to="'/projects/' + $route.params.projectid + '/todolists/' + $route.params.listid" class="link-style t-d-none">
                    {{list.list_title | truncate('15')}}
                </router-link>
            </project-nav>
            <!-- <div class="row">
                <div class="col-12 text-center">
                    <router-link :to="'/projects/' + $route.params.projectid " class="link-style inline-block" tag="h3">
                        <a>{{project.project_title}}</a>
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/todolists'" class="link-style inline-block" tag="h4">
                        <a><i class="fa fa-long-arrow-right p-l-10 p-r-10" aria-hidden="true"></i>{{ i18n.todos }}</a>
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/todolists/' + $route.params.listid" class="link-style inline-block" tag="h4">
                        <a><i class="fa fa-long-arrow-right p-l-10 p-r-10" aria-hidden="true"></i>{{list.list_title}}</a>
                    </router-link>
                </div>
            </div> -->
            <div class="lists border-for-nav">
                <div class="row ">
                    <div class="col-12">
                        <div class="text-center" v-if="loading">
                            <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>
                        </div>
                        <div v-if="todoObject && !loading" class="single-todo">
                            <div>
                                <div v-if="isShowEdit && !editTodo">
                                    <button class="button button-default"
                                            @click="showTodoEdit(todoObject)">{{ i18n.edit }}</button>
                                    <span style="float:right" @click="deleteTodo(todoObject)">
                                        <a style="color: #d54e21;cursor:pointer;">{{ i18n.delete }}</a>
                                    </span>
                                </div>
                            </div>
                            <br>
                            <div v-if="!editTodo" class="todo-details-div">
                                <div>
                                    <h1>
                                        <input type="checkbox"
                                        @click="toggleCheckbox(todoObject)"
                                        v-model="todoObject.is_complete"
                                        v-bind:true-value="1"
                                        v-bind:false-value="0">
                                        <span :class="{ completed: is_complete }">{{todoObject.todo}}</span>
                                    </h1>
                                </div>
                                <div class="row todo-info">
                                    <div class="col-3 text-right">
                                        <strong style="padding-right: 15%">{{ i18n.assign_to_label }}</strong>
                                    </div>
                                    <div class="col-9">
                                        <div>
                                            <img :src="todoObject.avatar_url"
                                                alt=""
                                                class="small-round-image"
                                                style="margin-right: 7px; margin-bottom: -3px;">
                                                {{todoObject.assignee_name}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row todo-info">
                                    <div class="col-3 text-right todo-info-title">
                                        <strong style="padding-right: 15%">{{ i18n.due_date_label}}</strong>
                                    </div>
                                    <div class="col-9">
                                        <span v-if="todoObject.formatted_due_date"
                                                v-bind:class="[is_overdue ? 'overdue' : 'due']">
                                                <i>{{todoObject.formatted_due_date}}</i>
                                        </span>
                                    </div>
                                </div>

                                <div class="row todo-info">
                                    <div class="col-3 text-right todo-info-title">
                                        <strong style="padding-right: 15%">{{ i18n.attachment_label }}</strong>
                                    </div>
                                    <div class="col-9">
                                        <div v-if="todoObject.files.length > 0">
                                            <div v-for="file in todoObject.files">
                                                <files-type-display :file="file" type="normal"></files-type-display>
                                                <!-- <img :src="file.url" alt="" class="image-resize"> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>

                                <div class="row">
                                    <div class="col-12">
                                        <i>{{ i18n.added_by }} <strong>{{todoObject.user_name}}</strong> {{ i18n.on }} {{todoObject.formatted_created}}</i>
                                    </div>
                                </div>
                            </div>

                            <div class="add_form_style" v-if="editTodo">
                                <div class="todo_name inline">
                                    <input type="text"
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
                                <file-upload
                                    :i18n="i18n"
                                    v-on:attach="updateEditAttachments"
                                    v-on:remove="removeEditAttachment"
                                    :attachments="attachmentsToEdit"></file-upload>
                                <br>

                                <div class="inline">
                                    <input style="vertical-align: middle;" type="submit" @click.prevent="updateTodo" name="add_todo" class="button button-primary" :value="i18n.update">
                                    <input style="vertical-align: middle;" type="submit" @click.prevent="cancelTodoEdit" class="button button-default" :value="i18n.cancel">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <comments :i18n="i18n" :comments="todoObject.comments" type="todo"></comments>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</template>

<style>

.todo-details-div {
    padding: 15px 50px 10px;
}
.overdue {
    color: #D54E21;
    font-size: 12px;
    font-style: italic;
    /*background: #D54E21;
    color: white;
    padding: 2px;
    border-radius: 5px;*/
}
.due {
    color: #46B450;
    font-size: 12px;
    font-style: italic;
    
    /*background: #46B450;
    color: white;
    padding: 2px;
    border-radius: 5px;*/
}
.todo-info {
    border-bottom: 1px solid #eee;
    padding-top: 10px;
    padding-bottom: 10px;
}

</style>
<script>
    import store from '../store';
    import DatePicker from './partials/DatePickerComponent.vue';
    import Comments from './partials/CommentsComponent.vue';
    import FilesTypeDisplay from './partials/FilesTypeDisplay.vue';
    import FileUpload from './partials/FileUploadComponent.vue';
    import ProjectNav from './partials/ProjectNavComponent.vue';
    export default {
        components: {
            Comments,
            FileUpload,
            DatePicker,
            FilesTypeDisplay,
            ProjectNav
        },
        data() {
            return {
                i18n: {},
                loading: false,
                todoObject: {},
                is_complete: '',
                is_overdue: '',
                editTodo: false,
                todoName: '',
                selected: '',
                attachmentsToEdit: [],
                attachmentIDsToEdit: [],
                updateDueDate: '',
                list: '',
                project: '',
                users: ''
            }
        },
        filters: {
            truncate: function(string, value) {
                var dotdot = '';
                if(!string)
                    string = '';
                if (string.length > value) {
                    dotdot = '...';
                }
                return string.substring(0, value) + dotdot;
            },
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
                        (!vm.is_complete && (vm.currentUser.data.ID === vm.todoObject.userID));
            }
        },
        methods: {

            updateEditAttachments: function(attachment) {
                var vm = this;
                vm.attachmentsToEdit.push(attachment);
                vm.attachmentIDsToEdit.push(attachment.id);
            },

            removeEditAttachment: function(index) {
                this.attachmentsToEdit.splice(index, 1);
                this.attachmentIDsToEdit.splice(index, 1);
            },

            showTodoEdit: function( todoObject ) {
                var vm = this;
                vm.editTodo = true;
                vm.todoName = vm.todoObject.todo;
                vm.updateDueDate = vm.todoObject.due_date;
                vm.selected = {
                    ID: vm.todoObject.assigneeID, assignee: vm.todoObject.assignee_name
                }
                vm.attachmentsToEdit = todoObject.files;
                vm.attachmentIDsToEdit = todoObject.attachmentIDs;
            },

            cancelTodoEdit: function() {
                this.editTodo = false;
            },

            fetchTodo: function() {
                var vm = this,
                    listID = vm.$route.params.listid,
                    projectID = vm.$route.params.projectid;

                vm.loading = true;

                var data = {
                    action: 'fpm-get-todo-details',
                    project_id: vm.$route.params.projectid,
                    list_id: vm.$route.params.listid,
                    todo_id: vm.$route.params.todoid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    
                    if ( resp.success ) {
                        vm.todoObject = resp.data[0];
                        vm.is_complete = +vm.todoObject.is_complete;
                        vm.is_overdue = vm.todoObject.is_overdue;
                        vm.list = resp.data[0].list_info;
                        vm.project = resp.data[0].project_info;
                    } else {
                        vm.$router.push({
                            path: `/?type=todo&info=notfound`
                        });
                    }
                });
            },

            toggleCheckbox: function( todo ) {
                var vm = this,
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
                            // vm.todoObject.is_complete = todo.is_complete;
                            vm.is_complete = todo.is_complete;
                        } else {
                            vm.message = resp.data;
                        }
                    });
                } else {
                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            // vm.todo.is_complete = todo.is_complete;
                            vm.is_complete = todo.is_complete;
                        } else {
                            vm.message = resp.data;
                        }
                    });
                }
            },

            updateTodo: function() {

                var vm = this,
                    todo,
                    data = {
                        action : 'fpm-insert-todo',
                        nonce : fpm.nonce,
                        todo: vm.todoName,
                        todo_id: vm.todoObject.ID,
                        list_id: vm.$route.params.listid,
                        project_id: vm.$route.params.projectid,
                        user_id: vm.currentUser.data.ID,
                        user_name: vm.currentUser.data.display_name,
                        assignee_id: vm.selected.ID,
                        assignee_name: vm.selected.assignee,
                        attachments: vm.attachmentIDsToEdit,
                        due_date: vm.updateDueDate ? vm.updateDueDate : ''
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    
                    if ( resp.success ) {
                        vm.todoObject.todo = vm.todoName;
                        vm.todoObject.formatted_due_date = resp.data.todo.formatted_due_date;
                        vm.todoObject.due_date = resp.data.todo.due_date;
                        vm.is_overdue = resp.data.todo.is_overdue;
                        vm.todoObject.assigneeID = vm.selected.ID;
                        vm.todoObject.assignee_name = vm.selected.assignee;

                        vm.todoName = '';
                        vm.editTodo = false;
                    } else {
                        vm.message = resp.data;
                    }
                });
            },
            deleteTodo: function( todo ) {

                if (confirm("Are you sure ??")) {
                    var vm = this,
                        todoInfo,
                        listID = +todo.listID,
                        projectID = +todo.projectID,
                        data = {
                            action : 'fpm-delete-todo',
                            nonce : fpm.nonce,
                            todo_id: todo.ID,
                            todo: todo.todo,
                            project_id: projectID
                        };

                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {

                            vm.$router.push({
                                path: `/projects/${projectID}/todolists/${listID}`
                            });

                        } else {

                        }
                    });
                }
            }
        },

        created() {
            this.fetchTodo();
            var vm = this,
                projectid,
                key;

            store.setLocalization( 'fpm-get-single-todo-local-data' ).then( function( data ) {
                vm.i18n = data;
            });

            vm.currentUser = fpm.currentUserInfo;

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
        },
    }
</script>