<template>
    <div>
        <div class="container summary-section">
            <div class="row">
                <div class="col-12">
                    <div class="text-center project-info">
                        <h1><strong>{{project.project_title}}</strong></h1>
                        <br>
                        <span>{{project.project_desc}}</span>

                        <span class="dropdown project-settings show-edit" v-if="isShowEdit">
                            <a data-target="#" class="setting-icon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Settings">
                                <i class="fa fa-gear" aria-hidden="true" style="font-size:15px;"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <router-link :to="'/projects/' + $route.params.projectid + '/edit'"
                                    class="link-style"
                                    tag="li">
                                    <a><i class="fa fa-edit p-r-10" aria-hidden="true"></i>{{ i18n.edit_info }}</a>
                                </router-link>
                                <li role="separator" class="divider"></li>
                                <router-link :to="'/projects/' + $route.params.projectid + '/status'"
                                    class="link-style"
                                    tag="li">
                                    <a><i class="fa fa-trash p-r-10" aria-hidden="true"></i>{{ i18n.delete }}</a>
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
                            <a>+{{project.user_count - 10}} {{ i18n.people }}</a>
                        </div>

                        <div style="margin-top: 15px;">
                            <router-link :to="'/projects/' + $route.params.projectid + '/users'" class="link-style button button-default">
                                {{ i18n.add_remove_people }}
                            </router-link>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5">
                    <div class="summary-card">
                        <router-link :to="'/projects/' + $route.params.projectid + '/todolists'" tag="h3" class="link-style">
                            <a>{{ i18n.todos }}</a>
                        </router-link>
                        <hr>
                        <div style="" class="text-left">
                            <div v-for="list in listSummary">
                                <h4>{{list.list_title}}</h4>
                                <ul>
                                    <li v-for="todo in list.todos">
                                        <span class="checkbox-style ellipsis-90"></span>{{todo.todo}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <span class="preview-fade"></span>
                    </div>
                </div>
                <div class="col-5">
                    <div class="summary-card">
                        <router-link :to="'/projects/' + $route.params.projectid + '/messages'" tag="h3" class="link-style">
                            <a>{{ i18n.message_board }}</a>
                        </router-link>
                        <hr>
                        <div style="position: absolute;margin-top:10px;" class="text-left">
                            <div v-for="messageObj in messages" class="messages">
                                <div class="message-list">
                                    <img class="small-round-image" :src="messageObj.avatar_url" alt="" width="20" height="20" style="float:left;margin-right:10px; width:7%;">
                                    <div style="float:left; width:82%">{{messageObj.message_title}}</div>
                                </div>
                            </div>
                        </div>
                        <span class="preview-fade"></span>
                    </div>
                </div>
            </div>
            <activities :i18n="i18n"></activities>
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
                i18n: {},
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
                // vm.loading = true;

                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // vm.loading = false;
                    // console.log(resp);
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                    } else {
                        vm.$router.push({
                            path: `/?type=project&info=notfound`
                        });
                    }
                });
            }
        },

        created() {
            var vm = this,
                projectid,
                prevID,
                prevKey;

            var self = this;
            store.setLocalization( 'fpm-get-summary-local-data' ).then( function( data ) {
                self.i18n = data;
            });

            vm.fetchProject();
            vm.fetchTodoSummary();
            vm.fetchMessageSummary();
            vm.currentUser = fpm.currentUserInfo;

            prevID = localStorage.getItem('pid');
            projectid = vm.$route.params.projectid;

            if ( prevID !== projectid ) {
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
                if (!vm.users) {
                    store.fetchUsers( projectid ).then(function(resp){
                        vm.users = resp.data;
                        localStorage.setItem(key, JSON.stringify(vm.users));
                    });
                }
            }
        },

        mounted() {

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
        padding: 0px 9px;
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
        position: relative;
        padding: 10px 25px;
        border-radius: 5px;
        text-align: center;
        position: relative;
        border: 1px solid #e5e5e5;
        -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        background: #fff;
        height: 200px;
        overflow: hidden;
    }

    .summary-card ul li{
        margin-bottom: 10px;
    }

    .summary-card h3,
    .summary-card h4 {
        margin: 15px 0px;
        padding: 0px;
    }

    .users-summary {
        padding: 0.7em 2em 1em;
        border-radius: 3px;
        text-align: center;
        height: auto;
    }
    .project-info {
        position: relative;
        padding: 30px 40px 10px;
    }

    .messages .message-list {
        overflow: hidden;
        margin-bottom: 10px;
    }

    .show-edit {
        padding-top: 7px;
        padding-right: 7px;
        position: absolute;
        right: 0;
        top: 0;
        cursor: pointer;
    }
</style>