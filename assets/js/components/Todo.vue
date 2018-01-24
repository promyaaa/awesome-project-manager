<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <router-link :to="'/projects/' + $route.params.projectid" tag="span" class="link-style">
                        <a>Project</a> >
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/todolists/' + $route.params.listid" tag="span" class="link-style">
                        <a>List</a>
                    </router-link>
                </div>
            </div>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10 box">
                    
                    <div class="loading" v-if="loading">
                        <p>Loading . . .</p>
                    </div>
                    <!-- <pre>
                        {{todoObject}}
                    </pre> -->
                    <div v-if="todoObject && !loading" class="single-todo">
                        <div>
                            <div v-if="isShowEdit">
                                <button class="button button-default" 
                                        @click="showTodoEdit(todoObject)">Edit</button>
                                <span style="float:right" @click="deleteTodo(todoObject)">
                                    <a style="color: #d54e21;cursor:pointer;">Delete</a>
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
                                    <strong style="padding-right: 15%">Assigned To :</strong>
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
                                    <strong style="padding-right: 15%">Due Date :</strong>
                                </div>
                                <div class="col-9">
                                    Due date er kaj korte hbe
                                </div>
                            </div>

                            <div class="row todo-info">
                                <div class="col-3 text-right todo-info-title">
                                    <strong style="padding-right: 15%">Attachments :</strong>
                                </div>
                                <div class="col-9">
                                    <div v-if="todoObject.files.length > 0">
                                        <div v-for="file in todoObject.files" class="image-common">
                                            <img :src="file.url" alt="" class="image-resize">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                    
                            <div class="row">
                                <div class="col-12">
                                    <i>Added by <strong>{{todoObject.user_name}}</strong> on {{todoObject.formatted_created}}</i>
                                </div>
                            </div>
                        </div>

                        <div class="add_form_style" v-if="editTodo">
                        
                            <div class="todo_name inline">
                                <input type="text"
                                    v-model="todoName" 
                                    class="form-control" 
                                    placeholder="add todo . . ." 
                                    v-focus
                                    @keyup.esc="hideTodoForm">
                                <span class="form-note"><i>*required field</i></span>
                            </div>
                            <div>
                                <select v-model="selected" class="form-control">
                                    <option disabled value="">select user</option>
                                    <option v-for="option in users" v-bind:value="{ID : option.ID, assignee : option.display_name}">
                                    {{ option.display_name }}
                                    </option>
                                </select>
                            </div>
                            <br>
                            <file-upload 
                                v-on:attach="updateEditAttachments" 
                                v-on:remove="removeEditAttachment" 
                                :attachments="attachmentsToEdit"></file-upload>
                            <br> 
                            
                            <div class="inline">
                                <input style="vertical-align: middle;" type="submit" @click.prevent="updateTodo" name="add_todo" class="button button-primary" value="Update Todo">
                                <input style="vertical-align: middle;" type="submit" @click.prevent="cancelTodoEdit" class="button button-default" value="Cancel">
                            </div>
                        
                    </div>
                        
                    </div>
                    
                </div>
            </div>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <comments :comments="todoObject.comments" type="todo"></comments> 
                </div>
            </div>
        </div>
    </div>
</template>

<style>

.todo-details-div {
    padding: 15px 50px 10px;
}

.todo-info {
    border-bottom: 1px solid #eee;
    padding-top: 10px;
    padding-bottom: 10px;
}
    
</style>
<script>
    import Comments from './partials/CommentsComponent.vue';
    import FileUpload from './partials/FileUploadComponent.vue';
    export default {
        components: {
            'comments': Comments,
            FileUpload
        },
        data() {
            return {
                loading: false,
                todoObject: {},
                is_complete: '',
                editTodo: false,
                todoName: '',
                selected: '',
                attachmentsToEdit: [],
                attachmentIDsToEdit: [],
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
                    console.log(resp);
                    if ( resp.success ) {
                        vm.todoObject = resp.data[0];
                        vm.is_complete = +vm.todoObject.is_complete
                    } else {
                        vm.$router.push({ 
                            path: `/projects/${projectID}/todolists/${listID}?type=todo&info=notfound` 
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
                        attachments: vm.attachmentIDsToEdit
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.todoObject.todo = vm.todoName;
                        vm.todoObject.assigneeID = vm.selected.ID;
                        vm.todoObject.assignee_name = vm.selected.assignee;
                        
                        vm.editTodo = false;
                        vm.todoName = '';
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

        mounted() {
            console.log('Component mounted.')
        }
    }
</script>