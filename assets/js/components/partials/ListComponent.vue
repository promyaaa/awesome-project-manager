<template>
    <div>
        <li>
            <div v-if="isSingle">
                <div v-if="!isListEdit" style="margin-top: -10px">
                    <button class="button button-default" 
                            @click="showListEditForm( list )">Edit</button>
                    <span style="float:right" @click="deleteList(list)">
                        <a style="color: #d54e21;cursor:pointer;">Delete</a>
                    </span>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h3 v-if="!isListEdit" style="padding-left: 4%;padding-top:2%">{{list.list_title}}</h3>
                        <div v-if="isListEdit" class="add_form_style">
                            <div>
                                <input type="text" 
                                    name="list_title" 
                                    v-model="listTitle" 
                                    class="form-control" 
                                    placeholder="Name this list . . ." 
                                    @keyup.enter="updateList(list)" 
                                    @keyup.esc="cancelListEdit"
                                    v-focus>
                            </div>
                            <div class="action">
                                <button class="button button-primary" 
                                        @click.prevent="updateList"
                                        >Update</button>
                                <button class="button button-default" @click="cancelListEdit">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else style="padding-left: 4%">
                <router-link :to="'/projects/' + $route.params.projectid + '/todolists/' + list.ID" tag="h3" class="link-style">
                    <a>{{list.list_title}}</a>
                </router-link>
            </div>
            
            <ul v-if="list.todos.length > 0">
                <todo-item v-for="(todo, tindex) in list.todos" :todo="todo" :tindex="tindex" :list="list" :key="todo.ID"></todo-item>
            </ul>
            <add-todo :sindex="sindex" :list="list"></add-todo>
            <br>
            <div class="row" v-if="isSingle">
                <div class="col-12">
                    <span style="padding-left:4%;"><i>Started by <strong>Rokib</strong> on {{list.formatted_created}}</i></span>
                </div>
            </div>
        </li>
    </div>
    
</template>

<style>
    .list-li {
        padding: 20px 30px 5px;
    }
</style>

<script>
    import TodoItem from './TodoItemComponent.vue';
    import AddTodo from './AddTodoComponent.vue';
    export default {
        components: {
            'todo-item': TodoItem,
            'add-todo': AddTodo
        },
        props: ['isSingle', 'list', 'sindex', 'users'],
        data() {
            return {
                isListEdit: false,
                listTitle: '',
                listCloneObj: '',
                currentUser: ''
            }
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },
        methods: {
            showListEditForm( listObj ) {
                var vm = this;

                vm.isListEdit = true;
                vm.listCloneObj = JSON.parse(JSON.stringify(listObj));
                vm.listTitle = listObj.list_title;
            },
            cancelListEdit() {
                var vm = this;

                vm.isListEdit = false;
                vm.list.list_title = this.listCloneObj.list_title;
            },
            updateList() {
                var vm = this,
                list,
                data = {
                    action : 'fpm-create-list',
                    nonce : fpm.nonce,
                    title: vm.listTitle,
                    project_id: vm.$route.params.projectid,
                    list_id: vm.$route.params.listid,
                    user_name: vm.currentUser.data.display_name
                };
                
                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.list.list_title = vm.listTitle;
                        vm.listTitle = '';
                        vm.isListEdit = false;
                    } else {
                        // vm.message = resp.data;
                    }
                });
            },
            deleteList( listObj ) {
                if (confirm("Are you sure ??")) {
                    var vm = this,
                        projectID = +listObj.projectID,
                        data = {
                            action : 'fpm-delete-list',
                            nonce : fpm.nonce,
                            list_id: listObj.ID
                        };
                        
                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            
                            vm.$router.push({ 
                                path: `/projects/${projectID}/todolists` 
                            });

                        } else {
                            
                        }
                    });
                }
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {
            this.currentUser = fpm.currentUserInfo;
        }
    }
</script>