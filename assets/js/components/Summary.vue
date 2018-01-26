<template>
    <div>
        <div class="container summary-section">
            <div class="row">
                <div class="col-12">
                    <div class="text-center project-info">
                        <h1><strong>{{project.project_title}}</strong></h1>
                        <br>
                        <span>{{project.project_desc}}</span>

                        <!-- <router-link :to="'/projects/' + $route.params.projectid + '/edit'" class="link-style edit" tag="span" v-if="isShowEdit">
                            <a>Edit info</a>
                        </router-link> -->
                        <span class="dropdown project-settings show-edit" v-if="isShowEdit">
                            <a data-target="#" class="setting-icon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Settings">
                                <i class="fa fa-gear" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <router-link :to="'/projects/' + $route.params.projectid + '/edit'" 
                                    class="link-style" 
                                    tag="li">
                                    <a><i class="fa fa-edit p-r-10" aria-hidden="true"></i>Edit info</a>
                                </router-link>
                                <li role="separator" class="divider"></li>
                                <router-link :to="'/projects/' + $route.params.projectid + '/status'" 
                                    class="link-style" 
                                    tag="li">
                                    <a><i class="fa fa-trash p-r-10" aria-hidden="true"></i>Delete</a>
                                </router-link>
                            </ul>
                        </span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="users-summary">
                        <div style="display: inline-block">
                            <img :src="user.avatar_url" v-for="user in users" alt="" class="small-round-image">
                        </div>
                        <div v-if="project.user_count > 10" style="display: inline-block;position: absolute;padding-top: 15px;padding-left:5px;">
                            <a>+{{project.user_count - 10}} people</a>
                        </div>

                        <div style="margin-top: 15px;">
                            <router-link :to="'/projects/' + $route.params.projectid + '/users'" class="link-style button button-default">
                                Add/Remove People...
                            </router-link>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-2"></div>
                <div class="col-4">
                    <div class="summary-card">
                        <router-link :to="'/projects/' + $route.params.projectid + '/todolists'" tag="h3" class="link-style">
                            <a>To-dos</a>
                        </router-link>
                        <hr>
                        <div style="position: absolute;" class="text-left">
                            <div v-for="list in listSummary">
                                <h3>{{list.list_title}}</h3>
                                <ul>
                                    <li v-for="todo in list.todos">
                                        <span class="checkbox-style"></span>{{todo.todo}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="summary-card">
                        <router-link :to="'/projects/' + $route.params.projectid + '/messages'" tag="h3" class="link-style">
                            <a>Message Board</a>
                        </router-link>
                        <hr>
                        <div style="position: absolute;" class="text-left">
                            <div v-for="messageObj in messages">
                                <div>
                                    <img class="small-round-image" :src="messageObj.avatar_url" alt="" width="20" height="20" style="float:left;margin-right:10px;">
                                    <strong>{{messageObj.message_title}}</strong>
                                </div><br>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-4">
                    <div class="summary-card">
                        <router-link :to="'/projects/' + $route.params.projectid + '/files'" tag="h3" class="link-style">
                            <a>Docs & Files</a>
                        </router-link>
                        <hr>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet architecto cupiditate consequuntur placeat atque neque. Voluptates odio in omnis, rem laboriosam magni eos corporis, error voluptatibus ut tempora, ullam adipisci!</p>
                    </div>
                </div> -->
            </div>
            <activities></activities>  
        </div>
    </div>
</template>

<script>
    import store from '../store';
    import Activities from './Activities.vue';
    export default {
        components: {
            Activities
        },
        data() {
            return {
                listSummary : [],
                messages: [],
                users: [],
                project: ''
            }
        },
        computed: {
            isShowEdit: function() {
                var vm = this;
                return (vm.currentUser.roles[0] === 'administrator') ||
                        (vm.currentUser.data.ID === vm.project.userID);
            }
        },
        methods: {
            fetchTodoSummary() {
                var vm = this;
                vm.loading = true;

                var data = {
                    action: 'fpm-get-lists',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                    limit: 3
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    // console.log(resp);
                    if ( resp.success ) {
                        vm.listSummary = resp.data;
                    }
                });
            },

            fetchMessageSummary() {
                var vm = this;
                vm.loading = true;

                var data = {
                    action: 'fpm-get-messages',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                    limit: 5
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    if ( resp.success ) {
                        vm.messages = resp.data;
                    }
                });
            },

            fetchProject() {
                var vm = this;
                vm.loading = true;

                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                    }
                });
            }
        },

        created() {
            var vm = this,
                projectid,
                prevID,
                prevKey;
            vm.fetchProject();
            vm.fetchTodoSummary();
            vm.fetchMessageSummary();
            vm.currentUser = fpm.currentUserInfo;

            prevID = localStorage.getItem('pid');
            projectid = vm.$route.params.projectid;

            if ( prevID !== projectid ) {
                console.log('prevID !== projectid')
                prevKey = prevID + '-users';
                localStorage.removeItem(prevKey);
                localStorage.setItem('pid', projectid);

                store.fetchUsers( projectid ).then(function(resp){
                    vm.users = resp.data;
                    var key = projectid + '-users';
                    localStorage.setItem(key, JSON.stringify(vm.users));
                });
            } else {
                var key = projectid + '-users';
                vm.users = JSON.parse(localStorage.getItem(key));
                console.log('prevID === projectid')
                if (!vm.users) {
                    console.log('!vm.users');
                    store.fetchUsers( projectid ).then(function(resp){
                        vm.users = resp.data;
                        localStorage.setItem(key, JSON.stringify(vm.users));
                    });
                }
            }
        },

        mounted() {
            console.log('Summary Component mounted.')
        }
    }
</script>

<style>
    .p-r-10 {
        padding-right: 10px;
    }
    .p-l-10 {
        padding-left: 10px;
    }
    .checkbox-style {
        padding: 0px 8px;
        margin-right: 10px;
        margin-left: 10px;
        border: 1px solid #ccc;
    }
    .link-style a:link {
        text-decoration: none;
        cursor: pointer;
    }

    .link-style a:visited {
        text-decoration: none;
        cursor: pointer;
    }

    .link-style a:hover {
        text-decoration: underline;
        cursor: pointer;
    }

    .link-style a:active {
        text-decoration: underline;
        cursor: pointer;
    }
    .small-round-image {
        border-radius: 40%;
    }
    /*.link-style {
        cursor: pointer;
    }*/
    .text-center {
        text-align: center;
    }
    .text-left {
        text-align: left;
    }
    .text-right {
        text-align: right;
    }

    .summary-section {
        background: #ffffff;
        padding-bottom: 40px;
        border-radius: 5px;
    }

    .summary-card {
        /*float: left;
        width: 27%;*/
        /*margin-right: 10px; */
        /*margin-top: 20px;*/
        padding: 0.7em 2em 1em;
        border-radius: 5px;
        text-align: center;
        position: relative;
        border: 1px solid #e5e5e5;
        -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        background: #fff;
        /*display: block;*/
        height: 200px;
        overflow: hidden;
    }

    .users-summary {
        padding: 0.7em 2em 1em;
        border-radius: 3px;
        text-align: center;
        /*-webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.04);*/
        /*box-shadow: 0 1px 1px rgba(0,0,0,0.04);*/
        /*background: #fff;*/
        height: auto;
    }
    .project-info {
        position: relative;
        padding: 30px 40px 10px;
    }
    .project-info:hover .show-edit {
        /*display: block;*/
    }

    .show-edit {
        padding-top: 7px;
        padding-right: 7px;
        position: absolute;
        right: 0;
        top: 0;
        cursor: pointer;
        /*display: none;*/
    }
</style>